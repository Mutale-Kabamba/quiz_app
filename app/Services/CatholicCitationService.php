<?php

namespace App\Services;

use App\Models\CatholicReference;
use App\Models\CatholicSource;

class CatholicCitationService
{
    /**
     * Canonical list of standard Biblical books (Catholic 73-book canon)
     */
    protected array $catholicBibleBooks = [
        'Genesis', 'Exodus', 'Leviticus', 'Numbers', 'Deuteronomy',
        'Joshua', 'Judges', 'Ruth', '1 Samuel', '2 Samuel', '1 Kings', '2 Kings',
        '1 Chronicles', '2 Chronicles', 'Ezra', 'Nehemiah', 'Tobit', 'Judith',
        'Esther', '1 Maccabees', '2 Maccabees', 'Job', 'Psalms', 'Proverbs',
        'Ecclesiastes', 'Song of Solomon', 'Wisdom', 'Sirach', 'Isaiah',
        'Jeremiah', 'Lamentations', 'Baruch', 'Ezekiel', 'Daniel', 'Hosea',
        'Joel', 'Amos', 'Obadiah', 'Jonah', 'Micah', 'Nahum', 'Habakkuk',
        'Zephaniah', 'Haggai', 'Zechariah', 'Malachi',
        'Matthew', 'Mark', 'Luke', 'John', 'Acts', 'Romans',
        '1 Corinthians', '2 Corinthians', 'Galatians', 'Ephesians', 'Philippians',
        'Colossians', '1 Thessalonians', '2 Thessalonians', '1 Timothy', '2 Timothy',
        'Titus', 'Philemon', 'Hebrews', 'James', '1 Peter', '2 Peter',
        '1 John', '2 John', '3 John', 'Jude', 'Revelation'
    ];

    /**
     * Validate and structure a citation string
     */
    public function validateAndStructureCitation(string $sourceCode, string $rawCitation): array
    {
        $source = CatholicSource::where('short_code', strtoupper($sourceCode))->first();
        $code = strtoupper($sourceCode);

        $isValid = false;
        $error = null;
        $parsed = [
            'source_id' => $source?->id,
            'source_code' => $code,
            'citation_label' => trim($rawCitation),
            'book_or_volume' => null,
            'chapter_or_section' => null,
            'verse_or_paragraph_range' => null,
        ];

        switch ($code) {
            case 'CCC':
                // Check CCC format: e.g. "CCC 1213", "1213-1216", "CCC #1213"
                if (preg_match('/(?:CCC\s*#?|#)?\s*(\d{1,4})(?:\s*-\s*(\d{1,4}))?/i', $rawCitation, $matches)) {
                    $start = (int) $matches[1];
                    $end = isset($matches[2]) ? (int) $matches[2] : $start;

                    if ($start >= 1 && $start <= 2865 && $end >= $start && $end <= 2865) {
                        $isValid = true;
                        $parsed['citation_label'] = "CCC {$start}" . ($end > $start ? "-{$end}" : "");
                        $parsed['chapter_or_section'] = null;
                        $parsed['verse_or_paragraph_range'] = (string) ($end > $start ? "{$start}-{$end}" : $start);
                    } else {
                        $error = "CCC paragraph must be between 1 and 2865.";
                    }
                } else {
                    $error = "Invalid CCC format. Example: CCC 1213 or CCC 1213-1216.";
                }
                break;

            case 'YOUCAT':
                if (preg_match('/(?:YOUCAT\s*#?|#)?\s*(\d{1,3})/i', $rawCitation, $matches)) {
                    $num = (int) $matches[1];
                    if ($num >= 1 && $num <= 527) {
                        $isValid = true;
                        $parsed['citation_label'] = "YOUCAT #{$num}";
                        $parsed['verse_or_paragraph_range'] = (string) $num;
                    } else {
                        $error = "YOUCAT question number must be between 1 and 527.";
                    }
                } else {
                    $error = "Invalid YOUCAT format. Example: YOUCAT 194.";
                }
                break;

            case 'DOCAT':
                if (preg_match('/(?:DOCAT\s*#?|#)?\s*(\d{1,3})/i', $rawCitation, $matches)) {
                    $num = (int) $matches[1];
                    if ($num >= 1 && $num <= 328) {
                        $isValid = true;
                        $parsed['citation_label'] = "DOCAT #{$num}";
                        $parsed['verse_or_paragraph_range'] = (string) $num;
                    } else {
                        $error = "DOCAT question number must be between 1 and 328.";
                    }
                } else {
                    $error = "Invalid DOCAT format. Example: DOCAT 84.";
                }
                break;

            case 'RSVCE':
            case 'SCRIPTURE':
            case 'BIBLE':
                // Match Scripture e.g. "John 3:16", "Romans 12:1-2", "1 Cor 13:4-8"
                if (preg_match('/^([1-3]?\s*[a-zA-Z]+)\s+(\d+)(?::(\d+(?:-\d+)?))?/i', $rawCitation, $matches)) {
                    $book = trim($matches[1]);
                    $chapter = (int) $matches[2];
                    $verses = $matches[3] ?? null;

                    $isValid = true;
                    $parsed['book_or_volume'] = $book;
                    $parsed['chapter_or_section'] = $chapter;
                    $parsed['verse_or_paragraph_range'] = $verses;
                    $parsed['citation_label'] = "{$book} {$chapter}" . ($verses ? ":{$verses}" : "");
                } else {
                    $error = "Invalid Scripture format. Example: John 3:16 or Romans 12:1-2.";
                }
                break;

            default:
                $isValid = !empty(trim($rawCitation));
                $parsed['citation_label'] = trim($rawCitation);
                break;
        }

        return [
            'is_valid' => $isValid,
            'error' => $error,
            'structured_reference' => $parsed,
        ];
    }

    /**
     * Create or retrieve a structured Catholic reference record
     */
    public function getOrCreateReference(string $sourceCode, string $citationLabel, ?string $excerpt = null): ?CatholicReference
    {
        $validated = $this->validateAndStructureCitation($sourceCode, $citationLabel);
        if (!$validated['is_valid']) {
            return null;
        }

        $struct = $validated['structured_reference'];
        $source = CatholicSource::firstOrCreate(
            ['short_code' => strtoupper($sourceCode)],
            [
                'title' => strtoupper($sourceCode),
                'publisher_authority' => 'Catholic Church',
                'document_type' => 'APPROVED_PUBLICATION',
                'is_verified' => true,
            ]
        );

        return CatholicReference::firstOrCreate(
            [
                'source_id' => $source->id,
                'citation_label' => $struct['citation_label'],
            ],
            [
                'book_or_volume' => $struct['book_or_volume'],
                'chapter_or_section' => $struct['chapter_or_section'],
                'verse_or_paragraph_range' => $struct['verse_or_paragraph_range'],
                'excerpt_text' => $excerpt,
            ]
        );
    }
}
