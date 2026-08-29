<?php

namespace App\Services;

use App\Models\User;
use App\Models\XpTransaction;
use Illuminate\Support\Facades\DB;

class XpLedgerService
{
    /**
     * Award or adjust XP via immutable transaction ledger
     */
    public function awardXp(
        User $user,
        int $amount,
        string $sourceType,
        ?string $sourceId = null,
        string $description = ''
    ): XpTransaction {
        return DB::transaction(function () use ($user, $amount, $sourceType, $sourceId, $description) {
            // 1. Create Ledger Entry
            $transaction = XpTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description ?: "XP awarded for {$sourceType}",
                'created_at' => now(),
            ]);

            // 2. Authoritative XP calculation
            $totalXp = max(0, ($user->xp ?? 0) + $amount);

            // 3. Recalculate Level: Level = floor(sqrt(XP / 100)) + 1
            $newLevel = (int) (floor(sqrt($totalXp / 100)) + 1);

            $user->update([
                'xp' => $totalXp,
                'level' => $newLevel,
                'last_activity_date' => now()->toDateString(),
            ]);

            return $transaction;
        });
    }

    /**
     * Correct a user's score/XP with administrative audit
     */
    public function correctXp(User $admin, User $user, int $adjustment, string $reason): XpTransaction
    {
        $oldXp = $user->xp;

        $transaction = $this->awardXp(
            $user,
            $adjustment,
            'admin_adjustment',
            (string) $admin->id,
            "Administrative correction: {$reason}"
        );

        app(AuditLogService::class)->log(
            'xp_score_corrected',
            $user,
            ['xp' => $oldXp],
            ['xp' => $user->fresh()->xp, 'adjustment' => $adjustment, 'reason' => $reason],
            $admin
        );

        return $transaction;
    }
}
