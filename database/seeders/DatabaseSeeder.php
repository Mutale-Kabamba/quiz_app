<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\Question;
use App\Models\StudyNote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Deaneries & Parishes
        $livingstoneDeanery = Deanery::create(['name' => 'Livingstone Deanery', 'code' => 'LIV']);
        $kazungulaDeanery = Deanery::create(['name' => 'Kazungula Deanery', 'code' => 'KAZ']);
        $seshekeDeanery = Deanery::create(['name' => 'Sesheke Deanery', 'code' => 'SES']);
        $senangaDeanery = Deanery::create(['name' => 'Senanga Deanery', 'code' => 'SEN']);
        $siomaDeanery = Deanery::create(['name' => 'Sioma Deanery', 'code' => 'SIO']);

        $cathedralParish = Parish::create([
            'deanery_id' => $livingstoneDeanery->id,
            'name' => "St. Theresa's Cathedral",
            'location' => 'Livingstone Town',
        ]);

        $holyCrossParish = Parish::create([
            'deanery_id' => $livingstoneDeanery->id,
            'name' => 'Holy Cross Parish',
            'location' => 'Maramba, Livingstone',
        ]);

        $kazungulaParish = Parish::create([
            'deanery_id' => $kazungulaDeanery->id,
            'name' => 'St. Joseph Catholic Parish',
            'location' => 'Kazungula Boma',
        ]);

        // 2. Seed Users
        $superAdmin = User::create([
            'name' => 'Diocesan Super Admin',
            'email' => 'admin@livingstonediocese.org',
            'phone' => '+260970000001',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $chairperson = User::create([
            'parish_id' => $cathedralParish->id,
            'name' => "St. Theresa Youth Chairperson",
            'email' => 'chairperson@livingstonediocese.org',
            'phone' => '+260970000002',
            'password' => Hash::make('password'),
            'role' => 'chairperson',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $approvedYouth = User::create([
            'parish_id' => $cathedralParish->id,
            'name' => 'Mutale Mwamba',
            'email' => 'mutale@example.com',
            'phone' => '+260970000003',
            'password' => Hash::make('password'),
            'role' => 'youth',
            'status' => 'approved',
            'approved_by' => $chairperson->id,
            'approved_at' => now(),
        ]);

        $pendingYouth = User::create([
            'parish_id' => $cathedralParish->id,
            'name' => 'Chileshe Bwalya',
            'email' => 'chileshe@example.com',
            'phone' => '+260970000004',
            'password' => Hash::make('password'),
            'role' => 'youth',
            'status' => 'pending',
        ]);

        // 3. Seed Categories
        $scripture = Category::create([
            'name' => 'Holy Scripture',
            'slug' => 'holy-scripture',
            'description' => 'Old and New Testament, Gospels, Catholic Epistles & Biblical History',
            'icon' => 'heroicon-o-book-open',
            'display_order' => 1,
        ]);

        $youcat = Category::create([
            'name' => 'YOUCAT (Youth Catechism)',
            'slug' => 'youcat',
            'description' => 'What We Believe, How We Celebrate, Life in Christ & How We Pray',
            'icon' => 'heroicon-o-sparkles',
            'display_order' => 2,
        ]);

        $docat = Category::create([
            'name' => 'DOCAT (Catholic Social Teaching)',
            'slug' => 'docat',
            'description' => 'Human Dignity, Common Good, Family, Work, Economy & Laudato Si',
            'icon' => 'heroicon-o-globe-alt',
            'display_order' => 3,
        ]);

        $doctrine = Category::create([
            'name' => 'Catechesis & Catholic Doctrine (CCC)',
            'slug' => 'catechesis-and-doctrine',
            'description' => 'Sacraments, Liturgy, Marian Dogmas & Church Precepts',
            'icon' => 'heroicon-o-academic-cap',
            'display_order' => 4,
        ]);

        $history = Category::create([
            'name' => 'Church History & African Heritage',
            'slug' => 'church-history',
            'description' => 'African Saints, Zambian Catholic Heritage & Livingstone Diocese History',
            'icon' => 'heroicon-o-building-library',
            'display_order' => 5,
        ]);

        // 4. Seed Questions
        Question::create([
            'category_id' => $scripture->id,
            'level' => 1,
            'question_text' => 'How many books are in the Catholic Old Testament Canon?',
            'options' => [
                'A' => '39 books',
                'B' => '46 books',
                'C' => '27 books',
                'D' => '73 books',
            ],
            'correct_option_key' => 'B',
            'explanation' => 'The Catholic Old Testament contains 46 books, including the 7 deuterocanonical books (Tobit, Judith, 1 & 2 Maccabees, Wisdom, Sirach, Baruch).',
            'reference_citation' => 'CCC #120',
            'is_active' => true,
        ]);

        Question::create([
            'category_id' => $youcat->id,
            'level' => 1,
            'question_text' => 'According to YOUCAT, what is the ultimate purpose of human life?',
            'options' => [
                'A' => 'To acquire wealth and honor',
                'B' => 'To know and love God, to do good, and to go to heaven',
                'C' => 'To live in isolation from others',
                'D' => 'To seek physical comfort only',
            ],
            'correct_option_key' => 'B',
            'explanation' => 'God made us to know Him, to love Him, to do good according to His will, and to go one day to heaven.',
            'reference_citation' => 'YOUCAT #1',
            'is_active' => true,
        ]);

        Question::create([
            'category_id' => $docat->id,
            'level' => 2,
            'question_text' => 'Which of the following is the central foundational pillar of Catholic Social Teaching in DOCAT?',
            'options' => [
                'A' => 'Economic profit',
                'B' => 'Human Dignity',
                'C' => 'Political dominance',
                'D' => 'Technological advancement',
            ],
            'correct_option_key' => 'B',
            'explanation' => 'Human dignity is the foundation of all Catholic Social Teaching because every human person is created in the image and likeness of God.',
            'reference_citation' => 'DOCAT #47',
            'is_active' => true,
        ]);

        Question::create([
            'category_id' => $history->id,
            'level' => 2,
            'question_text' => 'Which early African martyr and Doctor of the Church hailed from Hippo in North Africa?',
            'options' => [
                'A' => 'St. Augustine',
                'B' => 'St. Thomas Aquinas',
                'C' => 'St. Francis of Assisi',
                'D' => 'St. Patrick',
            ],
            'correct_option_key' => 'A',
            'explanation' => 'St. Augustine was Bishop of Hippo in modern-day Algeria and is one of the greatest Doctors of the Western Church.',
            'reference_citation' => 'Church History / Patristics',
            'is_active' => true,
        ]);

        // 5. Seed Study Notes
        StudyNote::create([
            'category_id' => $youcat->id,
            'title' => 'The Sacraments of Initiation',
            'subheading' => 'Baptism, Confirmation, and the Holy Eucharist',
            'content_body' => "Christian initiation is accomplished through three sacraments together: Baptism which is the beginning of new life; Confirmation which strengthens that life; and the Eucharist which nourishes the disciple with Christ's Body and Blood for His transformation.",
            'reference_code' => 'YOUCAT-SAC-01',
            'estimated_read_minutes' => 4,
        ]);
    }
}
