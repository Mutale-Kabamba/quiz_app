<?php

namespace App\Services;

use App\Models\DailyChallenge;
use App\Models\SaintProfile;
use App\Models\StudyResource;
use App\Models\TaxonomyTopic;
use App\Models\User;
use App\Models\UserTopicMastery;
use Carbon\Carbon;

class StudyRecommendationEngine
{
    /**
     * Generate a personalized home feed for a youth user
     */
    public function getPersonalizedFeed(User $user): array
    {
        $today = Carbon::today();
        $monthDay = $today->format('m-d');

        // 1. Saint of the Day (Universal + African Church)
        $saintToday = SaintProfile::where('feast_day_month_day', $monthDay)->first()
            ?? SaintProfile::inRandomOrder()->first();

        // 2. Weak Areas from Mastery Model
        $weakTopicMasteries = app(AdaptiveMasteryService::class)->getWeakTopics($user, 3);
        $weakTopicIds = $weakTopicMasteries->pluck('topic_id')->toArray();

        $weakAreaResources = StudyResource::where('status', 'PUBLISHED')
            ->whereIn('topic_id', $weakTopicIds)
            ->limit(4)
            ->get();

        // 3. Quick 5-Minute Study Notes
        $quickReads = StudyResource::where('status', 'PUBLISHED')
            ->where('estimated_read_minutes', '<=', 5)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // 4. Daily Formation Challenge
        $dailyChallenge = DailyChallenge::where('challenge_date', $today->toDateString())
            ->where('is_active', true)
            ->first();

        // 5. Featured Deep Dives
        $featuredStudy = StudyResource::where('status', 'PUBLISHED')
            ->whereIn('resource_type', ['LESSON', 'STUDY_GUIDE', 'DEEP_DIVE'])
            ->latest('published_at')
            ->limit(4)
            ->get();

        return [
            'saint_of_the_day' => $saintToday,
            'daily_challenge' => $dailyChallenge,
            'weak_areas' => [
                'topics' => $weakTopicMasteries,
                'resources' => $weakAreaResources,
            ],
            'quick_reads' => $quickReads,
            'featured_study' => $featuredStudy,
        ];
    }
}
