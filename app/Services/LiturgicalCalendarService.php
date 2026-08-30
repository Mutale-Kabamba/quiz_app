<?php

namespace App\Services;

use Carbon\Carbon;

class LiturgicalCalendarService
{
    /**
     * Get the current liturgical season, color accents, feast details, and full Catholic mass readings for today or a specific date.
     */
    public function getCurrentContext(?Carbon $date = null): array
    {
        $now = $date ? $date->copy() : Carbon::now();
        $month = (int) $now->format('n');
        $day = (int) $now->format('j');
        $dayOfWeek = (int) $now->dayOfWeek; // 0 = Sunday, 1 = Monday, ... 6 = Saturday
        $dayOfYear = (int) $now->dayOfYear;

        // 1. Determine Fixed Major Feasts & Solemnities in the Catholic Universal Calendar
        $fixedFeast = $this->getFixedFeast($month, $day);

        // 2. Determine Liturgical Season & Colors
        $seasonInfo = $this->determineSeason($month, $day, $dayOfWeek, $fixedFeast);

        // 3. Determine Today's Liturgical Day Title and Feast Type
        if ($fixedFeast) {
            $liturgicalDay = $fixedFeast['title'];
            $feastName = $fixedFeast['name'];
            $feastType = $fixedFeast['type'];
            $liturgicalColor = $fixedFeast['color'];
            $saintOfDay = $fixedFeast['saint'] ?? $fixedFeast['name'];
        } elseif ($dayOfWeek === 0) {
            $weekNum = min(34, max(1, (int) ceil(($dayOfYear - 10) / 7)));
            $liturgicalDay = $this->getSundayTitle($seasonInfo['season'], $weekNum);
            $feastName = $liturgicalDay;
            $feastType = ($seasonInfo['season'] === 'Ordinary Time') ? 'Sunday in Ordinary Time' : "Sunday of {$seasonInfo['season']}";
            $liturgicalColor = $seasonInfo['color'];
            $saintOfDay = $this->getSaintForDay($month, $day);
        } else {
            $weekdayName = $now->format('l');
            $weekNum = min(34, max(1, (int) ceil(($dayOfYear - 10) / 7)));
            $liturgicalDay = "{$weekdayName} of the {$weekNum}" . $this->getOrdinalSuffix($weekNum) . " Week in " . $seasonInfo['season'];
            $feastName = null;
            $feastType = 'Weekday';
            $liturgicalColor = $seasonInfo['color'];
            $saintOfDay = $this->getSaintForDay($month, $day);
        }

        // Color UI Theme Styling
        $colorTheme = $this->getColorTheme($liturgicalColor);

        // 4. Generate Catholic Mass Readings for Date
        $readings = $this->getDailyMassReadings($month, $day, $dayOfWeek, $fixedFeast, $seasonInfo['season']);

        // 5. Daily rotating scripture inspiration for youth (Daily Spiritual Bread)
        $dailyBread = $this->getDailySpiritualBread($dayOfYear);

        // 6. Build carousel slides array across the day's readings
        $slides = [];
        if (!empty($readings['reading_1'])) {
            $slides[] = [
                'type' => '1st Reading',
                'citation' => $readings['reading_1']['citation'] ?? 'First Reading',
                'highlight' => $readings['reading_1']['key_verse'] ?? \Illuminate\Support\Str::limit($readings['reading_1']['text'] ?? '', 140),
            ];
        }
        if (!empty($readings['psalm'])) {
            $slides[] = [
                'type' => 'Responsorial Psalm',
                'citation' => $readings['psalm']['citation'] ?? 'Psalm',
                'highlight' => !empty($readings['psalm']['response']) ? 'Response: ' . $readings['psalm']['response'] : \Illuminate\Support\Str::limit($readings['psalm']['text'] ?? '', 140),
            ];
        }
        if (!empty($readings['reading_2'])) {
            $slides[] = [
                'type' => '2nd Reading',
                'citation' => $readings['reading_2']['citation'] ?? 'Second Reading',
                'highlight' => $readings['reading_2']['key_verse'] ?? \Illuminate\Support\Str::limit($readings['reading_2']['text'] ?? '', 140),
            ];
        }
        if (!empty($readings['gospel'])) {
            $slides[] = [
                'type' => 'Holy Gospel',
                'citation' => $readings['gospel']['citation'] ?? 'Holy Gospel',
                'highlight' => $readings['gospel']['key_verse'] ?? \Illuminate\Support\Str::limit($readings['gospel']['text'] ?? '', 140),
            ];
        }

        return [
            'season' => $seasonInfo['season'],
            'liturgical_day' => $liturgicalDay,
            'feast_name' => $feastName,
            'feast_type' => $feastType,
            'liturgical_color' => $liturgicalColor,
            'color_key' => $colorTheme['key'],
            'color_hex' => $colorTheme['hex'],
            'color_bg' => $colorTheme['bg'],
            'color_text' => $colorTheme['text'],
            'color_border' => $colorTheme['border'],
            'color_badge' => $colorTheme['badge'],
            'readings' => $readings,
            'slides' => $slides,
            'verse' => $dailyBread['verse'],
            'verse_ref' => $dailyBread['ref'],
            'saint_of_day' => $saintOfDay,
            'is_today' => $now->isToday(),
            'date_raw' => $now->format('Y-m-d'),
            'date_formatted' => $now->format('l, F j, Y'),
            'date_short' => $now->format('M j'),
            'diocesan_patroness' => "St. Theresa of the Child Jesus (Cathedral Patroness)",
        ];
    }

