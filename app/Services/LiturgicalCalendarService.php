<?php

namespace App\Services;

use Carbon\Carbon;

class LiturgicalCalendarService
{
    /**
     * Get the current liturgical season, color accents, and scripture for today.
     */
    public function getCurrentContext(): array
    {
        $now = Carbon::now();
        $month = (int) $now->format('n');
        $day = (int) $now->format('j');

        // Determine liturgical season (simplified liturgical calendar calculation)
        // Green: Ordinary Time, Purple: Lent / Advent, Gold/White: Easter / Christmas, Red: Pentecost / Martyrs, Rose: Gaudete / Laetare
        $season = 'Ordinary Time';
        $colorKey = 'green';
        $colorHex = '#059669';
        $colorBg = 'bg-emerald-50 dark:bg-emerald-950/30';
        $colorText = 'text-emerald-700 dark:text-emerald-300';
        $colorBorder = 'border-emerald-200 dark:border-emerald-800';
        $verse = 'Thy word is a lamp unto my feet, and a light unto my path. (Psalm 119:105)';
        $verseRef = 'Psalm 119:105';
        $saintOfDay = 'St. Augustine of Hippo, Doctor of Grace';

        if ($month === 12 && $day <= 24) {
            $season = 'Advent';
            $colorKey = 'purple';
            $colorHex = '#7C3AED';
            $colorBg = 'bg-purple-50 dark:bg-purple-950/30';
            $colorText = 'text-purple-700 dark:text-purple-300';
            $colorBorder = 'border-purple-200 dark:border-purple-800';
            $verse = 'A voice of one calling in the wilderness: Prepare the way of the Lord. (Mark 1:3)';
            $verseRef = 'Mark 1:3';
            $saintOfDay = 'St. Francis Xavier, Missionary';
        } elseif (($month === 12 && $day >= 25) || ($month === 1 && $day <= 10)) {
            $season = 'Christmas Season';
            $colorKey = 'gold';
            $colorHex = '#D97706';
            $colorBg = 'bg-amber-50 dark:bg-amber-950/30';
            $colorText = 'text-amber-700 dark:text-amber-300';
            $colorBorder = 'border-amber-200 dark:border-amber-800';
            $verse = 'The Word became flesh and made his dwelling among us. (John 1:14)';
            $verseRef = 'John 1:14';
            $saintOfDay = 'The Holy Family of Nazareth';
        } elseif ($month >= 2 && $month <= 4 && $day <= 15) {
            $season = 'Lenten Season';
            $colorKey = 'purple';
            $colorHex = '#7C3AED';
            $colorBg = 'bg-purple-50 dark:bg-purple-950/30';
            $colorText = 'text-purple-700 dark:text-purple-300';
            $colorBorder = 'border-purple-200 dark:border-purple-800';
            $verse = 'Repent, and believe in the Gospel. (Mark 1:15)';
            $verseRef = 'Mark 1:15';
            $saintOfDay = 'St. Josephine Bakhita, Patroness of Hope';
        } elseif ($month >= 4 && $month <= 5) {
            $season = 'Easter Season';
            $colorKey = 'gold';
            $colorHex = '#D97706';
            $colorBg = 'bg-amber-50 dark:bg-amber-950/30';
            $colorText = 'text-amber-700 dark:text-amber-300';
            $colorBorder = 'border-amber-200 dark:border-amber-800';
            $verse = 'He is not here; he has risen, just as he said! (Matthew 28:6)';
            $verseRef = 'Matthew 28:6';
            $saintOfDay = 'St. Charles Lwanga & Companions, Martyrs';
        }

        // Daily rotating scripture inspiration for youth
        $dailyScriptures = [
            ['verse' => 'Let no one look down on your youth, but be an example to the believers in speech, in conduct, in love, in faith, in purity.', 'ref' => '1 Timothy 4:12'],
            ['verse' => 'I can do all things through Christ who strengthens me.', 'ref' => 'Philippians 4:13'],
            ['verse' => 'For I know the plans I have for you, declares the Lord, plans to prosper you and not to harm you, plans to give you hope and a future.', 'ref' => 'Jeremiah 29:11'],
            ['verse' => 'Trust in the Lord with all your heart, and do not lean on your own understanding.', 'ref' => 'Proverbs 3:5'],
            ['verse' => 'Be strong and courageous. Do not be frightened, for the Lord your God is with you wherever you go.', 'ref' => 'Joshua 1:9'],
            ['verse' => 'The Lord is my shepherd; I shall not want. He makes me lie down in green pastures.', 'ref' => 'Psalm 23:1-2'],
            ['verse' => 'You are the light of the world. A city set on a hill cannot be hidden.', 'ref' => 'Matthew 5:14'],
        ];

        $dayIndex = (int) $now->dayOfWeek;
        $selectedScripture = $dailyScriptures[$dayIndex % count($dailyScriptures)];

        return [
            'season' => $season,
            'color_key' => $colorKey,
            'color_hex' => $colorHex,
            'color_bg' => $colorBg,
            'color_text' => $colorText,
            'color_border' => $colorBorder,
            'verse' => $selectedScripture['verse'],
            'verse_ref' => $selectedScripture['ref'],
            'saint_of_day' => $saintOfDay,
            'date_formatted' => $now->format('l, F j, Y'),
            'diocesan_patroness' => "St. Theresa of the Child Jesus (Cathedral Patroness)",
        ];
    }
}
