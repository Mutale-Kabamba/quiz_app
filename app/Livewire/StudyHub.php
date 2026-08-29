<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\StudyNote;
use Livewire\Component;

class StudyHub extends Component
{
    public ?int $selectedCategoryId = null;
    public string $search = '';
    public ?string $activeNoteId = null;

    public function selectCategory(?int $id)
    {
        $this->selectedCategoryId = $id;
        $this->activeNoteId = null;
    }

    public function openNote(string $noteId)
    {
        $this->activeNoteId = $this->activeNoteId === $noteId ? null : $noteId;
    }

    public function render()
    {
        $categories = Category::withCount('studyNotes')->orderBy('display_order')->get();

        $notesQuery = StudyNote::with('category');

        if ($this->selectedCategoryId) {
            $notesQuery->where('category_id', $this->selectedCategoryId);
        }

        if (!empty($this->search)) {
            $notesQuery->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('subheading', 'like', "%{$this->search}%")
                  ->orWhere('content_body', 'like', "%{$this->search}%")
                  ->orWhere('reference_code', 'like', "%{$this->search}%");
            });
        }

        $notes = $notesQuery->latest()->get();

        return view('livewire.study-hub', [
            'categories' => $categories,
            'notes' => $notes,
        ])->layout('components.layouts.app', ['title' => 'Study & Resource Hub • Diocese of Livingstone']);
    }
}