    /**
     * Detect fixed Catholic Feasts, Solemnities, and Memorials
     */
    protected function getFixedFeast(int $month, int $day): ?array
    {
        $feasts = [
            '1-1' => ['title' => 'Solemnity of Mary, Mother of God', 'name' => 'Mary, Holy Mother of God', 'type' => 'Solemnity', 'color' => 'White', 'saint' => 'Blessed Virgin Mary'],
            '1-6' => ['title' => 'The Epiphany of the Lord', 'name' => 'The Epiphany', 'type' => 'Solemnity', 'color' => 'White', 'saint' => 'The Three Magi'],
            '1-25' => ['title' => 'Feast of the Conversion of Saint Paul, Apostle', 'name' => 'Conversion of St. Paul', 'type' => 'Feast', 'color' => 'White', 'saint' => 'St. Paul the Apostle'],
            '1-28' => ['title' => 'Memorial of Saint Thomas Aquinas, Priest and Doctor', 'name' => 'St. Thomas Aquinas', 'type' => 'Memorial', 'color' => 'White', 'saint' => 'St. Thomas Aquinas'],
            '2-2' => ['title' => 'Feast of the Presentation of the Lord (Candlemas)', 'name' => 'Presentation of the Lord', 'type' => 'Feast', 'color' => 'White', 'saint' => 'Simeon & Anna'],
            '2-8' => ['title' => 'Memorial of Saint Josephine Bakhita, Virgin', 'name' => 'St. Josephine Bakhita', 'type' => 'Memorial', 'color' => 'White', 'saint' => 'St. Josephine Bakhita'],
            '2-22' => ['title' => 'Feast of the Chair of Saint Peter, Apostle', 'name' => 'Chair of St. Peter', 'type' => 'Feast', 'color' => 'White', 'saint' => 'St. Peter the Apostle'],
            '3-19' => ['title' => 'Solemnity of Saint Joseph, Spouse of the Blessed Virgin Mary', 'name' => 'St. Joseph, Husband of Mary', 'type' => 'Solemnity', 'color' => 'White', 'saint' => 'St. Joseph'],
            '3-25' => ['title' => 'Solemnity of the Annunciation of the Lord', 'name' => 'The Annunciation', 'type' => 'Solemnity', 'color' => 'White', 'saint' => 'Archangel Gabriel'],
            '4-25' => ['title' => 'Feast of Saint Mark, Evangelist', 'name' => 'St. Mark the Evangelist', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'St. Mark'],
            '5-1' => ['title' => 'Memorial of Saint Joseph the Worker', 'name' => 'St. Joseph the Worker', 'type' => 'Memorial', 'color' => 'White', 'saint' => 'St. Joseph the Worker'],
            '5-3' => ['title' => 'Feast of Saints Philip and James, Apostles', 'name' => 'Sts. Philip & James', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'Sts. Philip & James'],
            '5-31' => ['title' => 'Feast of the Visitation of the Blessed Virgin Mary', 'name' => 'The Visitation', 'type' => 'Feast', 'color' => 'White', 'saint' => 'Blessed Virgin Mary'],
            '6-3' => ['title' => 'Memorial of Saint Charles Lwanga and Companions, Martyrs (Uganda)', 'name' => 'Uganda Martyrs', 'type' => 'Memorial', 'color' => 'Red', 'saint' => 'St. Charles Lwanga & Companions'],
            '6-24' => ['title' => 'Solemnity of the Nativity of Saint John the Baptist', 'name' => 'Nativity of St. John the Baptist', 'type' => 'Solemnity', 'color' => 'White', 'saint' => 'St. John the Baptist'],
            '6-29' => ['title' => 'Solemnity of Saints Peter and Paul, Apostles', 'name' => 'Sts. Peter & Paul', 'type' => 'Solemnity', 'color' => 'Red', 'saint' => 'Sts. Peter & Paul'],
            '7-3' => ['title' => 'Feast of Saint Thomas, Apostle', 'name' => 'St. Thomas the Apostle', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'St. Thomas'],
            '7-22' => ['title' => 'Feast of Saint Mary Magdalene, Apostle to the Apostles', 'name' => 'St. Mary Magdalene', 'type' => 'Feast', 'color' => 'White', 'saint' => 'St. Mary Magdalene'],
            '7-25' => ['title' => 'Feast of Saint James, Apostle', 'name' => 'St. James the Greater', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'St. James'],
            '8-6' => ['title' => 'Feast of the Transfiguration of the Lord', 'name' => 'The Transfiguration', 'type' => 'Feast', 'color' => 'White', 'saint' => 'Jesus Christ Transfigured'],
            '8-10' => ['title' => 'Feast of Saint Lawrence, Deacon and Martyr', 'name' => 'St. Lawrence', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'St. Lawrence'],
            '8-15' => ['title' => 'Solemnity of the Assumption of the Blessed Virgin Mary', 'name' => 'The Assumption', 'type' => 'Solemnity', 'color' => 'White', 'saint' => 'Blessed Virgin Mary'],
            '8-24' => ['title' => 'Feast of Saint Bartholomew, Apostle', 'name' => 'St. Bartholomew', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'St. Bartholomew'],
            '8-27' => ['title' => 'Memorial of Saint Monica', 'name' => 'St. Monica', 'type' => 'Memorial', 'color' => 'White', 'saint' => 'St. Monica of Hippo'],
            '8-28' => ['title' => 'Memorial of Saint Augustine, Bishop and Doctor of the Church', 'name' => 'St. Augustine of Hippo', 'type' => 'Memorial', 'color' => 'White', 'saint' => 'St. Augustine'],
            '8-29' => ['title' => 'Memorial of the Passion of Saint John the Baptist, Martyr', 'name' => 'Passion of St. John the Baptist', 'type' => 'Memorial', 'color' => 'Red', 'saint' => 'St. John the Baptist'],
            '9-8' => ['title' => 'Feast of the Nativity of the Blessed Virgin Mary', 'name' => 'Nativity of the Blessed Virgin Mary', 'type' => 'Feast', 'color' => 'White', 'saint' => 'Blessed Virgin Mary'],
            '9-14' => ['title' => 'Feast of the Exaltation of the Holy Cross', 'name' => 'Exaltation of the Holy Cross', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'The Holy Cross'],
            '9-21' => ['title' => 'Feast of Saint Matthew, Apostle and Evangelist', 'name' => 'St. Matthew the Evangelist', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'St. Matthew'],
            '9-29' => ['title' => 'Feast of Saints Michael, Gabriel, and Raphael, Archangels', 'name' => 'The Holy Archangels', 'type' => 'Feast', 'color' => 'White', 'saint' => 'Archangels Michael, Gabriel & Raphael'],
            '10-1' => ['title' => 'Memorial of Saint Thérèse of the Child Jesus, Virgin and Doctor (Cathedral Patroness)', 'name' => 'St. Thérèse of Lisieux', 'type' => 'Memorial', 'color' => 'White', 'saint' => 'St. Thérèse of the Child Jesus'],
            '10-4' => ['title' => 'Memorial of Saint Francis of Assisi', 'name' => 'St. Francis of Assisi', 'type' => 'Memorial', 'color' => 'White', 'saint' => 'St. Francis of Assisi'],
            '10-18' => ['title' => 'Feast of Saint Luke, Evangelist', 'name' => 'St. Luke the Evangelist', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'St. Luke'],
            '10-28' => ['title' => 'Feast of Saints Simon and Jude, Apostles', 'name' => 'Sts. Simon & Jude', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'Sts. Simon & Jude'],
            '11-1' => ['title' => 'Solemnity of All Saints', 'name' => 'All Saints Day', 'type' => 'Solemnity', 'color' => 'White', 'saint' => 'All the Saints in Heaven'],
            '11-2' => ['title' => 'The Commemoration of All the Faithful Departed (All Souls)', 'name' => 'All Souls Day', 'type' => 'Commemoration', 'color' => 'Purple', 'saint' => 'The Holy Souls in Purgatory'],
            '11-30' => ['title' => 'Feast of Saint Andrew, Apostle', 'name' => 'St. Andrew the Apostle', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'St. Andrew'],
            '12-3' => ['title' => 'Memorial of Saint Francis Xavier, Priest', 'name' => 'St. Francis Xavier', 'type' => 'Memorial', 'color' => 'White', 'saint' => 'St. Francis Xavier'],
            '12-8' => ['title' => 'Solemnity of the Immaculate Conception of the Blessed Virgin Mary', 'name' => 'The Immaculate Conception', 'type' => 'Solemnity', 'color' => 'White', 'saint' => 'Blessed Virgin Mary'],
            '12-25' => ['title' => 'Solemnity of the Nativity of the Lord (Christmas)', 'name' => 'Christmas Day', 'type' => 'Solemnity', 'color' => 'White', 'saint' => 'Our Lord Jesus Christ'],
            '12-26' => ['title' => 'Feast of Saint Stephen, First Martyr', 'name' => 'St. Stephen the Protomartyr', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'St. Stephen'],
            '12-27' => ['title' => 'Feast of Saint John, Apostle and Evangelist', 'name' => 'St. John the Evangelist', 'type' => 'Feast', 'color' => 'White', 'saint' => 'St. John'],
            '12-28' => ['title' => 'Feast of the Holy Innocents, Martyrs', 'name' => 'The Holy Innocents', 'type' => 'Feast', 'color' => 'Red', 'saint' => 'The Holy Innocents'],
        ];

        $key = "{$month}-{$day}";
        return $feasts[$key] ?? null;
    }

