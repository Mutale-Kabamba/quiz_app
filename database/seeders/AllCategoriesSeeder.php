<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds ALL 30 quiz categories with their exact numeric IDs as they exist in the
 * production JSON import files (all_questions.json / all_lessons.json).
 *
 * WHY EXPLICIT IDs:
 * The JSON import files embed hard-coded integer category_id values (1-31, with 4 missing).
 * If the categories table has different auto-increment IDs, all FK-linked question inserts
 * will fail with "FOREIGN KEY constraint failed". This seeder guarantees the categories
 * table has the exact IDs the JSON files expect.
 *
 * SAFE TO RE-RUN: uses updateOrCreate — no duplicates created.
 */
class AllCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FK checks temporarily so we can upsert with explicit IDs
        // even if the table already has some rows in a different order.
        DB::statement('PRAGMA foreign_keys = OFF');

        $categories = [
            [
                'id' => 1,
                'name' => 'Holy Scripture',
                'slug' => 'holy-scripture',
                'description' => 'Old and New Testament, Gospels, Catholic Epistles & Biblical History',
                'icon' => 'heroicon-o-book-open',
                'display_order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'YOUCAT (Youth Catechism)',
                'slug' => 'youcat',
                'description' => 'What We Believe, How We Celebrate, Life in Christ & How We Pray',
                'icon' => 'heroicon-o-sparkles',
                'display_order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'DOCAT (Social Teaching)',
                'slug' => 'docat',
                'description' => 'Human Dignity, Common Good, Family, Work, Economy & Laudato Si',
                'icon' => 'heroicon-o-globe-alt',
                'display_order' => 3,
            ],
            // Note: ID 4 was deleted locally — do NOT recreate it, or FK refs will be wrong.
            // The JSON skips category_id=4 entirely.
            [
                'id' => 5,
                'name' => 'Church History',
                'slug' => 'church-history',
                'description' => 'Early Apostolic Church, Fathers, Middle Ages, Reformation & Modern Era',
                'icon' => 'heroicon-o-building-library',
                'display_order' => 8,
            ],
            [
                'id' => 6,
                'name' => 'Catechesis & Catholic Doctrine (CCC)',
                'slug' => 'catechesis-and-doctrine',
                'description' => 'Catechism of the Catholic Church (CCC), foundational doctrines, dogmas, and Catholic faith fundamentals.',
                'icon' => 'heroicon-o-academic-cap',
                'display_order' => 4,
            ],
            [
                'id' => 7,
                'name' => 'Catholic Doctrine & Creed',
                'slug' => 'catholic-doctrine-and-creed',
                'description' => 'The Holy Trinity, Incarnation, Church Dogmas & The Nicene-Constantinopolitan Creed',
                'icon' => 'heroicon-o-shield-check',
                'display_order' => 5,
            ],
            [
                'id' => 8,
                'name' => 'Sacraments of the Church',
                'slug' => 'sacraments-of-the-church',
                'description' => 'Seven Sacraments of Initiation, Healing, and Service of Communion',
                'icon' => 'heroicon-o-heart',
                'display_order' => 6,
            ],
            [
                'id' => 9,
                'name' => 'Sacred Liturgy & Mass',
                'slug' => 'sacred-liturgy-and-mass',
                'description' => 'Holy Sacrifice of the Mass, Liturgical Year, Seasons, Colors & Vestments',
                'icon' => 'heroicon-o-sun',
                'display_order' => 7,
            ],
            [
                'id' => 10,
                'name' => 'African Church History',
                'slug' => 'african-church-history',
                'description' => 'Early Church in North Africa, Martyrs of Uganda & African Catholic Heritage',
                'icon' => 'heroicon-o-map-pin',
                'display_order' => 9,
            ],
            [
                'id' => 11,
                'name' => 'Zambian Catholic History',
                'slug' => 'zambian-catholic-history',
                'description' => 'Missionary History in Zambia, Diocese of Livingstone & Local Saints/Venerables',
                'icon' => 'heroicon-o-flag',
                'display_order' => 10,
            ],
            [
                'id' => 12,
                'name' => 'Catholic Social Teaching',
                'slug' => 'catholic-social-teaching',
                'description' => 'Human Dignity, Subsidiarity, Solidarity, Option for Poor & Encyclicals',
                'icon' => 'heroicon-o-user-group',
                'display_order' => 11,
            ],
            [
                'id' => 13,
                'name' => 'Christian Morality & 10 Commandments',
                'slug' => 'christian-morality-and-10-commandments',
                'description' => 'Decalogue, Beatitudes, Moral Conscience, Virtues & Sins',
                'icon' => 'heroicon-o-scale',
                'display_order' => 12,
            ],
            [
                'id' => 14,
                'name' => 'Prayer & Devotional Life',
                'slug' => 'prayer-and-devotional-life',
                'description' => 'Types of Prayer, Rosary, Adoration, Novenas & Liturgy of the Hours',
                'icon' => 'heroicon-o-fire',
                'display_order' => 13,
            ],
            [
                'id' => 15,
                'name' => 'Catholic Spirituality & Mystics',
                'slug' => 'catholic-spirituality-and-mystics',
                'description' => 'Ignatian, Carmelite, Franciscan, Benedictine & Dominican Traditions',
                'icon' => 'heroicon-o-sparkles',
                'display_order' => 14,
            ],
            [
                'id' => 16,
                'name' => 'Saints & Holy Men and Women',
                'slug' => 'saints-and-holy-men-and-women',
                'description' => 'Lives of the Saints, Doctors of the Church, Patronages & Canonization',
                'icon' => 'heroicon-o-star',
                'display_order' => 15,
            ],
            [
                'id' => 17,
                'name' => 'Marian Devotion & Dogmas',
                'slug' => 'marian-devotion-and-dogmas',
                'description' => 'Four Marian Dogmas, Apparitions, Feasts & Devotions',
                'icon' => 'heroicon-o-gift',
                'display_order' => 16,
            ],
            [
                'id' => 18,
                'name' => 'Catholic Apologetics',
                'slug' => 'catholic-apologetics',
                'description' => 'Defending Catholic Faith, Papacy, Real Presence, Purgatory & Traditions',
                'icon' => 'heroicon-o-shield-exclamation',
                'display_order' => 17,
            ],
            [
                'id' => 19,
                'name' => 'Christian Vocation & Discernment',
                'slug' => 'christian-vocation-and-discernment',
                'description' => 'Priesthood, Religious Life, Consecrated Single Life & Marriage Discernment',
                'icon' => 'heroicon-o-arrow-trending-up',
                'display_order' => 18,
            ],
            [
                'id' => 20,
                'name' => 'Catholic Family Life & Matrimony',
                'slug' => 'catholic-family-life-and-matrimony',
                'description' => 'Domestic Church, Marriage Sacrament, Parenting & Theology of the Body',
                'icon' => 'heroicon-o-home',
                'display_order' => 19,
            ],
            [
                'id' => 21,
                'name' => 'Youth Formation & Ministry',
                'slug' => 'youth-formation-and-ministry',
                'description' => 'Youth Leadership, Small Christian Communities, Choirs & Animation',
                'icon' => 'heroicon-o-user-plus',
                'display_order' => 20,
            ],
            [
                'id' => 22,
                'name' => 'Bible Study Methods & Exegesis',
                'slug' => 'bible-study-methods-and-exegesis',
                'description' => 'Lectio Divina, Senses of Scripture, Contextual Exegesis & Biblical Themes',
                'icon' => 'heroicon-o-magnifying-glass',
                'display_order' => 21,
            ],
            [
                'id' => 23,
                'name' => 'Catholic Terminology & Latin Terms',
                'slug' => 'catholic-terminology-and-latin-terms',
                'description' => 'Ecclesiastical Vocabulary, Latin Maxims, Liturgical Vessels & Vestments',
                'icon' => 'heroicon-o-language',
                'display_order' => 22,
            ],
            [
                'id' => 24,
                'name' => 'Catholic Traditions & Customs',
                'slug' => 'catholic-traditions-and-customs',
                'description' => 'Sacramentals, Holy Water, Incense, Processions, Fasting & Parish Customs',
                'icon' => 'heroicon-o-clock',
                'display_order' => 23,
            ],
            [
                'id' => 25,
                'name' => 'Councils of the Church',
                'slug' => 'councils-of-the-church',
                'description' => '21 Ecumenical Councils from Nicaea I to Vatican II',
                'icon' => 'heroicon-o-table-cells',
                'display_order' => 24,
            ],
            [
                'id' => 26,
                'name' => 'Popes & Papacy History',
                'slug' => 'popes-and-papacy-history',
                'description' => 'Petrine Office, Papal Succession, Major Popes & Encyclicals',
                'icon' => 'heroicon-o-crown',
                'display_order' => 25,
            ],
            [
                'id' => 27,
                'name' => 'Ecumenism & Interreligious Dialogue',
                'slug' => 'ecumenism-and-interreligious-dialogue',
                'description' => 'Christian Unity, Nostra Aetate, Unitatis Redintegratio & Relations',
                'icon' => 'heroicon-o-arrows-pointing-in',
                'display_order' => 26,
            ],
            [
                'id' => 28,
                'name' => 'Evangelization & Kerygma',
                'slug' => 'evangelization-and-kerygma',
                'description' => 'The Great Commission, Evangelii Gaudium & Sharing the Catholic Faith',
                'icon' => 'heroicon-o-megaphone',
                'display_order' => 27,
            ],
            [
                'id' => 29,
                'name' => 'Mission & Missionary Life',
                'slug' => 'mission-and-missionary-life',
                'description' => 'Mission Ad Gentes, Missionary Societies, Livingstone Diocese Missions',
                'icon' => 'heroicon-o-paper-airplane',
                'display_order' => 28,
            ],
            [
                'id' => 30,
                'name' => 'Christian Leadership & Stewardship',
                'slug' => 'christian-leadership-and-stewardship',
                'description' => 'Servant Leadership, Parish Council, Financial Stewardship & Accountability',
                'icon' => 'heroicon-o-briefcase',
                'display_order' => 29,
            ],
            [
                'id' => 31,
                'name' => 'Catholic Bioethics & Medical Ethics',
                'slug' => 'catholic-bioethics-and-medical-ethics',
                'description' => 'Sanctity of Life, Medical Ethics, Evangelium Vitae & Bioethical Decisions',
                'icon' => 'heroicon-o-beaker',
                'display_order' => 30,
            ],
        ];

        foreach ($categories as $data) {
            // Use upsert with explicit ID so existing records are updated, new ones inserted
            // with the exact same primary key the JSON import files expect.
            $existing = Category::find($data['id']);
            if ($existing) {
                $existing->update(array_diff_key($data, ['id' => true]));
            } else {
                DB::table('categories')->insert(array_merge($data, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // Remove orphaned category ID 4 if it exists on live (created by old DatabaseSeeder)
        // so it doesn't cause confusion — it has no questions or lessons.
        $orphan = Category::find(4);
        if ($orphan && $orphan->questions()->count() === 0 && $orphan->lessons()->count() === 0) {
            $orphan->delete();
            $this->command?->warn('Removed orphaned empty Category #4.');
        }

        DB::statement('PRAGMA foreign_keys = ON');

        $this->command?->info('✓ All 30 categories seeded with correct IDs (1,2,3,5–31). Ready for curriculum import.');
    }
}
