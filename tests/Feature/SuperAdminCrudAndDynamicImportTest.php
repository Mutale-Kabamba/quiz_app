<?php

namespace Tests\Feature;

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

    public function test_dynamic_csv_import_parses_and_inserts_questions(): void
    {
        $this->actingAs($this->superAdmin);

        $csvContent = "question_text,option_a,option_b,option_c,option_d,correct_option,explanation,reference_citation,difficulty,track\n"
            . "\"What is the first Sacrament of Christian Initiation?\",\"Confirmation\",\"Baptism\",\"Eucharist\",\"Holy Orders\",\"B\",\"Baptism cleanses original sin and incorporates us into Christ.\",\"CCC 1213\",\"EASY\",\"Holy Scripture & Gospels\"\n"
            . "\"How many gifts of the Holy Spirit are listed in Isaiah 11?\",\"Five\",\"Six\",\"Seven\",\"Twelve\",\"C\",\"The Seven Gifts of the Holy Spirit are wisdom, understanding, counsel, fortitude, knowledge, piety, and fear of the Lord.\",\"CCC 1831, Isaiah 11:1-3\",\"MEDIUM\",\"Holy Scripture & Gospels\"\n";

        $file = UploadedFile::fake()->createWithContent('custom_questions.csv', $csvContent);

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
        $file2 = UploadedFile::fake()->createWithContent('custom_questions_2.csv', $csvContent);
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

        $file = UploadedFile::fake()->createWithContent('questions.json', $jsonContent);

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

        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_test') . '.xlsx';
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

        $xlsxContent = file_get_contents($tempPath);
        $file = UploadedFile::fake()->createWithContent('questions.xlsx', $xlsxContent);

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

        $file = UploadedFile::fake()->createWithContent('custom_lessons.csv', $csvContent);

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

        $jsonFile = UploadedFile::fake()->createWithContent('custom_lessons.json', $jsonContent);

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
    }
}


