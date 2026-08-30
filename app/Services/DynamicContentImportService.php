<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionBankItem;
use App\Models\QuestionOption;
use App\Models\TaxonomyTrack;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class DynamicContentImportService
{
    /**
     * Parse an uploaded file into a normalized array of row associative arrays
     */
    public function parseFile(mixed $file, ?string $forcedExtension = null): array
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $filePath = '';
        $content = '';
        $extension = $forcedExtension;

        if ($file instanceof UploadedFile) {
            $filePath = $file->getRealPath() ?: $file->path();
            if (!$extension) {
                $origName = $file->getClientOriginalName();
                $extension = pathinfo($origName, PATHINFO_EXTENSION) ?: $file->getClientOriginalExtension();
            }

            try {
                if (method_exists($file, 'get') && ($c = $file->get())) {
                    $content = $c;
                } elseif ($filePath && file_exists($filePath)) {
                    $content = file_get_contents($filePath);
                } else {
                    $content = $file->getContent();
                }
            } catch (\Throwable $e) {
                $content = $filePath && file_exists($filePath) ? file_get_contents($filePath) : '';
            }
        } elseif (is_string($file)) {
            $filePath = $file;
            if (!$extension) {
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            }
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
            } else {
                $content = $file;
            }
        }

        $extension = strtolower((string) $extension);

        if ($extension === 'json') {
            return $this->parseJsonContent((string) $content);
        }

        if (in_array($extension, ['xlsx', 'xls']) && $filePath && file_exists($filePath)) {
            return $this->parseExcel($filePath);
        }

        return $this->parseCsvContent((string) $content);
    }

    /**
     * Parse CSV / Delimited text content with auto-delimiter and encoding detection
     */
    public function parseCsvContent(string $content): array
    {
        if (empty(trim($content))) {
            return [];
        }

        // Remove UTF-8 BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        // Detect delimiter
        $firstLine = strtok($content, "\r\n") ?: '';
        $delimiters = [',', ';', "\t", '|'];
        $chosenDelimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $delim) {
            $count = substr_count($firstLine, $delim);
            if ($count > $maxCount) {
                $maxCount = $count;
                $chosenDelimiter = $delim;
            }
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return [];
        }

        $headerLine = array_shift($lines);
        $rawHeaders = str_getcsv($headerLine, $chosenDelimiter);
        $headers = array_map(fn($h) => $this->normalizeHeaderKey(trim((string)$h)), $rawHeaders);

        $rows = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }
            $data = str_getcsv($line, $chosenDelimiter);
            if (count($data) < 2) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                if ($header !== '') {
                    $row[$header] = isset($data[$index]) ? trim((string)$data[$index]) : '';
                }
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Parse JSON content
     */
    public function parseJsonContent(string $content): array
    {
        if (empty(trim($content))) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        // Support structured series grouping: {"series": [ {"series_identifier": "...", "lessons": [...]}, ... ]}
        if (isset($decoded['series']) && is_array($decoded['series'])) {
            $isListOfSeries = false;
            foreach ($decoded['series'] as $sCheck) {
                if (is_array($sCheck) && (isset($sCheck['lessons']) || isset($sCheck['micro_lessons']) || isset($sCheck['items']))) {
                    $isListOfSeries = true;
                    break;
                }
            }

            if ($isListOfSeries) {
                $flattened = [];
                foreach ($decoded['series'] as $seriesObj) {
                    if (!is_array($seriesObj)) {
                        continue;
                    }
                    $sId = $seriesObj['series_identifier'] ?? $seriesObj['series_id'] ?? $seriesObj['series_code'] ?? $seriesObj['series_slug'] ?? $seriesObj['slug'] ?? null;
                    $sTitle = $seriesObj['series_title'] ?? $seriesObj['series_name'] ?? $seriesObj['title'] ?? $seriesObj['name'] ?? null;
                    $sProg = isset($seriesObj['is_progressive']) ? $seriesObj['is_progressive'] : (isset($seriesObj['progressive']) ? $seriesObj['progressive'] : true);
                    $sTrack = $seriesObj['track'] ?? $seriesObj['category'] ?? null;

                    $seriesLessons = $seriesObj['lessons'] ?? $seriesObj['micro_lessons'] ?? $seriesObj['items'] ?? [];
                    if (is_array($seriesLessons)) {
                        foreach ($seriesLessons as $idx => $lesson) {
                            if (!is_array($lesson)) {
                                continue;
                            }
                            if ($sId && !isset($lesson['series_identifier']) && !isset($lesson['series_id'])) {
                                $lesson['series_identifier'] = $sId;
                            }
                            if ($sTitle && !isset($lesson['series_title']) && !isset($lesson['series_name'])) {
                                $lesson['series_title'] = $sTitle;
                            }
                            if (!isset($lesson['is_progressive']) && !isset($lesson['progressive'])) {
                                $lesson['is_progressive'] = $sProg;
                            }
                            if (!isset($lesson['series_order']) && !isset($lesson['series_part']) && !isset($lesson['part'])) {
                                $lesson['series_order'] = $idx + 1;
                            }
                            if ($sTrack && !isset($lesson['track']) && !isset($lesson['category'])) {
                                $lesson['track'] = $sTrack;
                            }
                            $flattened[] = $lesson;
                        }
                    }
                }
                $items = $flattened;
            } else {
                $items = $decoded['series'];
            }
        } elseif (isset($decoded['questions']) && is_array($decoded['questions'])) {
            $items = $decoded['questions'];
        } elseif (isset($decoded['lessons']) && is_array($decoded['lessons'])) {
            $topSeriesId = $decoded['series_identifier'] ?? $decoded['series_id'] ?? $decoded['series_code'] ?? $decoded['series_slug'] ?? null;
            $topSeriesTitle = $decoded['series_title'] ?? $decoded['series_name'] ?? null;
            $topIsProgressive = $decoded['is_progressive'] ?? $decoded['progressive'] ?? null;
            $topTrack = $decoded['track'] ?? $decoded['category'] ?? null;

            $items = [];
            foreach ($decoded['lessons'] as $idx => $lesson) {
                if (is_array($lesson)) {
                    if ($topSeriesId && !isset($lesson['series_identifier']) && !isset($lesson['series_id'])) {
                        $lesson['series_identifier'] = $topSeriesId;
                    }
                    if ($topSeriesTitle && !isset($lesson['series_title']) && !isset($lesson['series_name'])) {
                        $lesson['series_title'] = $topSeriesTitle;
                    }
                    if ($topIsProgressive !== null && !isset($lesson['is_progressive']) && !isset($lesson['progressive'])) {
                        $lesson['is_progressive'] = $topIsProgressive;
                    }
                    if ($topSeriesId && !isset($lesson['series_order']) && !isset($lesson['series_part']) && !isset($lesson['part'])) {
                        $lesson['series_order'] = $idx + 1;
                    }
                    if ($topTrack && !isset($lesson['track']) && !isset($lesson['category'])) {
                        $lesson['track'] = $topTrack;
                    }
                    $items[] = $lesson;
                }
            }
        } elseif (isset($decoded['micro_lessons']) && is_array($decoded['micro_lessons'])) {
            $items = $decoded['micro_lessons'];
        } elseif (isset($decoded['items']) && is_array($decoded['items'])) {
            $items = $decoded['items'];
        } elseif (isset($decoded['data']) && is_array($decoded['data'])) {
            $items = $decoded['data'];
        } else {
            $items = $decoded;
        }

        if (!is_array($items)) {
            return [];
        }

        $normalized = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $row = [];
            foreach ($item as $key => $value) {
                // If 'series' key is provided as an associative array: {"series": {"identifier": "...", "title": "...", "order": 1, "is_progressive": true}}
                if (in_array(strtolower((string)$key), ['series', 'series_info', 'series_metadata']) && is_array($value)) {
                    if (isset($value['identifier']) || isset($value['id']) || isset($value['slug']) || isset($value['code'])) {
                        $row['series_identifier'] = (string) ($value['identifier'] ?? $value['id'] ?? $value['slug'] ?? $value['code']);
                    }
                    if (isset($value['title']) || isset($value['name'])) {
                        $row['series_title'] = (string) ($value['title'] ?? $value['name']);
                    }
                    if (isset($value['order']) || isset($value['part']) || isset($value['sequence'])) {
                        $row['series_order'] = $value['order'] ?? $value['part'] ?? $value['sequence'];
                    }
                    if (isset($value['is_progressive']) || isset($value['progressive'])) {
                        $row['is_progressive'] = $value['is_progressive'] ?? $value['progressive'];
                    }
                    continue;
                }

                $row[$this->normalizeHeaderKey((string)$key)] = is_array($value) ? $value : trim((string)$value);
            }
            $normalized[] = $row;
        }

        return $normalized;
    }

    /**
     * Parse Excel (.xlsx) file using OpenSpout
     */
    public function parseExcel(string $filePath): array
    {
        try {
            $reader = new XlsxReader();
            $reader->open($filePath);

            $rows = [];
            $headers = [];

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                    $cells = $row->toArray();
                    if ($rowIndex === 1) {
                        $headers = array_map(fn($h) => $this->normalizeHeaderKey(trim((string)$h)), $cells);
                        continue;
                    }

                    if (empty(array_filter($cells, fn($c) => !empty(trim((string)$c))))) {
                        continue;
                    }

                    $rowData = [];
                    foreach ($headers as $colIndex => $header) {
                        if ($header !== '') {
                            $rowData[$header] = isset($cells[$colIndex]) ? trim((string)$cells[$colIndex]) : '';
                        }
                    }
                    $rows[] = $rowData;
                }
                break; // Process first sheet
            }

            $reader->close();
            return $rows;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Normalize header names to standardized keys
     */
    protected function normalizeHeaderKey(string $key): string
    {
        $clean = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', trim($key)));
        $clean = trim($clean, '_');

        return match ($clean) {
            'question', 'question_title', 'text', 'prompt' => 'question_text',
            'a', 'choice_a', 'option1', 'choice_1' => 'option_a',
            'b', 'choice_b', 'option2', 'choice_2' => 'option_b',
            'c', 'choice_c', 'option3', 'choice_3' => 'option_c',
            'd', 'choice_d', 'option4', 'choice_4' => 'option_d',
            'correct', 'answer', 'correct_key', 'answer_key' => 'correct_option',
            'rationale', 'notes' => 'explanation',
            'scripture', 'bible', 'scripture_ref', 'bible_verse' => 'scripture_citations',
            'ccc', 'catechism', 'catechism_ref' => 'catechism_citations',
            'citation', 'reference', 'ref' => 'reference_citation',
            'diff', 'level' => 'difficulty',
            'track', 'track_name', 'category', 'category_name', 'topic' => 'track',
            'series_id', 'series_code', 'series_slug', 'series_tag', 'series_key', 'seriesid', 'seriesidentifier', 'series_uuid', 'series_ref' => 'series_identifier',
            'series_name', 'series_heading', 'series_label', 'seriestitle', 'seriesname' => 'series_title',
            'series_part', 'part', 'series_step', 'part_number', 'lesson_order', 'series_sequence', 'sequence', 'series_index', 'seriesorder', 'seriespart', 'part_no' => 'series_order',
            'progressive', 'is_sequential', 'sequential', 'lock_sequence', 'prerequisite_required', 'has_prerequisite', 'isprogressive', 'issequential' => 'is_progressive',
            default => $clean,
        };
    }

    /**
     * Import a batch of question rows into the database
     */
    public function importQuestions(
        array $rows,
        ?int $fallbackTrackId = null,
        string $duplicateStrategy = 'skip', // 'skip', 'overwrite'
        ?User $uploader = null
    ): array {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $duplicateService = app(DuplicateDetectionService::class);
        $tracks = TaxonomyTrack::all()->keyBy('name');
        $categories = Category::all()->keyBy('name');

        $totalProcessed = 0;
        $successful = 0;
        $duplicatesSkipped = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $totalProcessed++;
            $rowNum = $index + 1;

            // Extract question text
            $questionText = $row['question_text'] ?? null;
            if (empty($questionText)) {
                $failed++;
                $errors[] = "Row #{$rowNum}: Missing question text.";
                continue;
            }

            // Determine Options
            $optionA = $row['option_a'] ?? ($row['options'][0]['option_text'] ?? ($row['options']['A'] ?? ''));
            $optionB = $row['option_b'] ?? ($row['options'][1]['option_text'] ?? ($row['options']['B'] ?? ''));
            $optionC = $row['option_c'] ?? ($row['options'][2]['option_text'] ?? ($row['options']['C'] ?? ''));
            $optionD = $row['option_d'] ?? ($row['options'][3]['option_text'] ?? ($row['options']['D'] ?? ''));

            if (empty($optionA) || empty($optionB)) {
                $failed++;
                $errors[] = "Row #{$rowNum}: At least Option A and Option B must be provided for '{$questionText}'.";
                continue;
            }

            // Determine correct option
            $correctOption = strtoupper(trim((string)($row['correct_option'] ?? 'A')));
            if (!in_array($correctOption, ['A', 'B', 'C', 'D'])) {
                if (strcasecmp($correctOption, $optionA) === 0) $correctOption = 'A';
                elseif (strcasecmp($correctOption, $optionB) === 0) $correctOption = 'B';
                elseif (strcasecmp($correctOption, $optionC) === 0) $correctOption = 'C';
                elseif (strcasecmp($correctOption, $optionD) === 0) $correctOption = 'D';
                else $correctOption = 'A';
            }

            // Determine Track / Category ID
            $trackId = $fallbackTrackId;
            $categoryId = null;

            if (!empty($row['track_id']) && is_numeric($row['track_id'])) {
                $trackId = (int) $row['track_id'];
            } elseif (!empty($row['track'])) {
                $trackName = trim((string)$row['track']);
                $foundTrack = $tracks->first(fn($t) => strcasecmp($t->name, $trackName) === 0);
                if ($foundTrack) {
                    $trackId = $foundTrack->id;
                }
            }

            if (!empty($row['category_id']) && is_numeric($row['category_id'])) {
                $categoryId = (int) $row['category_id'];
            } elseif (!empty($row['category'])) {
                $catName = trim((string)$row['category']);
                $foundCat = $categories->first(fn($c) => strcasecmp($c->name, $catName) === 0);
                if ($foundCat) {
                    $categoryId = $foundCat->id;
                }
            }

            // Fallback track & category resolution
            if (!$trackId) {
                $firstTrack = TaxonomyTrack::first();
                $trackId = $firstTrack?->id ?? 1;
            }

            if (!$categoryId) {
                $firstCategory = Category::first();
                $categoryId = $firstCategory?->id ?? 1;
            }

            // Difficulty
            $rawDiff = strtoupper(trim((string)($row['difficulty'] ?? 'MEDIUM')));
            $difficultyInt = match ($rawDiff) {
                '1', 'EASY' => 1,
                '3', 'HARD' => 3,
                '4', 'EXPERT' => 4,
                default => 2, // MEDIUM
            };
            $difficultyEnum = match ($difficultyInt) {
                1 => 'EASY',
                3 => 'HARD',
                4 => 'EXPERT',
                default => 'MEDIUM',
            };

            $explanation = $row['explanation'] ?? null;
            $referenceCitation = $row['reference_citation'] ?? $row['catechism_citations'] ?? $row['scripture_citations'] ?? null;

            // Duplicate detection via similarity hash
            $simHash = $duplicateService->generateSimilarityHash($questionText);
            $existingBankItem = QuestionBankItem::where('duplicate_similarity_hash', $simHash)->first();

            if ($existingBankItem) {
                if ($duplicateStrategy === 'skip') {
                    $duplicatesSkipped++;
                    continue;
                }
            }

            // Database Insertion in Transaction
            try {
                DB::transaction(function () use (
                    $questionText,
                    $trackId,
                    $categoryId,
                    $optionA,
                    $optionB,
                    $optionC,
                    $optionD,
                    $correctOption,
                    $explanation,
                    $referenceCitation,
                    $difficultyInt,
                    $difficultyEnum,
                    $uploader
                ) {
                    $optionsList = [
                        ['option_key' => 'A', 'option_text' => $optionA, 'is_correct' => $correctOption === 'A'],
                        ['option_key' => 'B', 'option_text' => $optionB, 'is_correct' => $correctOption === 'B'],
                    ];
                    if (!empty($optionC)) {
                        $optionsList[] = ['option_key' => 'C', 'option_text' => $optionC, 'is_correct' => $correctOption === 'C'];
                    }
                    if (!empty($optionD)) {
                        $optionsList[] = ['option_key' => 'D', 'option_text' => $optionD, 'is_correct' => $correctOption === 'D'];
                    }

                    // 1. Universal Question Bank Item via QuestionBankService
                    app(QuestionBankService::class)->createQuestion([
                        'track_id' => $trackId,
                        'question_type' => 'MULTIPLE_CHOICE',
                        'question_text' => $questionText,
                        'explanation' => $explanation,
                        'reference_citation' => $referenceCitation,
                        'editorial_difficulty' => $difficultyEnum,
                        'status' => 'PUBLISHED',
                    ], $optionsList, $uploader);

                    // 2. Mobile Arena Question Model
                    $optionsMap = [
                        'A' => $optionA,
                        'B' => $optionB,
                    ];
                    if (!empty($optionC)) $optionsMap['C'] = $optionC;
                    if (!empty($optionD)) $optionsMap['D'] = $optionD;

                    Question::create([
                        'category_id' => $categoryId,
                        'level' => $difficultyInt,
                        'question_text' => $questionText,
                        'options' => $optionsMap,
                        'correct_option_key' => $correctOption,
                        'explanation' => $explanation,
                        'reference_citation' => $referenceCitation,
                        'is_active' => true,
                    ]);
                });

                $successful++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "Row #{$rowNum}: {$e->getMessage()}";
            }
        }

        return [
            'total_processed' => $totalProcessed,
            'successful' => $successful,
            'duplicates_skipped' => $duplicatesSkipped,
            'failed' => $failed,
            'errors' => array_slice($errors, 0, 10),
        ];
    }

    /**
     * Import lessons from file content (CSV, XLSX, JSON)
     */
    public function importLessonsFromFileContent(
        string $content,
        string $extension,
        ?int $fallbackCategoryId = null,
        string $duplicateStrategy = 'skip',
        ?User $user = null
    ): array {
        $extension = strtolower(trim($extension));

        if ($extension === 'json') {
            $rows = $this->parseJsonContent($content);
        } else {
            // CSV / text
            $rows = $this->parseCsvContent($content);
        }

        return $this->importLessons($rows, $fallbackCategoryId, $duplicateStrategy, $user);
    }

    /**
     * Import a batch of lesson rows into the database
     */
    public function importLessons(
        array $rows,
        ?int $fallbackCategoryId = null,
        string $duplicateStrategy = 'skip',
        ?User $uploader = null
    ): array {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $categories = Category::all()->keyBy(fn($c) => strtolower(trim($c->name)));
        $tracks = TaxonomyTrack::all()->keyBy(fn($t) => strtolower(trim($t->name)));

        $totalProcessed = 0;
        $successful = 0;
        $duplicatesSkipped = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $totalProcessed++;
            $rowNum = $index + 1;

            // Extract title
            $title = $row['title'] ?? $row['lesson_title'] ?? $row['name'] ?? $row['topic'] ?? null;
            if (empty($title)) {
                $failed++;
                $errors[] = "Row #{$rowNum}: Missing required lesson title.";
                continue;
            }
            $title = trim((string) $title);

            // Determine category
            $categoryId = $fallbackCategoryId;
            $trackName = strtolower(trim((string) ($row['track'] ?? $row['category'] ?? $row['category_name'] ?? '')));

            if ($trackName !== '') {
                if (isset($categories[$trackName])) {
                    $categoryId = $categories[$trackName]->id;
                } elseif (isset($tracks[$trackName])) {
                    $cat = Category::where('slug', $tracks[$trackName]->slug)->orWhere('name', $tracks[$trackName]->name)->first();
                    if ($cat) {
                        $categoryId = $cat->id;
                    }
                } else {
                    // Try partial match
                    foreach ($categories as $cName => $cat) {
                        if (str_contains($cName, $trackName) || str_contains($trackName, $cName)) {
                            $categoryId = $cat->id;
                            break;
                        }
                    }
                }
            }

            if (!$categoryId) {
                $firstCat = Category::first();
                $categoryId = $firstCat?->id;
            }

            if (!$categoryId) {
                $failed++;
                $errors[] = "Row #{$rowNum}: Could not resolve category for '{$title}'.";
                continue;
            }

            // Extract fields
            $subheading = $row['subheading'] ?? $row['hook_question'] ?? $row['subtitle'] ?? '';
            $rawContent = $row['content'] ?? $row['content_body'] ?? $row['body'] ?? $row['lesson_content'] ?? '';
            
            // Format content sections
            $contentSections = [];
            if (is_array($rawContent)) {
                $contentSections = $rawContent;
            } elseif (!empty(trim((string) $rawContent))) {
                $contentSections = [
                    [
                        'heading' => $title,
                        'body' => (string) $rawContent,
                    ]
                ];
            }

            // Summary takeaways
            $rawTakeaways = $row['summary_takeaways'] ?? $row['takeaways'] ?? $row['key_points'] ?? $row['summary'] ?? [];
            $takeaways = [];
            if (is_array($rawTakeaways)) {
                $takeaways = $rawTakeaways;
            } elseif (is_string($rawTakeaways) && !empty(trim($rawTakeaways))) {
                $takeaways = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|;/', $rawTakeaways))));
            }

            // Key terms
            $rawKeyTerms = $row['key_terms'] ?? $row['vocabulary'] ?? $row['keywords'] ?? [];
            $keyTerms = [];
            if (is_array($rawKeyTerms)) {
                $keyTerms = $rawKeyTerms;
            } elseif (is_string($rawKeyTerms) && !empty(trim($rawKeyTerms))) {
                $keyTerms = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $rawKeyTerms))));
            }

            // Read minutes & difficulty
            $readMinutes = isset($row['estimated_read_minutes']) ? (int) $row['estimated_read_minutes'] : (isset($row['read_time']) ? (int) $row['read_time'] : 5);
            $difficulty = isset($row['difficulty']) ? (int) $row['difficulty'] : (isset($row['level']) ? (int) $row['level'] : 1);
            if ($difficulty < 1) $difficulty = 1;
            if ($difficulty > 4) $difficulty = 4;

            $scripture = $row['scripture_citations'] ?? $row['scripture'] ?? $row['bible_citations'] ?? null;
            $catechism = $row['catechism_citations'] ?? $row['catechism'] ?? $row['ccc'] ?? $row['reference_citation'] ?? null;
            $status = strtolower((string) ($row['status'] ?? 'published'));
            if (!in_array($status, ['published', 'draft'])) {
                $status = 'published';
            }

            // Series identification & progressive linking
            $seriesId = $row['series_identifier'] ?? $row['series_id'] ?? $row['series_code'] ?? $row['series_slug'] ?? null;
            $seriesTitle = $row['series_title'] ?? $row['series_name'] ?? $row['series'] ?? null;
            $seriesOrder = isset($row['series_order']) && $row['series_order'] !== '' ? (int) $row['series_order'] : (isset($row['series_part']) && $row['series_part'] !== '' ? (int) $row['series_part'] : (isset($row['part']) && $row['part'] !== '' ? (int) $row['part'] : null));
            
            $isProgressive = true;
            if (isset($row['is_progressive'])) {
                $rawProg = $row['is_progressive'];
                if (is_bool($rawProg)) {
                    $isProgressive = $rawProg;
                } elseif (is_string($rawProg)) {
                    $rawLower = strtolower(trim($rawProg));
                    $isProgressive = !in_array($rawLower, ['false', '0', 'no', 'off', 'null', 'none', '']);
                } else {
                    $isProgressive = (bool) $rawProg;
                }
            }

            // Slugify seriesId if given
            if (!empty($seriesId)) {
                $seriesId = Str::slug((string) $seriesId);
            }

            // Auto-detect series from title if not explicitly provided (e.g. "Youth Leadership (Part 1)", "Joyful Mysteries - Lesson 2")
            if (empty($seriesId) && preg_match('/^(.*?)\s*[\-:\(]?(?:part|lesson|#|volume|vol|step|ep|episode)\s*([0-9]+)\)?/i', $title, $partMatches)) {
                $baseSeriesTitle = trim($partMatches[1], " -:(");
                if (!empty($baseSeriesTitle)) {
                    $seriesTitle = $seriesTitle ?: $baseSeriesTitle;
                    $seriesId = Str::slug($baseSeriesTitle);
                    $seriesOrder = $seriesOrder ?? (int) $partMatches[2];
                }
            } elseif (!empty($seriesTitle) && empty($seriesId)) {
                $seriesId = Str::slug((string) $seriesTitle);
            }

            if ($seriesId && empty($seriesTitle)) {
                $seriesTitle = ucwords(str_replace(['-', '_'], ' ', $seriesId));
            }

            if ($seriesId && $seriesOrder === null) {
                $seriesOrder = 1;
            }

            $slug = Str::slug($title);

            // Duplicate detection by title or slug
            $existingLesson = \App\Models\Lesson::where('title', $title)
                ->orWhere('slug', $slug)
                ->first();

            if ($existingLesson) {
                if ($duplicateStrategy === 'skip') {
                    $duplicatesSkipped++;
                    continue;
                }

                if ($duplicateStrategy === 'error') {
                    $failed++;
                    $errors[] = "Row #{$rowNum}: Duplicate lesson '{$title}' found.";
                    continue;
                }
            }

            try {
                DB::transaction(function () use (
                    $existingLesson,
                    $categoryId,
                    $seriesId,
                    $seriesTitle,
                    $seriesOrder,
                    $isProgressive,
                    $title,
                    $slug,
                    $subheading,
                    $contentSections,
                    $takeaways,
                    $keyTerms,
                    $readMinutes,
                    $difficulty,
                    $scripture,
                    $catechism,
                    $status
                ) {
                    if ($existingLesson) {
                        $existingLesson->update([
                            'category_id' => $categoryId,
                            'series_identifier' => $seriesId,
                            'series_title' => $seriesTitle,
                            'series_order' => $seriesOrder,
                            'is_progressive' => $isProgressive,
                            'title' => $title,
                            'slug' => $slug,
                            'subheading' => $subheading ?: null,
                            'content_sections' => $contentSections,
                            'summary_takeaways' => $takeaways,
                            'key_terms' => $keyTerms,
                            'estimated_read_minutes' => $readMinutes,
                            'difficulty' => $difficulty,
                            'scripture_citations' => $scripture ?: null,
                            'catechism_citations' => $catechism ?: null,
                            'status' => $status,
                        ]);
                        $lesson = $existingLesson;
                    } else {
                        $lesson = \App\Models\Lesson::create([
                            'category_id' => $categoryId,
                            'series_identifier' => $seriesId,
                            'series_title' => $seriesTitle,
                            'series_order' => $seriesOrder,
                            'is_progressive' => $isProgressive,
                            'title' => $title,
                            'slug' => $slug,
                            'subheading' => $subheading ?: null,
                            'content_sections' => $contentSections,
                            'summary_takeaways' => $takeaways,
                            'key_terms' => $keyTerms,
                            'estimated_read_minutes' => $readMinutes,
                            'difficulty' => $difficulty,
                            'scripture_citations' => $scripture ?: null,
                            'catechism_citations' => $catechism ?: null,
                            'status' => $status,
                            'display_order' => \App\Models\Lesson::where('category_id', $categoryId)->count() + 1,
                        ]);
                    }

                    // Also sync with MicroLesson table so youth can study in micro-lesson viewer
                    \App\Models\MicroLesson::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'category_id' => $categoryId,
                            'series_identifier' => $seriesId,
                            'series_title' => $seriesTitle,
                            'series_order' => $seriesOrder,
                            'is_progressive' => $isProgressive,
                            'title' => $title,
                            'hook_question' => $subheading ?: "What does Catholic doctrine teach about {$title}?",
                            'content_body' => is_array($contentSections) && isset($contentSections[0]['body']) ? $contentSections[0]['body'] : (string) json_encode($contentSections),
                            'takeaways' => $takeaways,
                            'reference_citation' => $catechism ?: ($scripture ?: 'CCC & Holy Scripture'),
                            'read_time_minutes' => $readMinutes,
                            'xp_reward' => 25,
                            'is_published' => ($status === 'published'),
                        ]
                    );
                });

                $successful++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "Row #{$rowNum}: {$e->getMessage()}";
            }
        }

        return [
            'total_processed' => $totalProcessed,
            'successful' => $successful,
            'duplicates_skipped' => $duplicatesSkipped,
            'failed' => $failed,
            'errors' => array_slice($errors, 0, 10),
        ];
    }

    /**
     * Generate sample CSV template for Lessons
     */
    public function getSampleLessonCsv(): string
    {
        $headers = ['title', 'track', 'series_identifier', 'series_title', 'series_order', 'is_progressive', 'subheading', 'content', 'summary_takeaways', 'estimated_read_minutes', 'difficulty', 'scripture_citations', 'catechism_citations'];
        $sampleRows = [
            [
                'The Seven Sacraments (Part 1): Sacraments of Initiation',
                'Sacraments of the Church',
                'seven-sacraments',
                'The Seven Sacraments of the Church',
                '1',
                'true',
                'How does Christ impart sanctifying grace to us through Baptism, Confirmation, and Eucharist?',
                "The Church celebrates seven sacraments instituted by Christ. The Sacraments of Christian Initiation—Baptism, Confirmation, and the Eucharist—lay the foundations of every Christian life.\n\nEach sacrament consists of proper matter, form, and intent, truly conferring the grace they signify.",
                "Sacraments were instituted by Jesus Christ.\nBaptism, Confirmation, and Eucharist form Christian Initiation.\nThey impart sanctifying grace to the soul.",
                '5',
                '1',
                'Matthew 28:19, John 20:22-23',
                'CCC 1113-1134, CCC 1210',
            ],
            [
                'The Seven Sacraments (Part 2): Sacraments of Healing & Service',
                'Sacraments of the Church',
                'seven-sacraments',
                'The Seven Sacraments of the Church',
                '2',
                'true',
                'How does Christ heal and commission us through Penance, Anointing, Holy Orders, and Matrimony?',
                "The Lord Jesus Christ, physician of our souls and bodies, willed that his Church continue his work of healing and salvation. Penance and Anointing of the Sick provide healing, while Holy Orders and Matrimony direct salvation towards the service of others.",
                "Penance and Anointing are Sacraments of Healing.\nHoly Orders and Matrimony are Sacraments of Service/Mission.\nThey strengthen Christians to serve Christ and Church.",
                '6',
                '2',
                'Luke 22:19-20, James 5:14-15, John 6:51-58',
                'CCC 1324, CCC 1420-1666',
            ],
            [
                'The Beatitudes: The Heart of Jesus Preaching',
                'Christian Morality & Ten Commandments',
                '',
                '',
                '',
                'true',
                'How do the Beatitudes fulfill and elevate the Ten Commandments?',
                "The Beatitudes (Matthew 5:3-12) depict the countenance of Jesus Christ and portray his charity. They express the vocation of the faithful associated with the glory of his Passion and Resurrection.\n\nThey respond to the natural desire for happiness that God has placed in the human heart.",
                "The Beatitudes are the path to true Christian joy.\nThey call Christians to poverty of spirit, meekness, and purity of heart.\nThey fulfill the promises made to the Chosen People from Abraham.",
                '4',
                '1',
                'Matthew 5:3-12, Luke 6:20-26',
                'CCC 1716-1724',
            ]
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return (string) $csv;
    }

    /**
     * Generate sample JSON template for Lessons
     */
    public function getSampleLessonJson(): string
    {
        return json_encode([
            'lessons' => [
                [
                    'title' => 'The Holy Trinity (Part 1): One God in Three Divine Persons',
                    'track' => 'Catholic Doctrine & Creed',
                    'series_identifier' => 'holy-trinity',
                    'series_title' => 'The Mystery of the Most Holy Trinity',
                    'series_order' => 1,
                    'is_progressive' => true,
                    'subheading' => 'How can God be Three Divine Persons in One Divine Nature?',
                    'content' => "The mystery of the Most Holy Trinity is the central mystery of Christian faith and life. It is the mystery of God in himself.\n\nWe do not confess three gods, but one God in three persons: the Father, the Son, and the Holy Spirit. The divine persons are really distinct from one another in their relations of origin.",
                    'summary_takeaways' => [
                        'The Trinity is One God in Three Divine Persons: Father, Son, and Holy Spirit.',
                        'The Father generates, the Son is begotten, and the Holy Spirit proceeds from Father and Son.',
                        'All three Persons share one and the same divine nature and eternal essence.'
                    ],
                    'key_terms' => ['Trinity', 'Consubstantial', 'Hypostasis', 'Monotheism'],
                    'estimated_read_minutes' => 5,
                    'difficulty' => 2,
                    'scripture_citations' => 'Matthew 28:19, 2 Corinthians 13:14',
                    'catechism_citations' => 'CCC 232-260',
                    'status' => 'published'
                ],
                [
                    'title' => 'The Holy Trinity (Part 2): The Economic Trinity and Salvation',
                    'track' => 'Catholic Doctrine & Creed',
                    'series_identifier' => 'holy-trinity',
                    'series_title' => 'The Mystery of the Most Holy Trinity',
                    'series_order' => 2,
                    'is_progressive' => true,
                    'subheading' => 'How does the Trinity work throughout human salvation history?',
                    'content' => "God's whole divine economy is the common work of the three divine persons. For as the Trinity has only one and the same nature, so too does it have only one and the same operation.\n\nCreation is attributed to the Father, Redemption to the Son, and Sanctification to the Holy Spirit.",
                    'summary_takeaways' => [
                        'The divine persons act together in salvation history.',
                        'Creation is attributed to the Father, Redemption to the Son, Sanctification to the Holy Spirit.',
                        'By grace, Christians are invited into communion with the Trinity.'
                    ],
                    'key_terms' => ['Divine Economy', 'Sanctification', 'Redemption', 'Grace'],
                    'estimated_read_minutes' => 6,
                    'difficulty' => 2,
                    'scripture_citations' => 'John 14:26, Ephesians 1:3-14',
                    'catechism_citations' => 'CCC 257-260',
                    'status' => 'published'
                ]
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate downloadable sample template for Lessons
     */
    public function generateSampleLessonTemplate(string $format = 'csv'): array
    {
        $format = strtolower($format);

        if ($format === 'json') {
            return [
                'filename' => 'catholic_lessons_template.json',
                'mime' => 'application/json',
                'content' => $this->getSampleLessonJson(),
            ];
        }

        return [
            'filename' => 'catholic_lessons_template.csv',
            'mime' => 'text/csv',
            'content' => $this->getSampleLessonCsv(),
        ];
    }

    /**
     * Generate sample CSV template string
     */
    public function getSampleCsv(): string
    {
        $headers = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_option', 'explanation', 'reference_citation', 'difficulty', 'track'];
        $sampleRows = [
            [
                'Which Gospel is symbolized by a Winged Ox in Catholic tradition?',
                'Gospel of Matthew',
                'Gospel of Mark',
                'Gospel of Luke',
                'Gospel of John',
                'C',
                'The Gospel of Luke is symbolized by the Winged Ox representing sacrifice and priesthood.',
                'CCC 1174, Luke 1:5',
                'MEDIUM',
                'Holy Scripture',
            ],
            [
                'What is the term for the mystery of the Son of God becoming human?',
                'Transubstantiation',
                'Incarnation',
                'Ascension',
                'Assumption',
                'B',
                'The Incarnation is the mystery of the Word becoming flesh in Jesus Christ.',
                'CCC 461, John 1:14',
                'EASY',
                'The Creed & Holy Trinity',
            ],
            [
                'How many Sacraments are recognized by the Holy Catholic Church?',
                'Three',
                'Five',
                'Seven',
                'Twelve',
                'C',
                'The Seven Sacraments were instituted by Christ to dispense divine grace.',
                'CCC 1113-1134',
                'EASY',
                'Sacraments & Grace',
            ]
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return (string) $csv;
    }

    /**
     * Generate sample JSON template string
     */
    public function getSampleJson(): string
    {
        return json_encode([
            'questions' => [
                [
                    'question_text' => 'Which Council defined the Dogma of the Immaculate Conception?',
                    'option_a' => 'Council of Trent',
                    'option_b' => 'Pope Pius IX (Ineffabilis Deus 1854)',
                    'option_c' => 'Council of Nicaea',
                    'option_d' => 'Vatican II',
                    'correct_option' => 'B',
                    'explanation' => 'Pope Pius IX dogmatically defined Mary Immaculate Conception in 1854.',
                    'reference_citation' => 'CCC 491',
                    'difficulty' => 'HARD',
                    'track' => 'The Blessed Virgin Mary',
                ],
                [
                    'question_text' => 'Who is the patron saint of youth and altar servers?',
                    'option_a' => 'St. John Bosco',
                    'option_b' => 'St. Tarcisius',
                    'option_c' => 'St. Aloysius Gonzaga',
                    'option_d' => 'St. Dominic Savio',
                    'correct_option' => 'D',
                    'explanation' => 'St. Dominic Savio is the patron saint of Catholic youth and choirs.',
                    'reference_citation' => 'Roman Martyrology',
                    'difficulty' => 'EASY',
                    'track' => 'Saints & Martyrs',
                ]
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

