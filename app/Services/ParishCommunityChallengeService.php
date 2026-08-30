<?php

namespace App\Services;

use App\Models\Parish;
use App\Models\ParishFormationChallenge;
use App\Models\TaxonomyTopic;
use App\Models\User;
use App\Models\UserParishChallengeEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ParishCommunityChallengeService
{
    public function __construct(
        protected GamificationService $gamificationService
    ) {}

    /**
     * Get active formation challenges for the parish.
     */
    public function getActiveChallengesForParish(Parish $parish): Collection
    {
        return ParishFormationChallenge::with(['parish', 'challengerParish', 'topic'])
            ->where(function ($q) use ($parish) {
                $q->where('parish_id', $parish->id)
                  ->orWhere('challenger_parish_id', $parish->id);
            })
            ->where('status', 'active')
            ->get();
    }

    /**
     * Record a youth's contribution toward their parish formation challenge.
     */
    public function recordContribution(User $user, int $xpEarned, int $tasksCount = 1): void
    {
        if (!$user->parish_id) {
            return;
        }

        $activeChallenges = ParishFormationChallenge::where('status', 'active')
            ->where(function ($q) use ($user) {
                $q->where('parish_id', $user->parish_id)
                  ->orWhere('challenger_parish_id', $user->parish_id);
            })
            ->get();

        foreach ($activeChallenges as $challenge) {
            $entry = UserParishChallengeEntry::firstOrNew([
                'challenge_id' => $challenge->id,
                'user_id' => $user->id,
            ]);

            $entry->parish_id = $user->parish_id;
            $entry->contribution_xp += $xpEarned;
            $entry->tasks_completed += $tasksCount;
            $entry->save();
        }
    }

    /**
     * Get standings for an inter-parish challenge.
     */
    public function getChallengeStandings(ParishFormationChallenge $challenge): array
    {
        $parish1Xp = UserParishChallengeEntry::where('challenge_id', $challenge->id)
            ->where('parish_id', $challenge->parish_id)
            ->sum('contribution_xp');

        $parish1Youths = UserParishChallengeEntry::where('challenge_id', $challenge->id)
            ->where('parish_id', $challenge->parish_id)
            ->count();

        $parish2Xp = 0;
        $parish2Youths = 0;

        if ($challenge->challenger_parish_id) {
            $parish2Xp = UserParishChallengeEntry::where('challenge_id', $challenge->id)
                ->where('parish_id', $challenge->challenger_parish_id)
                ->sum('contribution_xp');

            $parish2Youths = UserParishChallengeEntry::where('challenge_id', $challenge->id)
                ->where('parish_id', $challenge->challenger_parish_id)
                ->count();
        }

        return [
            'parish_1' => [
                'parish' => $challenge->parish,
                'total_xp' => $parish1Xp,
                'youth_count' => $parish1Youths,
            ],
            'parish_2' => [
                'parish' => $challenge->challengerParish,
                'total_xp' => $parish2Xp,
                'youth_count' => $parish2Youths,
            ],
        ];
    }
}
