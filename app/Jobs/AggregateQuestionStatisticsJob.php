<?php

namespace App\Jobs;

use App\Models\QuestionBankItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AggregateQuestionStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Batch calculate empirical difficulty & health across active questions
        QuestionBankItem::where('times_answered', '>=', 5)->chunk(200, function ($questions) {
            foreach ($questions as $q) {
                $empirical = round($q->times_correct / $q->times_answered, 3);
                $health = 100;
                if ($q->times_answered >= 20) {
                    if ($empirical < 0.10) {
                        $health -= 30;
                    } elseif ($empirical > 0.98) {
                        $health -= 10;
                    }
                }

                $q->update([
                    'empirical_difficulty' => $empirical,
                    'health_score' => max(0, min(100, $health)),
                ]);
            }
        });
    }
}
