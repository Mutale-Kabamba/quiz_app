<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionBankItem;
use App\Models\SaintProfile;
use App\Models\TaxonomyConcept;
use App\Models\TaxonomyTopic;

class CatholicDoctrinalExplanationService
{
    /**
     * Provide structured doctrinal explanation for a concept or topic.
     */
    public function getExplanation(string|TaxonomyConcept $concept): array
    {
        if (is_string($concept)) {
            $conceptModel = TaxonomyConcept::where('name', 'like', "%{$concept}%")->first();
            $title = $concept;
            $definition = $conceptModel?->summary_definition ?? "Core Catholic doctrine rooted in Sacred Scripture and Apostolic Tradition.";
            $ccc = "CCC 1376, CCC 1210-1216";
            $scripture = "1 Corinthians 11:23-26, John 6:51-58";
        } else {
            $conceptModel = $concept;
            $title = $conceptModel->name;
            $definition = $conceptModel->summary_definition ?? "Essential Catholic teaching.";
            $ccc = "CCC 1376, CCC 1210-1216";
            $scripture = "1 Corinthians 11:23-26, John 6:51-58";
        }

        $saint = SaintProfile::inRandomOrder()->first();

        return [
            'concept_title' => $title,
            'simple_explanation' => "In simple terms: {$definition}",
            'doctrinal_explanation' => "The Catholic Church teaches that this truth is revealed by God to guide our salvation, celebrated in the sacred Liturgy, and lived in moral charity.",
            'scripture_citation' => $scripture,
            'catechism_citation' => $ccc,
            'related_saint' => $saint ? [
                'name' => $saint->name,
                'patronage' => $saint->patronage,
                'quote' => $saint->quote ?? "Love God above all things.",
            ] : null,
            'practice_tip' => "Reflect on this truth during your daily prayer and when participating in the Holy Mass.",
        ];
    }
}