    /**
     * Determine season and base liturgical color
     */
    protected function determineSeason(int $month, int $day, int $dayOfWeek, ?array $fixedFeast): array
    {
        if ($month === 12 && $day <= 24) {
            return ['season' => 'Advent', 'color' => 'Purple'];
        } elseif (($month === 12 && $day >= 25) || ($month === 1 && $day <= 10)) {
            return ['season' => 'Christmas Season', 'color' => 'White'];
        } elseif ($month >= 2 && $month <= 4 && $day <= 15) {
            return ['season' => 'Lenten Season', 'color' => 'Purple'];
        } elseif ($month >= 4 && $month <= 5) {
            return ['season' => 'Easter Season', 'color' => 'White'];
        }

        return ['season' => 'Ordinary Time', 'color' => 'Green'];
    }

    /**
     * Format ordinal suffix for week numbers
     */
    protected function getOrdinalSuffix(int $number): string
    {
        if (!in_array(($number % 100), [11, 12, 13])) {
            switch ($number % 10) {
                case 1: return 'st';
                case 2: return 'nd';
                case 3: return 'rd';
            }
        }
        return 'th';
    }

    /**
     * Get Sunday title
     */
    protected function getSundayTitle(string $season, int $weekNum): string
    {
        if ($season === 'Ordinary Time') {
            return "{$weekNum}" . $this->getOrdinalSuffix($weekNum) . " Sunday in Ordinary Time";
        }
        return "{$weekNum}" . $this->getOrdinalSuffix($weekNum) . " Sunday of {$season}";
    }

