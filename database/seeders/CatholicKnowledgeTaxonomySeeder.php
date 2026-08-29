<?php

namespace Database\Seeders;

use App\Models\AgeBand;
use App\Models\BloomTaxonomy;
use App\Models\CatholicSource;
use App\Models\FormationLevel;
use App\Models\SaintProfile;
use App\Models\TaxonomyCategory;
use App\Models\TaxonomyConcept;
use App\Models\TaxonomyDomain;
use App\Models\TaxonomyTopic;
use App\Models\TaxonomyTrack;
use App\Services\KnowledgeTaxonomyService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatholicKnowledgeTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Formation Levels (1 to 6)
        $levels = [
            ['level_number' => 1, 'name' => 'Foundation', 'code' => 'foundation', 'description' => 'Basic Catholic Knowledge', 'min_xp_required' => 0],
            ['level_number' => 2, 'name' => 'Beginner', 'code' => 'beginner', 'description' => 'Core Catechetical Concepts', 'min_xp_required' => 200],
            ['level_number' => 3, 'name' => 'Intermediate', 'code' => 'intermediate', 'description' => 'Broader Understanding & Scripture', 'min_xp_required' => 800],
            ['level_number' => 4, 'name' => 'Advanced', 'code' => 'advanced', 'description' => 'Detailed Doctrinal & Historical Knowledge', 'min_xp_required' => 2000],
            ['level_number' => 5, 'name' => 'Expert', 'code' => 'expert', 'description' => 'Deep Theological & Catechetical Insight', 'min_xp_required' => 5000],
            ['level_number' => 6, 'name' => 'Competition', 'code' => 'competition', 'description' => 'High-Difficulty Tournament & Rally Questions', 'min_xp_required' => 10000],
        ];
        foreach ($levels as $lvl) {
            FormationLevel::updateOrCreate(['level_number' => $lvl['level_number']], $lvl);
        }

        // 2. Age Bands
        $ageBands = [
            ['code' => 'children', 'name' => 'Children (Holy Childhood)', 'min_age' => 6, 'max_age' => 11],
            ['code' => 'early_teens', 'name' => 'Early Teens (Junior Youth)', 'min_age' => 12, 'max_age' => 14],
            ['code' => 'teens', 'name' => 'Teens (High School Youth)', 'min_age' => 15, 'max_age' => 18],
            ['code' => 'youth', 'name' => 'Youth (Diocesan Youth Ministry)', 'min_age' => 19, 'max_age' => 25],
            ['code' => 'young_adults', 'name' => 'Young Adults', 'min_age' => 26, 'max_age' => 35],
            ['code' => 'adult_formation', 'name' => 'Adult Formation & Leaders', 'min_age' => 36, 'max_age' => 99],
        ];
        foreach ($ageBands as $band) {
            AgeBand::updateOrCreate(['code' => $band['code']], $band);
        }

        // 3. Bloom's Taxonomy Cognitive Dimensions
        $bloomLevels = [
            ['code' => 'remember', 'name' => 'Remember (Knowledge recall)', 'cognitive_order' => 1],
            ['code' => 'understand', 'name' => 'Understand (Comprehension)', 'cognitive_order' => 2],
            ['code' => 'apply', 'name' => 'Apply (Practical Christian living)', 'cognitive_order' => 3],
            ['code' => 'analyze', 'name' => 'Analyze (Doctrinal connections)', 'cognitive_order' => 4],
            ['code' => 'evaluate', 'name' => 'Evaluate (Moral & Theological discernment)', 'cognitive_order' => 5],
            ['code' => 'create', 'name' => 'Create (Synthesis & Evangelization)', 'cognitive_order' => 6],
        ];
        foreach ($bloomLevels as $bl) {
            BloomTaxonomy::updateOrCreate(['code' => $bl['code']], $bl);
        }

        // 4. Authoritative Catholic Sources
        $sources = [
            ['title' => 'Catechism of the Catholic Church', 'short_code' => 'CCC', 'publisher_authority' => 'Holy See', 'document_type' => 'CATECHISM', 'edition' => '2nd Edition', 'publication_year' => 1997],
            ['title' => 'Holy Bible: Revised Standard Version (Catholic Edition)', 'short_code' => 'RSVCE', 'publisher_authority' => 'Catholic Church', 'document_type' => 'SCRIPTURE', 'edition' => 'Ignatius RSV-CE', 'publication_year' => 2006],
            ['title' => 'YOUCAT (Youth Catechism of the Catholic Church)', 'short_code' => 'YOUCAT', 'publisher_authority' => 'Youth Catechism Foundation / Holy See', 'document_type' => 'CATECHISM', 'edition' => 'Official Youth Edition', 'publication_year' => 2011],
            ['title' => 'DOCAT (Catholic Social Teaching for Youth)', 'short_code' => 'DOCAT', 'publisher_authority' => 'YOUCAT Foundation / Holy See', 'document_type' => 'APPROVED_PUBLICATION', 'edition' => 'Official Edition', 'publication_year' => 2016],
            ['title' => 'Second Vatican Ecumenical Council Constitutions', 'short_code' => 'VATICAN2', 'publisher_authority' => 'Holy See', 'document_type' => 'COUNCIL_DOCUMENT', 'edition' => 'Vatican Documents', 'publication_year' => 1965],
            ['title' => 'Code of Canon Law (Codex Iuris Canonici)', 'short_code' => 'CANON_LAW', 'publisher_authority' => 'Holy See', 'document_type' => 'CANON_LAW', 'edition' => '1983 Code', 'publication_year' => 1983],
            ['title' => 'Catholic Diocese of Livingstone Pastoral Statutes', 'short_code' => 'LIV_DIOCESE', 'publisher_authority' => 'Diocese of Livingstone', 'document_type' => 'DIOCESAN_DOCUMENT', 'edition' => 'Diocesan Edition', 'publication_year' => 2024],
        ];
        foreach ($sources as $src) {
            CatholicSource::updateOrCreate(['short_code' => $src['short_code']], $src);
        }

        // 5. Taxonomy Domain
        $domain = TaxonomyDomain::updateOrCreate(
            ['slug' => 'catholic-formation'],
            [
                'name' => 'Catholic Formation & Catechesis',
                'description' => 'Comprehensive digital formation library and quiz curriculum for Catholic youth.',
                'icon' => '✝️',
                'display_order' => 1,
                'is_active' => true,
            ]
        );

        // 6. The 30 Catholic Study Tracks
        $tracks = [
            ['name' => 'Holy Scripture', 'code' => 'SCRIPTURE', 'icon' => '📜', 'color_theme' => '#8B5CF6'],
            ['name' => 'YOUCAT (Youth Catechism)', 'code' => 'YOUCAT', 'icon' => '🟡', 'color_theme' => '#F59E0B'],
            ['name' => 'DOCAT (Social Teaching)', 'code' => 'DOCAT', 'icon' => '🔵', 'color_theme' => '#3B82F6'],
            ['name' => 'Catechism of the Catholic Church', 'code' => 'CCC', 'icon' => '📕', 'color_theme' => '#EF4444'],
            ['name' => 'Catholic Doctrine & Creed', 'code' => 'DOCTRINE', 'icon' => '⛪', 'color_theme' => '#10B981'],
            ['name' => 'Sacraments of the Church', 'code' => 'SACRAMENTS', 'icon' => '🕊️', 'color_theme' => '#06B6D4'],
            ['name' => 'Sacred Liturgy & Mass', 'code' => 'LITURGY', 'icon' => '🕯️', 'color_theme' => '#6366F1'],
            ['name' => 'Church History', 'code' => 'CHURCH_HISTORY', 'icon' => '🏛️', 'color_theme' => '#EC4899'],
            ['name' => 'African Church History', 'code' => 'AFRICAN_HISTORY', 'icon' => '🌍', 'color_theme' => '#D97706'],
            ['name' => 'Zambian Catholic History', 'code' => 'ZAMBIAN_HISTORY', 'icon' => '🇿🇲', 'color_theme' => '#059669'],
            ['name' => 'Catholic Social Teaching', 'code' => 'SOCIAL_TEACHING', 'icon' => '⚖️', 'color_theme' => '#2563EB'],
            ['name' => 'Christian Morality & Ten Commandments', 'code' => 'MORALITY', 'icon' => '🧭', 'color_theme' => '#7C3AED'],
            ['name' => 'Prayer & Devotional Life', 'code' => 'PRAYER', 'icon' => '🙏', 'color_theme' => '#4B5563'],
            ['name' => 'Catholic Spirituality & Mystics', 'code' => 'SPIRITUALITY', 'icon' => '✨', 'color_theme' => '#84CC16'],
            ['name' => 'Saints & Holy Men and Women', 'code' => 'SAINTS', 'icon' => '👑', 'color_theme' => '#FBBF24'],
            ['name' => 'Marian Devotion & Dogmas', 'code' => 'MARY', 'icon' => '🌹', 'color_theme' => '#60A5FA'],
            ['name' => 'Catholic Apologetics (Defending Faith)', 'code' => 'APOLOGETICS', 'icon' => '🛡️', 'color_theme' => '#DC2626'],
            ['name' => 'Christian Vocation & Discernment', 'code' => 'VOCATION', 'icon' => '💍', 'color_theme' => '#9333EA'],
            ['name' => 'Catholic Family Life & Matrimony', 'code' => 'FAMILY', 'icon' => '👨‍👩‍👧‍👦', 'color_theme' => '#F43F5E'],
            ['name' => 'Catholic Youth Formation & Ministry', 'code' => 'YOUTH_MINISTRY', 'icon' => '🎒', 'color_theme' => '#14B8A6'],
            ['name' => 'Bible Study Methods & Exegesis', 'code' => 'BIBLE_STUDY', 'icon' => '📖', 'color_theme' => '#78716C'],
            ['name' => 'Catholic Terminology & Latin Terms', 'code' => 'TERMINOLOGY', 'icon' => '🔤', 'color_theme' => '#64748B'],
            ['name' => 'Catholic Traditions & Customs', 'code' => 'TRADITIONS', 'icon' => '🔔', 'color_theme' => '#A855F7'],
            ['name' => 'Councils of the Church', 'code' => 'COUNCILS', 'icon' => '📜', 'color_theme' => '#475569'],
            ['name' => 'Popes & Papacy History', 'code' => 'POPES', 'icon' => '🗝️', 'color_theme' => '#EAB308'],
            ['name' => 'Ecumenism & Interreligious Dialogue', 'code' => 'ECUMENISM', 'icon' => '🤝', 'color_theme' => '#0284C7'],
            ['name' => 'Evangelization & Kerygma', 'code' => 'EVANGELIZATION', 'icon' => '📢', 'color_theme' => '#EA580C'],
            ['name' => 'Mission & Missionary Life', 'code' => 'MISSION', 'icon' => '⛵', 'color_theme' => '#16A34A'],
            ['name' => 'Christian Leadership & Stewardship', 'code' => 'LEADERSHIP', 'icon' => '⭐', 'color_theme' => '#4F46E5'],
            ['name' => 'Catholic Bioethics & Medical Ethics', 'code' => 'ETHICS', 'icon' => '🩺', 'color_theme' => '#0891B2'],
        ];

        foreach ($tracks as $index => $t) {
            $trackModel = TaxonomyTrack::updateOrCreate(
                ['code' => $t['code']],
                [
                    'domain_id' => $domain->id,
                    'name' => $t['name'],
                    'slug' => Str::slug($t['name']),
                    'icon' => $t['icon'],
                    'color_theme' => $t['color_theme'],
                    'display_order' => $index + 1,
                    'is_active' => true,
                ]
            );

            // Populate foundational categories for Sacraments and Scripture
            if ($t['code'] === 'SACRAMENTS') {
                $initiationCat = TaxonomyCategory::updateOrCreate(
                    ['track_id' => $trackModel->id, 'slug' => 'sacraments-of-initiation'],
                    ['name' => 'Sacraments of Christian Initiation', 'icon' => '🌊', 'display_order' => 1]
                );

                $baptismTopic = TaxonomyTopic::updateOrCreate(
                    ['category_id' => $initiationCat->id, 'slug' => 'sacrament-of-baptism'],
                    ['name' => 'Sacrament of Baptism', 'icon' => '💧', 'display_order' => 1]
                );

                $eucharistTopic = TaxonomyTopic::updateOrCreate(
                    ['category_id' => $initiationCat->id, 'slug' => 'the-holy-eucharist'],
                    ['name' => 'The Holy Eucharist', 'icon' => '🍞', 'display_order' => 2]
                );

                $conceptOriginalSin = TaxonomyConcept::updateOrCreate(
                    ['topic_id' => $baptismTopic->id, 'slug' => 'original-sin-cleansing'],
                    ['name' => 'Cleansing of Original Sin', 'summary_definition' => 'Baptism erases original sin and all personal sins.']
                );

                $conceptGrace = TaxonomyConcept::updateOrCreate(
                    ['topic_id' => $baptismTopic->id, 'slug' => 'sanctifying-grace-infusion'],
                    ['name' => 'Infusion of Sanctifying Grace', 'summary_definition' => 'Makes the baptized an adopted child of God and temple of the Holy Spirit.']
                );

                $conceptTransubstantiation = TaxonomyConcept::updateOrCreate(
                    ['topic_id' => $eucharistTopic->id, 'slug' => 'transubstantiation'],
                    ['name' => 'Transubstantiation', 'summary_definition' => 'The real substantial change of bread and wine into the Body and Blood of Christ.']
                );

                // Link graph
                app(KnowledgeTaxonomyService::class)->linkConcepts($conceptOriginalSin, $conceptGrace, 'BUILDS_ON', 'Grace builds upon the purification of baptism.');
            }

            if ($t['code'] === 'AFRICAN_HISTORY') {
                $patronsCat = TaxonomyCategory::updateOrCreate(
                    ['track_id' => $trackModel->id, 'slug' => 'african-saints-and-witnesses'],
                    ['name' => 'African Saints, Martyrs & Witnesses', 'icon' => '🌍', 'display_order' => 1]
                );

                $ugandaTopic = TaxonomyTopic::updateOrCreate(
                    ['category_id' => $patronsCat->id, 'slug' => 'uganda-martyrs'],
                    ['name' => 'The Uganda Martyrs (1885-1887)', 'icon' => '🔥', 'display_order' => 1]
                );
            }
        }

        // 7. African & Universal Saint Profiles
        $saints = [
            [
                'name' => 'St. Josephine Bakhita',
                'slug' => 'st-josephine-bakhita',
                'title_designation' => 'Virgin',
                'feast_day_month_day' => '02-08',
                'birth_year' => '1869',
                'death_year' => '1947',
                'country_region' => 'Sudan',
                'is_african_heritage' => true,
                'patronages' => ['Sudan', 'Human Trafficking Victims'],
                'biography' => 'Born in Darfur, Sudan, kidnapped into slavery, freed in Italy, and became a Canossian sister renowned for forgiveness.',
                'virtues_exemplified' => ['Forgiveness', 'Hope', 'Humility'],
                'key_teachings_quotes' => ['If I were to meet the slave-traders who kidnapped me, I would kneel and kiss their hands, for if that had not happened, I would not be a Christian.'],
                'patronage_prayer' => 'St. Josephine Bakhita, pray for all victims of slavery and inspire forgiveness in our hearts. Amen.',
            ],
            [
                'name' => 'St. Charles Lwanga and Companions',
                'slug' => 'st-charles-lwanga',
                'title_designation' => 'Martyrs of Uganda',
                'feast_day_month_day' => '06-03',
                'birth_year' => '1860',
                'death_year' => '1886',
                'country_region' => 'Uganda',
                'is_african_heritage' => true,
                'patronages' => ['African Catholic Youth Action', 'Uganda'],
                'biography' => 'Chief of the royal pages who protected young Christian pages and chose martyrdom at Namugongo over apostasy.',
                'virtues_exemplified' => ['Courage', 'Purity', 'Loyalty to Christ'],
                'key_teachings_quotes' => ['A well which has much water is not easily dried up.'],
                'patronage_prayer' => 'St. Charles Lwanga and fellow Martyrs of Uganda, pray for African Catholic Youth. Amen.',
            ],
            [
                'name' => 'St. Theresa of the Child Jesus',
                'slug' => 'st-theresa-of-lisieux',
                'title_designation' => 'Doctor of the Church & Patroness of Missions',
                'feast_day_month_day' => '10-01',
                'birth_year' => '1873',
                'death_year' => '1897',
                'country_region' => 'France',
                'is_african_heritage' => false,
                'patronages' => ['Missions', 'Florists', 'Livingstone Cathedral Patroness'],
                'biography' => 'Carmelite nun who taught the "Little Way" of spiritual childhood and absolute trust in God\'s merciful love.',
                'virtues_exemplified' => ['Simplicity', 'Trust', 'Missionary Zeal'],
                'key_teachings_quotes' => ['My vocation is love! In the heart of the Church, my Mother, I will be Love.'],
                'patronage_prayer' => 'St. Theresa of Lisieux, patroness of our Cathedral in Livingstone, shower your roses of grace upon our Diocese. Amen.',
            ],
        ];

        foreach ($saints as $saint) {
            SaintProfile::updateOrCreate(['slug' => $saint['slug']], $saint);
        }
    }
}
