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
        $livingstoneDeanery = Deanery::firstOrCreate(['code' => 'LIV'], ['name' => 'Livingstone Deanery']);
        $kazungulaDeanery = Deanery::firstOrCreate(['code' => 'KAZ'], ['name' => 'Kazungula Deanery']);
        $seshekeDeanery = Deanery::firstOrCreate(['code' => 'SES'], ['name' => 'Sesheke Deanery']);
        $senangaDeanery = Deanery::firstOrCreate(['code' => 'SEN'], ['name' => 'Senanga Deanery']);
        $siomaDeanery = Deanery::firstOrCreate(['code' => 'SIO'], ['name' => 'Sioma Deanery']);

        $cathedralParish = Parish::firstOrCreate(
            ['deanery_id' => $livingstoneDeanery->id, 'name' => "St. Theresa's Cathedral"],
            ['location' => 'Livingstone Town']
        );

        $holyCrossParish = Parish::firstOrCreate(
            ['deanery_id' => $livingstoneDeanery->id, 'name' => 'Holy Cross Parish'],
            ['location' => 'Maramba, Livingstone']
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
                'xp' => 380,
                'level' => 2,
                'current_streak' => 5,
                'longest_streak' => 8,
                'last_activity_date' => now(),
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
                'xp' => 50,
                'level' => 1,
                'current_streak' => 1,
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

        // 4. Seed Structured Lessons
        $lessonBaptism = Lesson::firstOrCreate(['slug' => 'sacrament-of-holy-baptism'], [
            'category_id' => $doctrine->id,
            'title' => 'The Sacrament of Holy Baptism: Doorway to Divine Life',
            'subheading' => 'Foundations of Christian Initiation in the Catholic Church',
            'summary_takeaways' => [
                'Baptism is the basis of the whole Christian life and the gateway to life in the Spirit.',
                'It frees us from Original Sin and all personal sins, making us adopted children of God.',
                'The essential rite consists of immersing in water or pouring water over the head while invoking the Holy Trinity.',
                'Baptism imprints an indelible spiritual mark (character) that cannot be erased.',
            ],
            'content_sections' => [
                [
                    'heading' => 'What is Holy Baptism?',
                    'body' => 'Holy Baptism is the basis of the whole Christian life, the gateway to life in the Spirit (vitae spiritualis ianua), and the door which gives access to the other sacraments. Through Baptism we are freed from sin and reborn as sons of God; we become members of Christ, are incorporated into the Church and made sharers in her mission.',
                    'scripture_quote' => '"Go therefore and make disciples of all nations, baptizing them in the name of the Father and of the Son and of the Holy Spirit." (Matthew 28:19)',
                    'catechism_quote' => 'CCC 1213: Baptism is the sacrament of regeneration through water in the word.',
                ],
                [
                    'heading' => 'The Effects and Graces of Baptism',
                    'body' => 'The two principal effects of Baptism are purification from sins and new birth in the Holy Spirit. By Baptism, all sins are forgiven: original sin and all personal sins, as well as all punishment for sin. The baptized person is anointed with the Holy Spirit and incorporated into the Body of Christ.',
                    'scripture_quote' => '"Do you not know that all of us who have been baptized into Christ Jesus were baptized into his death? We were buried therefore with him by baptism into death, so that as Christ was raised from the dead by the glory of the Father, we too might walk in newness of life." (Romans 6:3-4)',
                    'catechism_quote' => 'YOUCAT 194: Baptism incorporates us into the death of Christ on the Cross and raises us with Him.',
                ],
            ],
            'key_terms' => [
                ['term' => 'Sacramental Character', 'definition' => 'An indelible spiritual seal imprinted on the soul by Baptism, Confirmation, and Holy Orders that can never be repeated.'],
                ['term' => 'Original Sin', 'definition' => 'The fallen state of human nature into which all people are born, inherited from our first parents.'],
                ['term' => 'Catechumen', 'definition' => 'A person preparing for baptism and initiation into the Catholic Church.'],
            ],
            'estimated_read_minutes' => 5,
            'difficulty' => 1,
            'scripture_citations' => 'Matthew 28:19, Romans 6:3-4, John 3:5',
            'catechism_citations' => 'CCC 1213-1216, YOUCAT 194-200',
            'display_order' => 1,
            'status' => 'published',
        ]);

        $lessonDignity = Lesson::firstOrCreate(['slug' => 'human-dignity-docat'], [
            'category_id' => $docat->id,
            'title' => 'Human Dignity: The Foundation of Catholic Social Teaching',
            'subheading' => 'Why Every Person Has Infinite Worth in DOCAT',
            'summary_takeaways' => [
                'Every human person is created in the image and likeness of God (Imago Dei).',
                'Human dignity is intrinsic, universal, and inalienable—it cannot be earned, bought, or revoked.',
                'The measure of any society is how it protects and values the most vulnerable.',
            ],
            'content_sections' => [
                [
                    'heading' => 'Imago Dei: In God’s Image',
                    'body' => 'God did not create humanity as an afterthought or a commodity. Every single person—from conception until natural death—possesses inviolable dignity because God loved each person into existence and redeemed them through Jesus Christ.',
                    'scripture_quote' => '"So God created man in his own image, in the image of God he created him; male and female he created them." (Genesis 1:27)',
                    'catechism_quote' => 'DOCAT 47: The dignity of the human person is rooted in his or her creation in the image and likeness of God.',
                ],
            ],
            'key_terms' => [
                ['term' => 'Inalienable', 'definition' => 'Cannot be taken away or transferred under any circumstances.'],
                ['term' => 'Common Good', 'definition' => 'The sum total of social conditions which allow people, either as groups or as individuals, to reach their fulfillment more fully.'],
            ],
            'estimated_read_minutes' => 4,
            'difficulty' => 2,
            'scripture_citations' => 'Genesis 1:26-27, Luke 10:25-37',
            'catechism_citations' => 'DOCAT 47-52, CCC 1700-1702',
            'display_order' => 1,
            'status' => 'published',
        ]);

        $lessonAugustine = Lesson::firstOrCreate(['slug' => 'st-augustine-african-heritage'], [
            'category_id' => $history->id,
            'title' => 'St. Augustine of Hippo: Champion of Grace and African Patristics',
            'subheading' => 'The Journey of One of the Greatest Doctors of the Church',
            'summary_takeaways' => [
                'Born in Tagaste (modern Algeria) in 354 AD, son of St. Monica.',
                'Underwent a profound intellectual and spiritual conversion in Milan through prayer and reading Romans 13:13-14.',
                'Served as Bishop of Hippo for 35 years and authored Confessions and The City of God.',
            ],
            'content_sections' => [
                [
                    'heading' => 'The Restless Heart and Mother’s Tears',
                    'body' => 'For years, Augustine searched for truth in philosophies and worldly pleasures. His mother, St. Monica, prayed persistently with tears for his conversion. His famous autobiography, The Confessions, documents his turning to Christ.',
                    'scripture_quote' => '"Let us behave decently, as in the daytime, not in carousing and drunkenness... rather, clothe yourselves with the Lord Jesus Christ." (Romans 13:13-14)',
                    'catechism_quote' => 'CCC 30: "You are great, Lord, and highly to be praised... you have made us for yourself, and our heart is restless until it rests in you."',
                ],
            ],
            'key_terms' => [
                ['term' => 'Doctor of the Church', 'definition' => 'A saint whose theological writings have significantly enlightened the universal Church.'],
                ['term' => 'Patristics', 'definition' => 'The study of the early Church Fathers and their theological heritage.'],
            ],
            'estimated_read_minutes' => 4,
            'difficulty' => 2,
            'scripture_citations' => 'Romans 13:13-14, Psalm 63:1',
            'catechism_citations' => 'CCC 30, CCC 1718',
            'display_order' => 1,
            'status' => 'published',
        ]);

        // 5. Seed Flashcards
        Flashcard::firstOrCreate(['front_text' => 'What are the three Sacraments of Christian Initiation?'], [
            'category_id' => $doctrine->id,
            'lesson_id' => $lessonBaptism->id,
            'back_text' => 'Baptism, Confirmation, and the Holy Eucharist.',
            'reference_citation' => 'CCC #1212 / YOUCAT #193',
            'difficulty' => 1,
            'status' => 'published',
        ]);

        Flashcard::firstOrCreate(['front_text' => 'What is the essential matter and form of Baptism?'], [
            'category_id' => $doctrine->id,
            'lesson_id' => $lessonBaptism->id,
            'back_text' => 'Matter: Pure water. Form: "I baptize you in the name of the Father, and of the Son, and of the Holy Spirit."',
            'reference_citation' => 'CCC #1240 / Matthew 28:19',
            'difficulty' => 1,
            'status' => 'published',
        ]);

        Flashcard::firstOrCreate(['front_text' => 'What is the foundational principle of all Catholic Social Teaching in DOCAT?'], [
            'category_id' => $docat->id,
            'lesson_id' => $lessonDignity->id,
            'back_text' => 'Human Dignity, because every person is created in the image and likeness of God.',
            'reference_citation' => 'DOCAT #47 / Genesis 1:27',
            'difficulty' => 2,
            'status' => 'published',
        ]);

        Flashcard::firstOrCreate(['front_text' => 'Where was St. Augustine born and where did he serve as Bishop?'], [
            'category_id' => $history->id,
            'lesson_id' => $lessonAugustine->id,
            'back_text' => 'Born in Tagaste (modern Algeria) and served as Bishop of Hippo Regius in North Africa.',
            'reference_citation' => 'African Patristics / Church History',
            'difficulty' => 2,
            'status' => 'published',
        ]);

        Flashcard::firstOrCreate(['front_text' => 'How many books are contained in the Catholic Bible Canon?'], [
            'category_id' => $scripture->id,
            'back_text' => '73 books in total (46 in the Old Testament, 27 in the New Testament).',
            'reference_citation' => 'CCC #120',
            'difficulty' => 1,
            'status' => 'published',
        ]);

        // 6. Seed Questions
        $q1 = Question::firstOrCreate(['question_text' => 'How many books are in the Catholic Old Testament Canon?'], [
            'category_id' => $scripture->id,
            'level' => 1,
            'options' => ['A' => '39 books', 'B' => '46 books', 'C' => '27 books', 'D' => '73 books'],
            'correct_option_key' => 'B',
            'explanation' => 'The Catholic Old Testament contains 46 books, including the 7 deuterocanonical books (Tobit, Judith, 1 & 2 Maccabees, Wisdom, Sirach, Baruch).',
            'reference_citation' => 'CCC #120',
            'is_active' => true,
        ]);

        $q2 = Question::firstOrCreate(['question_text' => 'According to YOUCAT, what is the ultimate purpose of human life?'], [
            'category_id' => $youcat->id,
            'level' => 1,
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

        $q3 = Question::firstOrCreate(['question_text' => 'Which of the following is the central foundational pillar of Catholic Social Teaching in DOCAT?'], [
            'category_id' => $docat->id,
            'level' => 2,
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

        $q4 = Question::firstOrCreate(['question_text' => 'Which early African martyr and Doctor of the Church hailed from Hippo in North Africa?'], [
            'category_id' => $history->id,
            'level' => 2,
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

        $q5 = Question::firstOrCreate(['question_text' => 'Which three sacraments constitute the Sacraments of Christian Initiation?'], [
            'category_id' => $doctrine->id,
            'level' => 1,
            'options' => [
                'A' => 'Baptism, Confirmation, and Eucharist',
                'B' => 'Baptism, Confession, and Holy Orders',
                'C' => 'Confirmation, Matrimony, and Anointing',
                'D' => 'Eucharist, Confession, and Baptism',
            ],
            'correct_option_key' => 'A',
            'explanation' => 'Christian initiation is accomplished through Baptism, Confirmation, and the Holy Eucharist together.',
            'reference_citation' => 'CCC #1212',
            'is_active' => true,
        ]);

        // 7. Seed Daily Challenge for Today
        DailyChallenge::firstOrCreate(['challenge_date' => now()->toDateString()], [
            'title' => 'Daily Catholic Formation Challenge',
            'description' => 'Test your daily knowledge in Scripture, YOUCAT and the Sacraments to keep your streak alive!',
            'question_ids' => [$q1->id, $q2->id, $q3->id, $q4->id, $q5->id],
            'xp_reward' => 50,
            'is_active' => true,
        ]);

        // 8. Seed Standard Achievements
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