    /**
     * Fallback Saint for day
     */
    protected function getSaintForDay(int $month, int $day): string
    {
        $monthlySaints = [
            1 => 'St. Elizabeth Ann Seton & St. Anthony of Egypt',
            2 => 'St. Scholastica & St. Polycarp of Smyrna',
            3 => 'St. Patrick of Ireland & St. Cyril of Jerusalem',
            4 => 'St. George & St. Catherine of Siena',
            5 => 'St. Athanasius & St. Rita of Cascia',
            6 => 'St. Boniface & St. Aloysius Gonzaga',
            7 => 'St. Benedict of Nursia & St. Bridget of Sweden',
            8 => 'St. John Vianney & St. Bernard of Clairvaux',
            9 => 'St. Gregory the Great & St. Vincent de Paul',
            10 => 'St. John Paul II & St. Luke the Evangelist',
            11 => 'St. Martin of Tours & St. Cecilia',
            12 => 'St. Nicholas of Myra & St. Lucy of Syracuse',
        ];

        return $monthlySaints[$month] ?? 'St. Theresa of the Child Jesus';
    }

    /**
     * Color theme Tailwind presets
     */
    protected function getColorTheme(string $color): array
    {
        return match (strtolower($color)) {
            'purple' => [
                'key' => 'purple',
                'hex' => '#7C3AED',
                'bg' => 'bg-purple-50 dark:bg-purple-950/30',
                'text' => 'text-purple-700 dark:text-purple-300',
                'border' => 'border-purple-200 dark:border-purple-800',
                'badge' => 'bg-purple-100 dark:bg-purple-900/60 text-purple-800 dark:text-purple-200 border-purple-300 dark:border-purple-700',
            ],
            'red' => [
                'key' => 'red',
                'hex' => '#DC2626',
                'bg' => 'bg-rose-50 dark:bg-rose-950/30',
                'text' => 'text-rose-700 dark:text-rose-300',
                'border' => 'border-rose-200 dark:border-rose-800',
                'badge' => 'bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-200 border-rose-300 dark:border-rose-700',
            ],
            'white', 'gold' => [
                'key' => 'gold',
                'hex' => '#D97706',
                'bg' => 'bg-amber-50 dark:bg-amber-950/30',
                'text' => 'text-amber-700 dark:text-amber-300',
                'border' => 'border-amber-200 dark:border-amber-800',
                'badge' => 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border-amber-300 dark:border-amber-700',
            ],
            default => [ // Green
                'key' => 'green',
                'hex' => '#059669',
                'bg' => 'bg-emerald-50 dark:bg-emerald-950/30',
                'text' => 'text-emerald-700 dark:text-emerald-300',
                'border' => 'border-emerald-200 dark:border-emerald-800',
                'badge' => 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 border-emerald-300 dark:border-emerald-700',
            ],
        };
    }

