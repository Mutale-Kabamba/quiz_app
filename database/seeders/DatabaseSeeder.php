<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\DailyChallenge;
use App\Models\Deanery;
use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\Parish;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Deaneries & Parishes
        $this->call(DioceseParishesAndDeaneriesSeeder::class);
        $this->call(SaintsAndAfricanHeritageSeeder::class);

        $cathedralParish = Parish::where('name', 'St. Theresa Cathedral Parish')->first()
            ?? Parish::firstOrCreate(
                ['name' => 'St. Theresa Cathedral Parish'],
                ['location' => 'Livingstone Town', 'code' => 'ST-THERESA-CATHEDRAL']
            );

        // 2. Seed Users
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@livingstonediocese.org'],
            [
                'name' => 'Diocesan Super Admin',
                'phone' => '+260970000001',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'status' => 'approved',
                'xp' => 1500,
                'level' => 4,
                'current_streak' => 12,
                'longest_streak' => 15,
                'approved_at' => now(),
            ]
        );

        $chairperson = User::firstOrCreate(
            ['email' => 'chairperson@livingstonediocese.org'],
            [
                'parish_id' => $cathedralParish->id,
                'name' => "St. Theresa Youth Chairperson",
                'phone' => '+260970000002',
                'password' => Hash::make('password'),
                'role' => 'chairperson',
                'status' => 'approved',
                'xp' => 850,
                'level' => 3,
                'current_streak' => 7,
                'longest_streak' => 10,
                'approved_at' => now(),
            ]
        );

        $approvedYouth = User::firstOrCreate(
            ['email' => 'mutale@example.com'],
            [
                'parish_id' => $cathedralParish->id,
                'name' => 'Mutale Mwamba',
                'phone' => '+260970000003',
                'password' => Hash::make('password'),
                'role' => 'youth',
                'status' => 'approved',
                'approved_by' => $chairperson->id,
                'approved_at' => now(),
                'xp' => 0,
                'level' => 1,
                'current_streak' => 0,
                'longest_streak' => 0,
                'last_activity_date' => null,
            ]
        );

        $pendingYouth = User::firstOrCreate(
            ['email' => 'chileshe@example.com'],
            [
                'parish_id' => $cathedralParish->id,
                'name' => 'Chileshe Bwalya',
                'phone' => '+260970000004',
                'password' => Hash::make('password'),
                'role' => 'youth',
                'status' => 'pending',
                'xp' => 0,
                'level' => 1,
                'current_streak' => 0,
                'longest_streak' => 0,
            ]
        );

        // 3. Seed Categories
        $scripture = Category::firstOrCreate(['slug' => 'holy-scripture'], [
            'name' => 'Holy Scripture',
            'description' => 'Old and New Testament, Gospels, Catholic Epistles & Biblical History',
            'icon' => 'heroicon-o-book-open',
            'display_order' => 1,
        ]);

        $youcat = Category::firstOrCreate(['slug' => 'youcat'], [
            'name' => 'YOUCAT (Youth Catechism)',
            'description' => 'What We Believe, How We Celebrate, Life in Christ & How We Pray',
            'icon' => 'heroicon-o-sparkles',
            'display_order' => 2,
        ]);

        $docat = Category::firstOrCreate(['slug' => 'docat'], [
            'name' => 'DOCAT (Catholic Social Teaching)',
            'description' => 'Human Dignity, Common Good, Family, Work, Economy & Laudato Si',
            'icon' => 'heroicon-o-globe-alt',
            'display_order' => 3,
        ]);

        $doctrine = Category::firstOrCreate(['slug' => 'catechesis-and-doctrine'], [
            'name' => 'Catechesis & Catholic Doctrine (CCC)',
            'description' => 'Sacraments, Liturgy, Marian Dogmas & Church Precepts',
            'icon' => 'heroicon-o-academic-cap',
            'display_order' => 4,
        ]);

        $history = Category::firstOrCreate(['slug' => 'church-history'], [
            'name' => 'Church History & African Heritage',
            'description' => 'African Saints, Zambian Catholic Heritage & Livingstone Diocese History',
            'icon' => 'heroicon-o-building-library',
            'display_order' => 5,
        ]);

        // 4. Seed Standard Achievements
        Achievement::firstOrCreate(['code' => 'first_lesson'], [
            'title' => 'First Step of Faith',
            'description' => 'Complete your first Catholic catechetical study lesson.',
            'icon' => '🌱',
            'type' => 'lesson_count',
            'threshold' => 1,
            'xp_reward' => 50,
        ]);

        Achievement::firstOrCreate(['code' => 'scripture_pillar'], [
            'title' => 'Scripture Pillar',
            'description' => 'Complete 5 Bible and Scripture study lessons.',
            'icon' => '📜',
            'type' => 'lesson_count',
            'threshold' => 5,
            'xp_reward' => 100,
        ]);

        Achievement::firstOrCreate(['code' => 'streak_3'], [
            'title' => 'Faithful Disciple',
            'description' => 'Maintain an active daily formation streak for 3 consecutive days.',
            'icon' => '🔥',
            'type' => 'streak',
            'threshold' => 3,
            'xp_reward' => 75,
        ]);

        Achievement::firstOrCreate(['code' => 'streak_7'], [
            'title' => 'Streak Master',
            'description' => 'Maintain an active daily formation streak for 7 consecutive days.',
            'icon' => '⭐',
            'type' => 'streak',
            'threshold' => 7,
            'xp_reward' => 150,
        ]);

        Achievement::firstOrCreate(['code' => 'flashcard_master'], [
            'title' => 'Catechism Memorizer',
            'description' => 'Review 10 flashcard concepts in the Spaced Review Arena.',
            'icon' => '💡',
            'type' => 'flashcard_count',
            'threshold' => 10,
            'xp_reward' => 100,
        ]);

        Achievement::firstOrCreate(['code' => 'quiz_champion'], [
            'title' => 'Diocesan Competitor',
            'description' => 'Complete 5 Ranked competition quizzes.',
            'icon' => '🏆',
            'type' => 'quiz_count',
            'threshold' => 5,
            'xp_reward' => 200,
        ]);
    }
}
