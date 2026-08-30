<?php

namespace Tests\Feature;

use App\Livewire\ArenaHub;
use App\Livewire\DioceseDashboard;
use App\Models\Category;
use App\Models\Deanery;
use App\Models\DiocesanCompetition;
use App\Models\Lesson;
use App\Models\Parish;
use App\Models\Question;
use App\Models\QuestionBankItem;
use App\Models\TaxonomyTrack;
use App\Models\User;
use App\Services\DynamicContentImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class SuperAdminCrudAndDynamicImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected Deanery $deanery;
    protected Parish $parish;
    protected Category $category;
    protected TaxonomyTrack $track;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deanery = Deanery::create([
            'name' => 'Livingstone Urban Deanery',
            'code' => 'LIV-URB',
            'headquarters' => 'Cathedral of St. Theresa',
        ]);

        $this->parish = Parish::create([
            'deanery_id' => $this->deanery->id,
            'name' => 'St. Theresa Cathedral',
            'code' => 'ST-THERESA',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Holy Scripture & Gospels',
            'slug' => 'holy-scripture-and-gospels',
            'description' => 'Testament study and Gospels',
            'is_active' => true,
        ]);

        $this->track = TaxonomyTrack::create([
            'name' => 'Holy Scripture & Gospels',
            'slug' => 'holy-scripture-and-gospels',
            'code' => 'HOLY_SCRIPTURE',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Diocese Super Admin',
            'email' => 'admin@livingstonediocese.org',
            'phone' => '+260970000001',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'approved',
            'parish_id' => $this->parish->id,
        ]);
    }

    public function test_super_admin_can_crud_deanery(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Deanery
        Livewire::test(DioceseDashboard::class)
            ->set('deaneryName', 'Kazungula Rural Deanery')
            ->set('deaneryCode', 'KAZ-RUR')
            ->set('deaneryHeadquarters', 'St. Mary Parish')
            ->call('saveDeanery')
            ->assertHasNoErrors();

        $createdDeanery = Deanery::where('code', 'KAZ-RUR')->first();
        $this->assertNotNull($createdDeanery);
        $this->assertEquals('Kazungula Rural Deanery', $createdDeanery->name);

        // 2. Edit & Update Deanery
        Livewire::test(DioceseDashboard::class)
            ->call('editDeanery', $createdDeanery->id)
            ->assertSet('deaneryName', 'Kazungula Rural Deanery')
            ->set('deaneryName', 'Kazungula West Deanery')
            ->call('saveDeanery')
            ->assertHasNoErrors();

        $this->assertEquals('Kazungula West Deanery', $createdDeanery->fresh()->name);

        // 3. Delete Deanery (when no parishes attached)
        Livewire::test(DioceseDashboard::class)
            ->call('deleteDeanery', $createdDeanery->id);

        $this->assertDatabaseMissing('deaneries', ['id' => $createdDeanery->id]);
    }

    public function test_super_admin_can_crud_parish(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Parish
        Livewire::test(DioceseDashboard::class)
            ->set('newParishName', 'St. Jude Parish')
            ->set('newParishCode', 'ST-JUDE')
            ->set('newParishDeaneryId', $this->deanery->id)
            ->set('newParishLocation', 'Maramba')
            ->call('saveParish')
            ->assertHasNoErrors();

        $createdParish = Parish::where('code', 'ST-JUDE')->first();
        $this->assertNotNull($createdParish);

        // 2. Edit & Update Parish
        Livewire::test(DioceseDashboard::class)
            ->call('editParish', $createdParish->id)
            ->assertSet('newParishName', 'St. Jude Parish')
            ->set('newParishName', 'St. Jude Thaddaeus Parish')
            ->call('saveParish')
            ->assertHasNoErrors();

        $this->assertEquals('St. Jude Thaddaeus Parish', $createdParish->fresh()->name);

        // 3. Delete Parish (when no users attached)
        Livewire::test(DioceseDashboard::class)
            ->call('deleteParish', $createdParish->id);

        $this->assertDatabaseMissing('parishes', ['id' => $createdParish->id]);
    }

    public function test_super_admin_can_crud_track(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Track
        Livewire::test(DioceseDashboard::class)
            ->set('trackName', 'Catholic Liturgy & Mass')
            ->set('trackDescription', 'Understanding the Holy Sacrifice of the Mass')
            ->set('trackDisplayOrder', 2)
            ->call('saveTrack')
            ->assertHasNoErrors();

        $createdTrack = TaxonomyTrack::where('name', 'Catholic Liturgy & Mass')->first();
        $this->assertNotNull($createdTrack);

        // 2. Edit & Update Track
        Livewire::test(DioceseDashboard::class)
            ->call('editTrack', $createdTrack->id)
            ->set('trackDescription', 'Sacred Liturgy and Rites')
            ->call('saveTrack')
            ->assertHasNoErrors();

        $this->assertEquals('Sacred Liturgy and Rites', $createdTrack->fresh()->description);

        // 3. Delete Track
        Livewire::test(DioceseDashboard::class)
            ->call('deleteTrack', $createdTrack->id);

        $this->assertDatabaseMissing('taxonomy_tracks', ['id' => $createdTrack->id]);
    }

    public function test_super_admin_can_crud_question(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Question
        Livewire::test(DioceseDashboard::class)
            ->set('newQuestionCategoryId', $this->category->id)
            ->set('newQuestionText', 'Who is the author of the Acts of the Apostles in Sacred Scripture?')
            ->set('optionA', 'St. Peter')
            ->set('optionB', 'St. Luke')
            ->set('optionC', 'St. Paul')
            ->set('optionD', 'St. John')
            ->set('correctOption', 'B')
            ->set('newQuestionExplanation', 'St. Luke the Evangelist authored the Acts of the Apostles.')
            ->set('newQuestionCitation', 'Luke 1:1-4, Acts 1:1')
            ->call('saveQuestion')
            ->assertHasNoErrors();

        $createdQuestion = Question::where('question_text', 'like', '%Acts of the Apostles%')->first();
        $this->assertNotNull($createdQuestion);
        $this->assertCount(4, $createdQuestion->options);

        // 2. Edit Question
        Livewire::test(DioceseDashboard::class)
            ->call('editQuestion', $createdQuestion->id)
            ->assertSet('optionB', 'St. Luke')
            ->set('newQuestionExplanation', 'St. Luke was a companion of St. Paul and wrote Acts.')
            ->call('saveQuestion')
            ->assertHasNoErrors();

        $this->assertEquals('St. Luke was a companion of St. Paul and wrote Acts.', $createdQuestion->fresh()->explanation);

        // 3. Delete Question
        Livewire::test(DioceseDashboard::class)
            ->call('deleteQuestion', $createdQuestion->id);

        $this->assertDatabaseMissing('questions', ['id' => $createdQuestion->id]);
    }

    public function test_super_admin_can_crud_competition_and_rally(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Schedule Rally
        Livewire::test(DioceseDashboard::class)
            ->set('newCompTitle', 'Diocesan Youth Bible Championship 2026')
            ->set('newCompDescription', 'Annual faith quiz competition across all Livingstone parishes.')
            ->set('newCompType', 'youth_rally')
            ->set('newCompCategoryId', $this->category->id)
            ->set('newCompStartTime', now()->addDays(1)->format('Y-m-d\TH:i'))
            ->set('newCompEndTime', now()->addDays(5)->format('Y-m-d\TH:i'))
            ->call('saveCompetition')
            ->assertHasNoErrors();

        $comp = DiocesanCompetition::where('title', 'Diocesan Youth Bible Championship 2026')->first();
        $this->assertNotNull($comp);
        $this->assertEquals('active', $comp->status);

        // 2. Toggle status
        Livewire::test(DioceseDashboard::class)
            ->call('toggleCompetitionStatus', $comp->id);

        $this->assertEquals('concluded', $comp->fresh()->status);

        // 3. Delete Competition
        Livewire::test(DioceseDashboard::class)
            ->call('deleteCompetition', $comp->id);

        $this->assertDatabaseMissing('diocesan_competitions', ['id' => $comp->id]);
    }

    protected function createUploadedFile(string $filename, string $content): \Illuminate\Http\Testing\File
    {
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'upload_test_' . uniqid() . '_' . $filename;
        file_put_contents($tempPath, $content);
        $file = new \Illuminate\Http\Testing\File($filename, fopen($tempPath, 'r+'));
        $file->sizeToReport = filesize($tempPath);
        return $file;
    }

    public function test_dynamic_csv_import_parses_and_inserts_questions(): void
    {
        $this->actingAs($this->superAdmin);

        $csvContent = "question_text,option_a,option_b,option_c,option_d,correct_option,explanation,reference_citation,difficulty,track\n"
            . "\"What is the first Sacrament of Christian Initiation?\",\"Confirmation\",\"Baptism\",\"Eucharist\",\"Holy Orders\",\"B\",\"Baptism cleanses original sin and incorporates us into Christ.\",\"CCC 1213\",\"EASY\",\"Holy Scripture & Gospels\"\n"
            . "\"How many gifts of the Holy Spirit are listed in Isaiah 11?\",\"Five\",\"Six\",\"Seven\",\"Twelve\",\"C\",\"The Seven Gifts of the Holy Spirit are wisdom, understanding, counsel, fortitude, knowledge, piety, and fear of the Lord.\",\"CCC 1831, Isaiah 11:1-3\",\"MEDIUM\",\"Holy Scripture & Gospels\"\n";

        $file = $this->createUploadedFile('custom_questions.csv', $csvContent);

        Livewire::test(DioceseDashboard::class)
            ->set('importFile', $file)
            ->set('importDuplicateStrategy', 'skip')
            ->call('processDynamicImport')
            ->assertHasNoErrors()
            ->assertSet('importResults.successful', 2)
            ->assertSet('importResults.duplicates_skipped', 0);

        $this->assertDatabaseHas('questions', ['question_text' => 'What is the first Sacrament of Christian Initiation?']);
        $this->assertDatabaseHas('questions', ['question_text' => 'How many gifts of the Holy Spirit are listed in Isaiah 11?']);

        // Duplicate test: re-importing same file with skip strategy
        $file2 = $this->createUploadedFile('custom_questions_2.csv', $csvContent);
        Livewire::test(DioceseDashboard::class)
            ->set('importFile', $file2)
            ->set('importDuplicateStrategy', 'skip')
            ->call('processDynamicImport')
            ->assertHasNoErrors()
            ->assertSet('importResults.successful', 0)
            ->assertSet('importResults.duplicates_skipped', 2);
    }

    public function test_dynamic_json_import_parses_and_inserts_questions(): void
    {
        $this->actingAs($this->superAdmin);

        $jsonContent = json_encode([
            'questions' => [
                [
                    'question_text' => 'Where was St. Paul converted on the road?',
                    'option_a' => 'Rome',
                    'option_b' => 'Jerusalem',
                    'option_c' => 'Damascus',
                    'option_d' => 'Antioch',
                    'correct_option' => 'C',
                    'explanation' => 'Saul of Tarsus encountered the Risen Christ on the road to Damascus.',
                    'reference_citation' => 'Acts 9:1-19',
                    'difficulty' => 'EASY',
                    'track' => 'Holy Scripture & Gospels',
                ]
            ]
        ]);

        $file = $this->createUploadedFile('questions.json', $jsonContent);

        Livewire::test(DioceseDashboard::class)
            ->set('importFile', $file)
            ->set('importDuplicateStrategy', 'skip')
            ->call('processDynamicImport')
            ->assertHasNoErrors()
            ->assertSet('importResults.successful', 1);

        $this->assertDatabaseHas('questions', ['question_text' => 'Where was St. Paul converted on the road?']);
    }

    public function test_dynamic_excel_xlsx_import_parses_and_inserts_questions(): void
    {
        $this->actingAs($this->superAdmin);

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'xlsx_test_' . uniqid() . '.xlsx';
        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToFile($tempPath);

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_option', 'explanation', 'reference_citation', 'difficulty', 'track'
        ]));

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'Who was the foster father of Jesus Christ on earth?',
            'St. Peter',
            'St. Joseph',
            'St. John the Baptist',
            'St. Joachim',
            'B',
            'St. Joseph of the House of David was the chaste spouse of the Blessed Virgin Mary and foster father of Jesus.',
            'Matthew 1:18-25, Luke 2:41-52',
            'EASY',
            'Holy Scripture & Gospels'
        ]));

        $writer->close();

        $file = new \Illuminate\Http\Testing\File('questions.xlsx', fopen($tempPath, 'r+'));
        $file->sizeToReport = filesize($tempPath);

        Livewire::test(DioceseDashboard::class)
            ->set('importFile', $file)
            ->set('importDuplicateStrategy', 'skip')
            ->call('processDynamicImport')
            ->assertHasNoErrors()
            ->assertSet('importResults.successful', 1);

        $this->assertDatabaseHas('questions', ['question_text' => 'Who was the foster father of Jesus Christ on earth?']);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    public function test_super_admin_can_create_update_toggle_and_delete_lessons(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Lesson
        Livewire::test(DioceseDashboard::class)
            ->call('openCreateLessonModal')
            ->set('lessonCategoryId', $this->category->id)
            ->set('lessonTitle', 'The Seven Sacraments of the Church')
            ->set('lessonSubheading', 'Efficacious signs of grace instituted by Christ')
            ->set('lessonContent', 'The sacraments are outward signs instituted by Christ to give grace. There are seven sacraments in the Catholic Church.')
            ->set('lessonTakeaways', "Sacraments confer sanctifying grace\nThey are divided into Christian Initiation, Healing, and Service")
            ->set('lessonScripture', 'Matthew 28:19')
            ->set('lessonCatechism', 'CCC 1113-1134')
            ->set('lessonReadMinutes', 6)
            ->set('lessonDifficulty', 1)
            ->set('lessonStatus', 'published')
            ->call('saveLesson')
            ->assertHasNoErrors()
            ->assertSet('showLessonModal', false);

        $this->assertDatabaseHas('lessons', [
            'title' => 'The Seven Sacraments of the Church',
            'category_id' => $this->category->id,
            'status' => 'published',
            'estimated_read_minutes' => 6,
        ]);

        $lesson = \App\Models\Lesson::where('title', 'The Seven Sacraments of the Church')->first();
        $this->assertNotNull($lesson);

        // 2. Edit Lesson
        Livewire::test(DioceseDashboard::class)
            ->call('editLesson', $lesson->id)
            ->assertSet('showLessonModal', true)
            ->assertSet('lessonTitle', 'The Seven Sacraments of the Church')
            ->set('lessonTitle', 'The Seven Holy Sacraments of the Catholic Church')
            ->call('saveLesson')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'title' => 'The Seven Holy Sacraments of the Catholic Church',
        ]);

        // 3. Toggle Status (published -> draft)
        Livewire::test(DioceseDashboard::class)
            ->call('toggleLessonStatus', $lesson->id);

        $lesson->refresh();
        $this->assertEquals('draft', $lesson->status);

        // 4. Delete Lesson
        Livewire::test(DioceseDashboard::class)
            ->call('deleteLesson', $lesson->id);

        $this->assertDatabaseMissing('lessons', [
            'id' => $lesson->id,
        ]);
    }

    public function test_super_admin_can_crud_questions_and_rallies_via_arena_hub(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Question CRUD in ArenaHub
        Livewire::test(\App\Livewire\ArenaHub::class)
            ->set('newQuestionCategoryId', $this->category->id)
            ->set('newQuestionLevel', 2)
            ->set('newQuestionText', 'Who is the patron saint of youth and altar servers in Catholic tradition?')
            ->set('optionA', 'St. Augustine')
            ->set('optionB', 'St. Aloysius Gonzaga')
            ->set('optionC', 'St. Thomas Aquinas')
            ->set('optionD', 'St. Francis of Assisi')
            ->set('correctOption', 'B')
            ->set('newQuestionCitation', 'Catholic Saints')
            ->set('newQuestionExplanation', 'St. Aloysius Gonzaga is a patron saint of Christian youth.')
            ->call('saveQuestion')
            ->assertHasNoErrors();

        $question = Question::where('question_text', 'Who is the patron saint of youth and altar servers in Catholic tradition?')->first();
        $this->assertNotNull($question);
        $this->assertEquals('B', $question->correct_option_key);

        // Edit question via ArenaHub
        Livewire::test(\App\Livewire\ArenaHub::class)
            ->call('editQuestion', $question->id)
            ->assertSet('optionB', 'St. Aloysius Gonzaga')
            ->set('newQuestionExplanation', 'St. Aloysius Gonzaga and St. John Bosco are patrons of youth.')
            ->call('saveQuestion')
            ->assertHasNoErrors();

        $this->assertEquals('St. Aloysius Gonzaga and St. John Bosco are patrons of youth.', $question->fresh()->explanation);

        // Delete question via ArenaHub
        Livewire::test(\App\Livewire\ArenaHub::class)
            ->call('deleteQuestion', $question->id);

        $this->assertDatabaseMissing('questions', ['id' => $question->id]);

        // 2. Rally CRUD in ArenaHub
        Livewire::test(\App\Livewire\ArenaHub::class)
            ->set('newCompTitle', 'Livingstone Bible Olympiad 2026')
            ->set('newCompDescription', 'Diocesan inter-parish youth competition.')
            ->set('newCompCategoryId', $this->category->id)
            ->set('newCompStartTime', now()->addDays(1)->format('Y-m-d\TH:i'))
            ->set('newCompEndTime', now()->addDays(7)->format('Y-m-d\TH:i'))
            ->set('newCompTimeLimit', 360)
            ->set('newCompQuestionCount', 20)
            ->call('saveCompetition')
            ->assertHasNoErrors();

        $comp = DiocesanCompetition::where('title', 'Livingstone Bible Olympiad 2026')->first();
        $this->assertNotNull($comp);

        // Edit competition via ArenaHub
        Livewire::test(\App\Livewire\ArenaHub::class)
            ->call('editCompetition', $comp->id)
            ->set('newCompTitle', 'Livingstone Grand Bible Olympiad 2026')
            ->call('saveCompetition')
            ->assertHasNoErrors();

        $this->assertEquals('Livingstone Grand Bible Olympiad 2026', $comp->fresh()->title);

        // Toggle status
        Livewire::test(\App\Livewire\ArenaHub::class)
            ->call('toggleCompetitionStatus', $comp->id);

        $this->assertEquals('concluded', $comp->fresh()->status);

        // Delete competition
        Livewire::test(\App\Livewire\ArenaHub::class)
            ->call('deleteCompetition', $comp->id);

        $this->assertDatabaseMissing('diocesan_competitions', ['id' => $comp->id]);
    }

    public function test_dynamic_csv_and_json_import_for_lessons(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. CSV Lesson Import Test
        $csvContent = "title,track,subheading,content,summary_takeaways,estimated_read_minutes,difficulty,scripture_citations,catechism_citations\n"
            . "\"The Sacrament of Holy Orders\",\"Sacraments of the Church\",\"How is apostolic succession handed down through the episcopate, presbyterate, and diaconate?\",\"Holy Orders is the sacrament through which the mission entrusted by Christ to his apostles continues to be exercised in the Church until the end of time.\",\"Three degrees: Bishop, Priest, Deacon; Configures to Christ the Head; Imparts indelible character\",\"6\",\"2\",\"1 Timothy 3:1-13\",\"CCC 1536-1600\"\n";

        $file = $this->createUploadedFile('custom_lessons.csv', $csvContent);

        Livewire::test(DioceseDashboard::class)
            ->set('lessonImportFile', $file)
            ->set('lessonImportDuplicateStrategy', 'skip')
            ->call('processLessonImport')
            ->assertHasNoErrors();

        $lesson = Lesson::where('title', 'The Sacrament of Holy Orders')->first();
        $this->assertNotNull($lesson);
        $this->assertEquals(6, $lesson->estimated_read_minutes);
        $this->assertEquals(2, $lesson->difficulty);
        $this->assertEquals('1 Timothy 3:1-13', $lesson->scripture_citations);

        // Verify synced micro-lesson
        $microLesson = \App\Models\MicroLesson::where('title', 'The Sacrament of Holy Orders')->first();
        $this->assertNotNull($microLesson);

        // 2. JSON Lesson Import Test
        $jsonContent = json_encode([
            'lessons' => [
                [
                    'title' => 'The Virtue of Christian Fortitude',
                    'track' => 'Christian Morality & Ten Commandments',
                    'subheading' => 'How does fortitude ensure firmness in difficulties and constancy in the pursuit of the good?',
                    'content' => 'Fortitude is the moral virtue that ensures firmness in difficulties and constancy in the pursuit of the good. It strengthens the resolve to resist temptations and to overcome obstacles in the moral life.',
                    'summary_takeaways' => [
                        'Fortitude is one of the four Cardinal Virtues.',
                        'It enables one to conquer fear, even fear of death, and to face trials.',
                        'It disposes one even to renounce and sacrifice his life in defense of a just cause.'
                    ],
                    'key_terms' => ['Virtue', 'Fortitude', 'Martyrdom', 'Cardinal Virtues'],
                    'estimated_read_minutes' => 4,
                    'difficulty' => 1,
                    'scripture_citations' => 'Psalm 27:1, John 16:33',
                    'catechism_citations' => 'CCC 1808',
                    'status' => 'published'
                ]
            ]
        ]);

        $jsonFile = $this->createUploadedFile('custom_lessons.json', $jsonContent);

        Livewire::test(DioceseDashboard::class)
            ->set('lessonImportFile', $jsonFile)
            ->set('lessonImportDuplicateStrategy', 'skip')
            ->call('processLessonImport')
            ->assertHasNoErrors();

        $fortitudeLesson = Lesson::where('title', 'The Virtue of Christian Fortitude')->first();
        $this->assertNotNull($fortitudeLesson);
        $this->assertCount(3, $fortitudeLesson->summary_takeaways);
        $this->assertEquals('CCC 1808', $fortitudeLesson->catechism_citations);
    }

    public function test_lesson_preview_and_study_hub_route_resolution(): void
    {
        $this->actingAs($this->superAdmin);

        $lesson = Lesson::create([
            'category_id' => $this->category->id,
            'title' => 'The Holy Rosary: Contemplating the Face of Christ',
            'slug' => 'the-holy-rosary-contemplating-christ',
            'subheading' => 'A Scriptural prayer journey through the mysteries of our salvation.',
            'content_sections' => [['heading' => 'Introduction', 'body' => 'The Rosary is a contemplative prayer.']],
            'summary_takeaways' => ['The Rosary is centered on Christ.'],
            'estimated_read_minutes' => 5,
            'difficulty' => 1,
            'status' => 'published',
        ]);

        // 1. Direct lesson viewer route /lesson/{lesson}
        $response = $this->get(route('lesson.show', $lesson->id));
        $response->assertOk();
        $response->assertSee('The Holy Rosary: Contemplating the Face of Christ');

        // 2. StudyHub fallback when lesson UUID or slug is accessed via /study/{id}
        $redirectResponse = $this->get('/study/' . $lesson->id);
        $redirectResponse->assertRedirect(route('lesson.show', $lesson->id));

        // 3. StudyHub valid category integer or slug
        $catResponse = $this->get('/study/' . $this->category->id);
        $catResponse->assertOk();

        // 4. In-App Lesson Preview Modal on DioceseDashboard
        Livewire::test(DioceseDashboard::class)
            ->call('previewLesson', $lesson->id)
            ->assertSet('showLessonPreviewModal', true)
            ->assertSet('previewLessonId', $lesson->id)
            ->assertSee('The Holy Rosary: Contemplating the Face of Christ')
            ->call('closeLessonPreview')
            ->assertSet('showLessonPreviewModal', false)
            ->assertSet('previewLessonId', null);
    }

    public function test_super_admin_can_update_and_delete_track_qa_bank(): void
    {
        $this->actingAs($this->superAdmin);

        // Create questions in track
        $q1 = Question::create([
            'category_id' => $this->category->id,
            'level' => 1,
            'question_text' => 'What is the first book of the New Testament?',
            'options' => ['A' => 'Genesis', 'B' => 'Matthew', 'C' => 'Mark', 'D' => 'Romans'],
            'correct_option_key' => 'B',
            'explanation' => 'The Gospel of Matthew is first in the NT canon.',
            'is_active' => true,
        ]);

        $q2 = Question::create([
            'category_id' => $this->category->id,
            'level' => 1,
            'question_text' => 'Who is the patron of the universal Church?',
            'options' => ['A' => 'St. Joseph', 'B' => 'St. Peter', 'C' => 'St. Francis', 'D' => 'St. Paul'],
            'correct_option_key' => 'A',
            'explanation' => 'St. Joseph is the patron of the universal Catholic Church.',
            'is_active' => true,
        ]);

        $this->assertEquals(2, Question::where('category_id', $this->category->id)->where('level', 1)->count());

        // 1. Toggle active state
        Livewire::test(DioceseDashboard::class)
            ->call('toggleTrackQuestionsActive', $this->category->id, 1);

        $this->assertEquals(0, Question::where('category_id', $this->category->id)->where('level', 1)->where('is_active', true)->count());

        // 2. Open & Save Track Q&A Management (Update metadata & reassign level)
        Livewire::test(DioceseDashboard::class)
            ->call('openManageTrackModal', $this->category->id, 1)
            ->assertSet('showManageTrackModal', true)
            ->set('manageTrackName', 'Sacred Scripture & Holy Gospels')
            ->set('manageTargetLevel', 2)
            ->set('manageBatchActiveAction', 'activate_all')
            ->call('saveTrackQAManagement')
            ->assertHasNoErrors()
            ->assertSet('showManageTrackModal', false);

        $this->assertEquals('Sacred Scripture & Holy Gospels', $this->category->fresh()->name);
        $this->assertEquals(2, Question::where('category_id', $this->category->id)->where('level', 2)->where('is_active', true)->count());

        // 3. Delete Track Questions
        Livewire::test(DioceseDashboard::class)
            ->call('deleteTrackQuestions', $this->category->id, 2);

        $this->assertEquals(0, Question::where('category_id', $this->category->id)->count());
    }

    public function test_super_admin_can_manage_track_qa_bank_via_arena_hub(): void
    {
        $this->actingAs($this->superAdmin);

        $q = Question::create([
            'category_id' => $this->category->id,
            'level' => 3,
            'question_text' => 'What ecumenical council defined papal infallibility in Pastor Aeternus?',
            'options' => ['A' => 'Trent', 'B' => 'Vatican I', 'C' => 'Vatican II', 'D' => 'Nicaea'],
            'correct_option_key' => 'B',
            'explanation' => 'First Vatican Council (1869-1870) defined papal infallibility.',
            'is_active' => true,
        ]);

        Livewire::test(ArenaHub::class)
            ->call('openManageTrackModal', $this->category->id, 3)
            ->assertSet('showManageTrackModal', true)
            ->set('manageTrackDescription', 'Comprehensive Catholic theology and scripture')
            ->call('saveTrackQAManagement')
            ->assertHasNoErrors();

        $this->assertEquals('Comprehensive Catholic theology and scripture', $this->category->fresh()->description);

        // Delete track questions
        Livewire::test(ArenaHub::class)
            ->call('deleteTrackQuestions', $this->category->id, 3);

        $this->assertDatabaseMissing('questions', ['id' => $q->id]);
    }

    public function test_super_admin_can_update_and_delete_track_lessons(): void
    {
        $this->actingAs($this->superAdmin);

        $l1 = Lesson::create([
            'category_id' => $this->category->id,
            'title' => 'Catholic Social Teaching: Solidarity',
            'slug' => 'cst-solidarity',
            'subheading' => 'We are one human family whatever our national or ideological differences.',
            'content_sections' => [['heading' => 'Solidarity', 'body' => 'Loving our neighbor has global dimensions.']],
            'summary_takeaways' => ['Universal brotherhood', 'Pursuit of justice'],
            'estimated_read_minutes' => 5,
            'difficulty' => 2,
            'status' => 'published',
        ]);

        $l2 = Lesson::create([
            'category_id' => $this->category->id,
            'title' => 'Catholic Social Teaching: Subsidiarity',
            'slug' => 'cst-subsidiarity',
            'subheading' => 'Matters ought to be handled by the smallest, lowest or least centralized competent authority.',
            'content_sections' => [['heading' => 'Subsidiarity', 'body' => 'Empowering local communities and families.']],
            'summary_takeaways' => ['Community empowerment', 'Dignity of civil society'],
            'estimated_read_minutes' => 5,
            'difficulty' => 2,
            'status' => 'published',
        ]);

        $this->assertEquals(2, Lesson::where('category_id', $this->category->id)->count());

        // 1. Toggle status
        Livewire::test(DioceseDashboard::class)
            ->call('toggleTrackLessonsStatus', $this->category->id);

        $this->assertEquals(0, Lesson::where('category_id', $this->category->id)->where('status', 'published')->count());

        // 2. Delete track lessons
        Livewire::test(DioceseDashboard::class)
            ->call('deleteTrackLessons', $this->category->id);

        $this->assertEquals(0, Lesson::where('category_id', $this->category->id)->count());
    }

    public function test_large_json_import_for_questions_and_lessons(): void
    {
        $this->actingAs($this->superAdmin);

        // Generate a large batch of 50 structured questions in JSON
        $questionsData = [];
        for ($i = 1; $i <= 50; $i++) {
            $uniqueHash = substr(md5("question_{$i}"), 0, 8);
            $questionsData[] = [
                'question_text' => "Catholic Theological Inquiry #{$i} [{$uniqueHash}]: How does scripture and tradition illuminate divine revelation in salvation history?",
                'option_a' => 'Through subjective human philosophies and cultural trends alone',
                'option_b' => 'Through the deposit of faith guarded faithfully by the Magisterium',
                'option_c' => 'Through individual private interpretations without biblical grounding',
                'option_d' => 'Through secular sociological surveys and popular consensus',
                'correct_option' => 'B',
                'explanation' => 'Sacred Scripture and Sacred Tradition form one sacred deposit of the Word of God (Dei Verbum 10).',
                'level' => 2,
                'track' => $this->category->name,
                'is_active' => true,
            ];
        }

        $jsonQuestions = json_encode(['questions' => $questionsData]);
        $largeFile = $this->createUploadedFile('large_catholic_questions.json', $jsonQuestions);

        Livewire::test(DioceseDashboard::class)
            ->set('importFile', $largeFile)
            ->set('importDuplicateStrategy', 'skip')
            ->call('processDynamicImport')
            ->assertHasNoErrors();

        $this->assertEquals(50, Question::where('category_id', $this->category->id)->count());

        // Generate a large batch of 30 structured lessons in JSON
        $lessonsData = [];
        for ($j = 1; $j <= 30; $j++) {
            $lessonsData[] = [
                'title' => "Formation Lesson #{$j}: The Mysteries of the Most Holy Rosary",
                'track' => $this->category->name,
                'subheading' => "Meditating on Christ life with Mary (Part {$j})",
                'content' => "The Rosary is a Scripture-based prayer that guides the faithful through the mysteries of redemption.",
                'summary_takeaways' => ['Joyful, Luminous, Sorrowful, and Glorious mysteries', 'Rooted in Sacred Scripture'],
                'estimated_read_minutes' => 6,
                'difficulty' => 2,
                'status' => 'published',
            ];
        }

        $jsonLessons = json_encode(['lessons' => $lessonsData]);
        $largeLessonFile = $this->createUploadedFile('large_catholic_lessons.json', $jsonLessons);

        Livewire::test(DioceseDashboard::class)
            ->set('lessonImportFile', $largeLessonFile)
            ->set('lessonImportDuplicateStrategy', 'skip')
            ->call('processLessonImport')
            ->assertHasNoErrors();

        $this->assertEquals(30, Lesson::where('category_id', $this->category->id)->count());
    }

    public function test_lesson_viewer_handles_string_and_associative_key_terms_and_sections(): void
    {
        $this->actingAs($this->superAdmin);

        // Create lesson with simple string arrays for key_terms (as imported from diverse JSON datasets)
        $lesson = Lesson::create([
            'category_id' => $this->category->id,
            'title' => 'The Four Marks of the Church',
            'slug' => 'the-four-marks-of-the-church',
            'subheading' => 'One, Holy, Catholic, and Apostolic',
            'summary_takeaways' => ['Unity', 'Holiness', 'Catholicity', 'Apostolicity'],
            'key_terms' => ['Magisterium: The teaching authority of the Church', 'Tradition', 'Papacy - Bishop of Rome'],
            'content_sections' => [
                ['heading' => 'One', 'body' => 'The Church is one because of her source, founder, and soul.'],
                'The Church is holy because Christ sanctified her with the Holy Spirit.',
            ],
            'estimated_read_minutes' => 5,
            'difficulty' => 1,
            'status' => 'published',
        ]);

        $response = $this->get("/lesson/{$lesson->id}");
        $response->assertStatus(200);
        $response->assertSee('The Four Marks of the Church');
        $response->assertSee('Magisterium');
        $response->assertSee('The teaching authority of the Church');
        $response->assertSee('Tradition');
        $response->assertSee('Papacy');
        $response->assertSee('Bishop of Rome');
    }

    public function test_super_admin_can_crud_lesson_with_series_identifier_and_progressive_linking(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(DioceseDashboard::class)
            ->call('openCreateLessonModal')
            ->set('lessonCategoryId', $this->category->id)
            ->set('lessonSeriesIdentifier', 'rosary-mysteries')
            ->set('lessonSeriesTitle', 'Mysteries of the Holy Rosary')
            ->set('lessonSeriesOrder', 1)
            ->set('lessonIsProgressive', true)
            ->set('lessonTitle', 'Joyful Mysteries (Part 1)')
            ->set('lessonSubheading', 'From the Annunciation to the Finding in the Temple')
            ->set('lessonContent', 'The Joyful Mysteries are marked by the joy radiating from the event of the Incarnation.')
            ->set('lessonTakeaways', "Annunciation\nVisitation\nNativity\nPresentation\nFinding in the Temple")
            ->set('lessonReadMinutes', 6)
            ->set('lessonDifficulty', 1)
            ->set('lessonStatus', 'published')
            ->call('saveLesson')
            ->assertHasNoErrors();

        $lessonPart1 = Lesson::where('title', 'Joyful Mysteries (Part 1)')->first();
        $this->assertNotNull($lessonPart1);
        $this->assertEquals('rosary-mysteries', $lessonPart1->series_identifier);
        $this->assertEquals('Mysteries of the Holy Rosary', $lessonPart1->series_title);
        $this->assertEquals(1, $lessonPart1->series_order);
        $this->assertTrue($lessonPart1->is_progressive);

        // Create Part 2 in same series
        Livewire::test(DioceseDashboard::class)
            ->call('openCreateLessonModal')
            ->set('lessonCategoryId', $this->category->id)
            ->set('lessonSeriesIdentifier', 'rosary-mysteries')
            ->set('lessonSeriesTitle', 'Mysteries of the Holy Rosary')
            ->set('lessonSeriesOrder', 2)
            ->set('lessonIsProgressive', true)
            ->set('lessonTitle', 'Luminous Mysteries (Part 2)')
            ->set('lessonSubheading', 'The Mysteries of Light')
            ->set('lessonContent', 'The public ministry of Christ brings the light of the Gospel to the world.')
            ->set('lessonTakeaways', "Baptism\nCana\nProclamation\nTransfiguration\nInstitution")
            ->set('lessonReadMinutes', 7)
            ->set('lessonDifficulty', 2)
            ->set('lessonStatus', 'published')
            ->call('saveLesson')
            ->assertHasNoErrors();

        $lessonPart2 = Lesson::where('title', 'Luminous Mysteries (Part 2)')->first();
        $this->assertNotNull($lessonPart2);
        $this->assertEquals(2, $lessonPart2->series_order);

        $youth = User::create([
            'parish_id' => $this->parish->id,
            'name' => 'Youth Tester',
            'email' => 'youth.series@example.com',
            'phone' => '+260971234567',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
        ]);

        // Verify progressive prerequisite checking
        $this->assertTrue($lessonPart1->arePrerequisitesMet($youth));
        $this->assertFalse($lessonPart2->arePrerequisitesMet($youth));

        // Mark Part 1 completed
        app(\App\Services\LearningProgressService::class)->completeLesson($youth, $lessonPart1);
        $this->assertTrue($lessonPart2->arePrerequisitesMet($youth));

        // Test viewer rendering shows series info and breadcrumbs
        $response = $this->actingAs($youth)->get("/lesson/{$lessonPart2->id}");
        $response->assertStatus(200);
        $response->assertSee('Mysteries of the Holy Rosary');
        $response->assertSee('Part 2 of 2');
    }

    public function test_dynamic_json_import_auto_detects_and_persists_series(): void
    {
        $this->actingAs($this->superAdmin);

        $jsonContent = json_encode([
            'lessons' => [
                [
                    'title' => 'Catholic Leadership (Part 1): Foundation',
                    'track' => $this->category->name,
                    'series_identifier' => 'catholic-leadership',
                    'series_title' => 'Catholic Youth Leadership',
                    'series_order' => 1,
                    'is_progressive' => true,
                    'content' => 'Humility and prayer are the cornerstones of authentic Catholic leadership.',
                ],
                [
                    'title' => 'Catholic Leadership (Part 2): Action',
                    'track' => $this->category->name,
                    'series_identifier' => 'catholic-leadership',
                    'series_title' => 'Catholic Youth Leadership',
                    'series_order' => 2,
                    'is_progressive' => true,
                    'content' => 'Mobilizing parish youth communities in apostolic service.',
                ],
            ]
        ]);

        $file = $this->createUploadedFile('series_lessons.json', $jsonContent);

        Livewire::test(DioceseDashboard::class)
            ->set('lessonImportFile', $file)
            ->set('lessonImportDuplicateStrategy', 'overwrite')
            ->call('processLessonImport')
            ->assertHasNoErrors();

        $part1 = Lesson::where('title', 'Catholic Leadership (Part 1): Foundation')->first();
        $part2 = Lesson::where('title', 'Catholic Leadership (Part 2): Action')->first();

        $this->assertNotNull($part1);
        $this->assertNotNull($part2);
        $this->assertEquals('catholic-leadership', $part1->series_identifier);
        $this->assertEquals('catholic-leadership', $part2->series_identifier);
        $this->assertEquals(1, $part1->series_order);
        $this->assertEquals(2, $part2->series_order);

        $next = app(\App\Services\LearningProgressService::class)->getNextLesson($part1);
        $this->assertEquals($part2->id, $next->id);
    }

    public function test_dynamic_json_import_supports_grouped_series_and_nested_metadata(): void
    {
        $this->actingAs($this->superAdmin);

        // Test format 1: Root series grouping
        $groupedJson = json_encode([
            'series' => [
                [
                    'series_identifier' => 'creed-doctrine',
                    'series_title' => 'The Nicene Creed in Depth',
                    'progressive' => true,
                    'track' => $this->category->name,
                    'lessons' => [
                        [
                            'title' => 'The Nicene Creed (Part 1): God the Father Almighty',
                            'content' => 'I believe in one God, the Father almighty, maker of heaven and earth.',
                        ],
                        [
                            'title' => 'The Nicene Creed (Part 2): Jesus Christ Son of God',
                            'content' => 'I believe in one Lord Jesus Christ, the Only Begotten Son of God.',
                        ],
                    ]
                ]
            ]
        ]);

        $file1 = $this->createUploadedFile('grouped_series.json', $groupedJson);

        Livewire::test(DioceseDashboard::class)
            ->set('lessonImportFile', $file1)
            ->set('lessonImportDuplicateStrategy', 'overwrite')
            ->call('processLessonImport')
            ->assertHasNoErrors();

        $creed1 = Lesson::where('title', 'The Nicene Creed (Part 1): God the Father Almighty')->first();
        $creed2 = Lesson::where('title', 'The Nicene Creed (Part 2): Jesus Christ Son of God')->first();

        $this->assertNotNull($creed1);
        $this->assertNotNull($creed2);
        $this->assertEquals('creed-doctrine', $creed1->series_identifier);
        $this->assertEquals('The Nicene Creed in Depth', $creed1->series_title);
        $this->assertEquals(1, $creed1->series_order);
        $this->assertTrue($creed1->is_progressive);

        $this->assertEquals('creed-doctrine', $creed2->series_identifier);
        $this->assertEquals('The Nicene Creed in Depth', $creed2->series_title);
        $this->assertEquals(2, $creed2->series_order);

        // Test format 2: Nested series object in lesson row
        $nestedJson = json_encode([
            'lessons' => [
                [
                    'title' => 'Holy Orders: Presbyterate',
                    'track' => $this->category->name,
                    'content' => 'Priests are consecrated to preach the Gospel and shepherd the faithful.',
                    'series' => [
                        'identifier' => 'holy-orders-hierarchy',
                        'title' => 'Holy Orders & Hierarchy',
                        'order' => 2,
                        'is_progressive' => true,
                    ]
                ]
            ]
        ]);

        $file2 = $this->createUploadedFile('nested_series.json', $nestedJson);

        Livewire::test(DioceseDashboard::class)
            ->set('lessonImportFile', $file2)
            ->set('lessonImportDuplicateStrategy', 'overwrite')
            ->call('processLessonImport')
            ->assertHasNoErrors();

        $holyOrdersLesson = Lesson::where('title', 'Holy Orders: Presbyterate')->first();
        $this->assertNotNull($holyOrdersLesson);
        $this->assertEquals('holy-orders-hierarchy', $holyOrdersLesson->series_identifier);
        $this->assertEquals('Holy Orders & Hierarchy', $holyOrdersLesson->series_title);
        $this->assertEquals(2, $holyOrdersLesson->series_order);
        $this->assertTrue($holyOrdersLesson->is_progressive);
    }

    public function test_chairperson_admin_full_crud_in_diocese_dashboard(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Chairperson via DioceseDashboard
        Livewire::test(DioceseDashboard::class)
            ->call('openCreateAdminModal')
            ->assertSet('showAdminModal', true)
            ->assertSet('editAdminId', null)
            ->set('newAdminParishId', $this->parish->id)
            ->set('newAdminName', 'Clement Nyambe')
            ->set('newAdminPhone', '+260971234567')
            ->set('newAdminEmail', 'clement.nyambe@parish.org')
            ->set('newAdminRole', 'chairperson')
            ->set('newAdminStatus', 'approved')
            ->set('newAdminPassword', 'SecurePassword123!')
            ->call('saveAdmin')
            ->assertHasNoErrors()
            ->assertSet('showAdminModal', false);

        $admin = User::where('email', 'clement.nyambe@parish.org')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('Clement Nyambe', $admin->name);
        $this->assertEquals('+260971234567', $admin->phone);
        $this->assertEquals('chairperson', $admin->role);
        $this->assertEquals('approved', $admin->status);
        $this->assertEquals($this->parish->id, $admin->parish_id);

        // 2. Edit Chairperson via DioceseDashboard
        Livewire::test(DioceseDashboard::class)
            ->call('editAdmin', $admin->id)
            ->assertSet('showAdminModal', true)
            ->assertSet('editAdminId', $admin->id)
            ->assertSet('newAdminName', 'Clement Nyambe')
            ->set('newAdminName', 'Clement Nyambe - Updated')
            ->set('newAdminRole', 'deanery_admin')
            ->call('saveAdmin')
            ->assertHasNoErrors()
            ->assertSet('showAdminModal', false);

        $admin->refresh();
        $this->assertEquals('Clement Nyambe - Updated', $admin->name);
        $this->assertEquals('deanery_admin', $admin->role);

        // 3. Toggle Status (Suspend / Activate)
        Livewire::test(DioceseDashboard::class)
            ->call('toggleAdminStatus', $admin->id);

        $this->assertEquals('rejected', $admin->fresh()->status);

        Livewire::test(DioceseDashboard::class)
            ->call('toggleAdminStatus', $admin->id);

        $this->assertEquals('approved', $admin->fresh()->status);

        // 4. Delete Admin
        Livewire::test(DioceseDashboard::class)
            ->call('deleteAdmin', $admin->id);

        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }
}