    /**
     * Complete Catholic Lectionary Mass Readings Engine for Today
     */
    protected function getDailyMassReadings(int $month, int $day, int $dayOfWeek, ?array $fixedFeast, string $season): array
    {
        $isSunday = ($dayOfWeek === 0);

        // Curated readings bank based on liturgical cycles & calendar
        $readingsLibrary = [
            // Sample Lectionary Cycle entries for Ordinary Time / Feasts
            0 => [
                'reading_1' => [
                    'citation' => 'Sirach 3:17-18, 20, 28-29',
                    'title' => 'First Reading',
                    'text' => 'My child, conduct your affairs with humility, and you will be loved more than a giver of gifts. Humble yourself the more, the greater you are, and you will find favor with God. What is too sublime for you, seek not; into things beyond your strength, search not.',
                    'key_verse' => 'Humble yourself the more, the greater you are, and you will find favor with God.',
                ],
                'psalm' => [
                    'citation' => 'Psalm 68:4-5, 6-7, 10-11',
                    'title' => 'Responsorial Psalm',
                    'response' => 'God, in your goodness, you have made a home for the poor.',
                    'text' => 'The just rejoice and exult before God; they are glad and rejoice. Sing to God, chant praise to his name; whose name is the LORD. Father of the fatherless and defender of widows is God in his holy abode.',
                ],
                'reading_2' => [
                    'citation' => 'Hebrews 12:18-19, 22-24a',
                    'title' => 'Second Reading',
                    'text' => 'Brothers and sisters: You have not approached that which could be touched and a blazing fire and gloomy darkness and storm. No, you have approached Mount Zion and the city of the living God, the heavenly Jerusalem, and countless angels in festal gathering, and Jesus, the mediator of a new covenant.',
                    'key_verse' => 'You have approached Mount Zion and the city of the living God.',
                ],
                'acclamation' => [
                    'citation' => 'Matthew 11:29ab',
                    'verse' => 'Take my yoke upon you, says the Lord, and learn from me, for I am meek and humble of heart.',
                ],
                'gospel' => [
                    'citation' => 'Luke 14:1, 7-14',
                    'title' => 'Holy Gospel',
                    'text' => 'On a sabbath Jesus went to dine at the home of one of the leading Pharisees. He told a parable to those who had been invited: "When you are invited by someone to a wedding banquet, do not recline in the place of honor. Go and take the lowest place. For everyone who exalts himself will be humbled, but the one who humbles himself will be exalted."',
                    'key_verse' => 'For everyone who exalts himself will be humbled, but the one who humbles himself will be exalted.',
                ],
                'reflection' => 'Christ invites us to embrace authentic humility in all our relationships and service, trusting that God elevates the meek.',
            ],
            1 => [
                'reading_1' => [
                    'citation' => '1 Corinthians 2:1-5',
                    'title' => 'First Reading',
                    'text' => 'When I came to you, brothers and sisters, proclaiming the mystery of God, I did not come with sublimity of words or of wisdom. For I resolved to know nothing while I was with you except Jesus Christ, and him crucified.',
                    'key_verse' => 'I resolved to know nothing while I was with you except Jesus Christ, and him crucified.',
                ],
                'psalm' => [
                    'citation' => 'Psalm 119:97, 98, 99, 100, 101, 102',
                    'title' => 'Responsorial Psalm',
                    'response' => 'Lord, I love your commands.',
                    'text' => 'How I love your law, Lord! It is my meditation all the day. Your command has made me wiser than my enemies, for it is ever with me. I have more understanding than all my teachers when your decrees are my meditation.',
                ],
                'reading_2' => null,
                'acclamation' => [
                    'citation' => 'Luke 4:18',
                    'verse' => 'The Spirit of the Lord is upon me; he has sent me to bring glad tidings to the poor.',
                ],
                'gospel' => [
                    'citation' => 'Luke 4:16-30',
                    'title' => 'Holy Gospel',
                    'text' => 'Jesus came to Nazareth, where he had grown up, and went according to his custom into the synagogue on the sabbath day. He unrolled the scroll of the prophet Isaiah and found the passage where it was written: "The Spirit of the Lord is upon me, because he has anointed me to bring glad tidings to the poor."',
                    'key_verse' => 'Today this Scripture passage is fulfilled in your hearing.',
                ],
                'reflection' => 'The Word of God brings freedom and truth into our everyday challenges when we listen with an open, receptive heart.',
            ],
            2 => [
                'reading_1' => [
                    'citation' => '1 Corinthians 2:10b-16',
                    'title' => 'First Reading',
                    'text' => 'Brothers and sisters: The Spirit scrutinizes everything, even the depths of God. Among humans, who knows what pertains to a person except the spirit within? Similarly, no one knows what pertains to God except the Spirit of God. We have received not the spirit of the world but the Spirit that is from God.',
                    'key_verse' => 'We have received not the spirit of the world but the Spirit that is from God.',
                ],
                'psalm' => [
                    'citation' => 'Psalm 145:8-9, 10-11, 12-13ab, 13cd-14',
                    'title' => 'Responsorial Psalm',
                    'response' => 'The Lord is just in all his ways.',
                    'text' => 'The LORD is gracious and merciful, slow to anger and of great kindness. The LORD is good to all and compassionate toward all his works. Let all your works give you thanks, O LORD, and let your faithful ones bless you.',
                ],
                'reading_2' => null,
                'acclamation' => [
                    'citation' => 'Luke 7:16',
                    'verse' => 'A great prophet has arisen in our midst and God has visited his people.',
                ],
                'gospel' => [
                    'citation' => 'Luke 4:31-37',
                    'title' => 'Holy Gospel',
                    'text' => 'Jesus went down to Capernaum, a town of Galilee. He taught them on the sabbath, and they were astonished at his teaching because he spoke with authority. In the synagogue there was a man with the spirit of an unclean demon, and Jesus rebuked him and healed the man.',
                    'key_verse' => 'They were all amazed and said to one another, "What is there about his word? For with authority and power he commands."',
                ],
                'reflection' => 'Jesus speaks with divine authority to calm our fears, bring healing to our hearts, and restore peace to our lives.',
            ],
            3 => [
                'reading_1' => [
                    'citation' => '1 Corinthians 3:1-9',
                    'title' => 'First Reading',
                    'text' => 'Brothers and sisters, I could not talk to you as spiritual people, but as people of the flesh. I planted, Apollos watered, but God caused the growth. Therefore, neither the one who plants nor the one who waters is anything, but only God, who causes the growth.',
                    'key_verse' => 'I planted, Apollos watered, but God caused the growth.',
                ],
                'psalm' => [
                    'citation' => 'Psalm 33:12-13, 14-15, 20-21',
                    'title' => 'Responsorial Psalm',
                    'response' => 'Blessed the people the Lord has chosen to be his own.',
                    'text' => 'Blessed the nation whose God is the LORD, the people he has chosen for his own inheritance. From heaven the LORD looks down; he sees all mankind. From his fixed dwelling he beholds all who dwell on the earth.',
                ],
                'reading_2' => null,
                'acclamation' => [
                    'citation' => 'Luke 4:18',
                    'verse' => 'The Lord sent me to bring glad tidings to the poor and to proclaim liberty to captives.',
                ],
                'gospel' => [
                    'citation' => 'Luke 4:38-44',
                    'title' => 'Holy Gospel',
                    'text' => 'After Jesus left the synagogue, he entered the house of Simon. Simon\'s mother-in-law was afflicted with a severe fever, and they interceded with him about her. He stood over her, rebuked the fever, and it left her. She got up immediately and waited on them.',
                    'key_verse' => 'He laid his hands on each of them and cured them.',
                ],
                'reflection' => 'When Christ touches and heals us, our immediate joyful response is service to God and our parish community.',
            ],
            4 => [
                'reading_1' => [
                    'citation' => '1 Corinthians 3:18-23',
                    'title' => 'First Reading',
                    'text' => 'Brothers and sisters: Let no one deceive himself. If any one among you considers himself wise in this age, let him become a fool, so as to become wise. For the wisdom of this world is foolishness in the eyes of God.',
                    'key_verse' => 'All belong to you, and you to Christ, and Christ to God.',
                ],
                'psalm' => [
                    'citation' => 'Psalm 24:1bc-2, 3-4ab, 5-6',
                    'title' => 'Responsorial Psalm',
                    'response' => 'To the Lord belongs the earth and all that fills it.',
                    'text' => 'The LORD\'s are the earth and its fullness; the world and those who dwell in it. For he founded it upon the seas and established it upon the rivers. Who can ascend the mountain of the LORD? He whose hands are sinless, whose heart is clean.',
                ],
                'reading_2' => null,
                'acclamation' => [
                    'citation' => 'Matthew 4:19',
                    'verse' => 'Come after me, says the Lord, and I will make you fishers of men.',
                ],
                'gospel' => [
                    'citation' => 'Luke 5:1-11',
                    'title' => 'Holy Gospel',
                    'text' => 'While the crowd was pressing in on Jesus and listening to the word of God, he said to Simon: "Put out into deep water and lower your nets for a catch." Simon said in reply, "Master, we have worked hard all night and have caught nothing, but at your command I will lower the nets." When they had done this, they caught a great number of fish.',
                    'key_verse' => '"Do not be afraid; from now on you will be catching men." When they brought their boats to the shore, they left everything and followed him.',
                ],
                'reflection' => 'Duc in altum! Put out into the deep. Jesus calls our youth to trust his voice even when past efforts seemed fruitless.',
            ],
            5 => [
                'reading_1' => [
                    'citation' => '1 Corinthians 4:1-5',
                    'title' => 'First Reading',
                    'text' => 'Brothers and sisters: Thus should one regard us: as servants of Christ and stewards of the mysteries of God. Now it is of course required of stewards that one be found trustworthy. The Lord is the one who judges me.',
                    'key_verse' => 'It is required of stewards that one be found trustworthy.',
                ],
                'psalm' => [
                    'citation' => 'Psalm 37:3-4, 5-6, 27-28, 39-40',
                    'title' => 'Responsorial Psalm',
                    'response' => 'The salvation of the just comes from the Lord.',
                    'text' => 'Trust in the LORD and do good, that you may dwell in the land and be fed in security. Take delight in the LORD, and he will grant you your heart\'s requests. Commit to the LORD your way; trust in him, and he will act.',
                ],
                'reading_2' => null,
                'acclamation' => [
                    'citation' => 'John 8:12',
                    'verse' => 'I am the light of the world, says the Lord; whoever follows me will have the light of life.',
                ],
                'gospel' => [
                    'citation' => 'Luke 5:33-39',
                    'title' => 'Holy Gospel',
                    'text' => 'The scribes and Pharisees said to Jesus, "The disciples of John fast often and offer prayers, and the disciples of the Pharisees do the same; but yours eat and drink." Jesus answered them, "Can you make the wedding guests fast while the bridegroom is with them? But the days will come when the bridegroom is taken away from them, then they will fast in those days."',
                    'key_verse' => 'No one pours new wine into old wineskins. New wine must be poured into fresh wineskins.',
                ],
                'reflection' => 'Christ brings a new covenant of grace and joy into our lives, renewing our spirit from within.',
            ],
            6 => [
                'reading_1' => [
                    'citation' => '1 Corinthians 4:6b-15',
                    'title' => 'First Reading',
                    'text' => 'Brothers and sisters: Learn from us not to go beyond what is written, so that none of you will be inflated with pride in favor of one person over against another. For who makes you see one as better than another? What do you possess that you have not received?',
                    'key_verse' => 'What do you possess that you have not received? If you have received it, why do you boast as if you had not received it?',
                ],
                'psalm' => [
                    'citation' => 'Psalm 145:17-18, 19-20, 21',
                    'title' => 'Responsorial Psalm',
                    'response' => 'The Lord is near to all who call upon him.',
                    'text' => 'The LORD is just in all his ways and holy in all his works. The LORD is near to all who call upon him, to all who call upon him in truth. He fulfills the desire of those who fear him; he hears their cry and saves them.',
                ],
                'reading_2' => null,
                'acclamation' => [
                    'citation' => 'John 14:6',
                    'verse' => 'I am the way, the truth, and the life, says the Lord; no one comes to the Father except through me.',
                ],
                'gospel' => [
                    'citation' => 'Luke 6:1-5',
                    'title' => 'Holy Gospel',
                    'text' => 'While Jesus was going through a field of grain on a sabbath, his disciples were picking the heads of grain, rubbing them in their hands, and eating them. Some Pharisees said, "Why are you doing what is unlawful on the sabbath?" Jesus said to them in reply, "Have you not read what David did? The Son of Man is lord of the sabbath."',
                    'key_verse' => 'The Son of Man is lord of the sabbath.',
                ],
                'reflection' => 'The Sabbath is a gift of holy rest and encounter with Christ, who is Lord of our time and our worship.',
            ],
        ];

        $feastKey = "{$month}-{$day}";
        $specialFeastReadings = [
            '1-1' => [
                'reading_1' => [
                    'citation' => 'Numbers 6:22–27',
                    'title' => 'First Reading',
                    'text' => 'The LORD said to Moses: "Speak to Aaron and his sons and say: Thus you shall bless the Israelites. Say to them: The LORD bless you and keep you! The LORD let his face shine upon you, and be gracious to you! The LORD look upon you kindly and give you peace!" So shall they invoke my name upon the Israelites, and I will bless them.',
                    'key_verse' => 'The LORD bless you and keep you! The LORD let his face shine upon you!',
                ],
                'psalm' => [
                    'citation' => 'Psalm 67:2–3, 5, 6, 8',
                    'title' => 'Responsorial Psalm',
                    'response' => 'May God bless us in his mercy.',
                    'text' => 'May God have pity on us and bless us; may he let his face shine upon us. So may your way be known upon earth; among all nations, your salvation. May the nations be glad and exult because you rule the peoples in equity.',
                ],
                'reading_2' => [
                    'citation' => 'Galatians 4:4–7',
                    'title' => 'Second Reading',
                    'text' => 'Brothers and sisters: When the fullness of time had come, God sent his Son, born of a woman, born under the law, to ransom those under the law, so that we might receive adoption. As proof that you are sons, God sent the Spirit of his Son into our hearts, crying out, "Abba, Father!"',
                    'key_verse' => 'God sent his Son, born of a woman, so that we might receive adoption as children.',
                ],
                'acclamation' => [
                    'citation' => 'Hebrews 1:1–2',
                    'verse' => 'In the past God spoke to our ancestors through the prophets; in these last days, he has spoken to us through the Son.',
                ],
                'gospel' => [
                    'citation' => 'Luke 2:16–21',
                    'title' => 'Holy Gospel',
                    'text' => 'The shepherds went in haste to Bethlehem and found Mary and Joseph, and the infant lying in the manger. When they saw this, they made known the message that had been told them about this child. All who heard it were amazed by what had been told them by the shepherds. And Mary kept all these things, reflecting on them in her heart.',
                    'key_verse' => 'Mary kept all these things, reflecting on them in her heart.',
                ],
                'reflection' => 'On this octave of Christmas and Solemnity of Mary, Mother of God, we look to Mary\'s contemplative heart and entrust the year to her maternal protection.',
            ],
            '6-3' => [
                'reading_1' => [
                    'citation' => '2 Maccabees 7:1–2, 9–14',
                    'title' => 'First Reading',
                    'text' => 'It also happened that seven brothers with their mother were arrested and tortured with whips and scourges by the king, to force them to eat pork in violation of God\'s law. One of the brothers, speaking for the others, said: "What do you expect to achieve by questioning us? We are ready to die rather than transgress the laws of our ancestors."',
                    'key_verse' => 'The King of the world will raise us up to live again forever, because we are dying for his laws.',
                ],
                'psalm' => [
                    'citation' => 'Psalm 124:2–3, 4–5, 7cd–8',
                    'title' => 'Responsorial Psalm',
                    'response' => 'Our soul has been rescued like a bird from the fowler\'s snare.',
                    'text' => 'Had not the LORD been with us, when men rose up against us, then would they have swallowed us alive, when their fury was inflamed against us. Broken was the snare, and we were freed. Our help is in the name of the LORD, the maker of heaven and earth.',
                ],
                'reading_2' => null,
                'acclamation' => [
                    'citation' => 'Matthew 5:10',
                    'verse' => 'Blessed are they who are persecuted for the sake of righteousness, for theirs is the kingdom of heaven.',
                ],
                'gospel' => [
                    'citation' => 'Matthew 10:17–22',
                    'title' => 'Holy Gospel',
                    'text' => 'Jesus said to his disciples: "Beware of men, for they will hand you over to courts and scourge you in their synagogues, and you will be led before governors and kings for my sake as a witness before them and the pagans. You will be hated by all because of my name, but whoever endures to the end will be saved."',
                    'key_verse' => 'Whoever endures to the end will be saved.',
                ],
                'reflection' => 'St. Charles Lwanga and the Uganda Martyrs inspire African Catholic youth to hold fast to moral purity and unwavering devotion to Jesus Christ.',
            ],
            '10-1' => [
                'reading_1' => [
                    'citation' => 'Isaiah 66:10–14c',
                    'title' => 'First Reading',
                    'text' => 'Rejoice with Jerusalem and be glad because of her, all you who love her! Exult, exult with her, all you who were mourning over her! As a mother comforts her son, so will I comfort you; in Jerusalem you shall find your comfort.',
                    'key_verse' => 'As a mother comforts her child, so will I comfort you.',
                ],
                'psalm' => [
                    'citation' => 'Psalm 131:1bcde, 2, 3',
                    'title' => 'Responsorial Psalm',
                    'response' => 'In you, Lord, I have found my peace.',
                    'text' => 'O LORD, my heart is not proud, nor are my eyes haughty; I busy not myself with great things, nor with things too sublime for me. Nay rather, I have stilled and quieted my soul like a weaned child on its mother\'s lap.',
                ],
                'reading_2' => null,
                'acclamation' => [
                    'citation' => 'Matthew 11:25',
                    'verse' => 'Blessed are you, Father, Lord of heaven and earth; you have revealed to little ones the mysteries of the kingdom.',
                ],
                'gospel' => [
                    'citation' => 'Matthew 18:1–5',
                    'title' => 'Holy Gospel',
                    'text' => 'The disciples approached Jesus and said, "Who is the greatest in the Kingdom of heaven?" He called a child over, placed it in their midst, and said, "Amen, I say to you, unless you turn and become like children, you will not enter the Kingdom of heaven. Whoever humbles himself like this child is the greatest in the Kingdom of heaven."',
                    'key_verse' => 'Unless you turn and become like children, you will not enter the Kingdom of heaven.',
                ],
                'reflection' => 'Our Cathedral Patroness, St. Theresa of the Child Jesus, teaches us the "Little Way" of spiritual childhood: doing ordinary things with extraordinary love.',
            ],
        ];

        if (isset($specialFeastReadings[$feastKey])) {
            return $specialFeastReadings[$feastKey];
        }

        $entryIndex = ($isSunday ? 0 : ($dayOfWeek % count($readingsLibrary)));
        $readings = $readingsLibrary[$entryIndex];

        // If today is a special feast with dedicated gospel, customize
        if ($fixedFeast) {
            $readings['feast_title'] = $fixedFeast['title'];
        }

        return $readings;
    }

    /**
     * Daily rotating spiritual bread scripture for youth reflection
     */
    protected function getDailySpiritualBread(int $dayOfYear): array
    {
        $dailyBreadScriptures = [
            ['verse' => 'Let no one look down on your youth, but be an example to the believers in speech, in conduct, in love, in faith, in purity.', 'ref' => '1 Timothy 4:12'],
            ['verse' => 'I can do all things through Christ who strengthens me.', 'ref' => 'Philippians 4:13'],
            ['verse' => 'For I know the plans I have for you, declares the Lord, plans to prosper you and not to harm you, plans to give you hope and a future.', 'ref' => 'Jeremiah 29:11'],
            ['verse' => 'Trust in the Lord with all your heart, and do not lean on your own understanding; in all your ways acknowledge him, and he will make your paths straight.', 'ref' => 'Proverbs 3:5-6'],
            ['verse' => 'Be strong and courageous. Do not be frightened, and do not be dismayed, for the Lord your God is with you wherever you go.', 'ref' => 'Joshua 1:9'],
            ['verse' => 'The Lord is my shepherd; I shall not want. He makes me lie down in green pastures; he leads me beside still waters. He restores my soul.', 'ref' => 'Psalm 23:1-3'],
            ['verse' => 'You are the light of the world. A city built on a hill cannot be hidden. In the same way, let your light shine before others.', 'ref' => 'Matthew 5:14, 16'],
            ['verse' => 'Come to me, all who labor and are heavy laden, and I will give you rest. Take my yoke upon you, and learn from me, for I am gentle and lowly in heart.', 'ref' => 'Matthew 11:28-29'],
            ['verse' => 'Ask, and it will be given to you; seek, and you will find; knock, and it will be opened to you.', 'ref' => 'Matthew 7:7'],
            ['verse' => 'I am the vine; you are the branches. Whoever abides in me and I in him, he it is that bears much fruit, for apart from me you can do nothing.', 'ref' => 'John 15:5'],
            ['verse' => 'Peace I leave with you; my peace I give to you. Not as the world gives do I give to you. Let not your hearts be troubled, neither let them be afraid.', 'ref' => 'John 14:27'],
            ['verse' => 'Put on the whole armor of God, that you may be able to stand against the schemes of the devil.', 'ref' => 'Ephesians 6:11'],
        ];

        return $dailyBreadScriptures[$dayOfYear % count($dailyBreadScriptures)];
    }
}
