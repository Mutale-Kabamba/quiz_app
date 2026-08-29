<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChairpersonApproval extends Component
{
    public ?string $selectedUserId = null;
    public string $rejectionReason = '';
    public bool $showRejectModal = false;

    public function mount()
    {
        $user = Auth::user();
        if (!$user || (!$user->isChairperson() && !$user->isSuperAdmin())) {
            abort(403, 'Unauthorized access to Parish Chairperson portal.');
        }
    }

    public function approve(string $userId)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        session()->flash('message', "{$user->name} has been approved for ranked competitions!");
    }

    public function openRejectModal(string $userId)
    {
        $this->selectedUserId = $userId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function confirmReject()
    {
        $this->validate([
            'rejectionReason' => 'required|min:3',
        ]);

        if ($this->selectedUserId) {
            $user = User::findOrFail($this->selectedUserId);
            $user->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $this->rejectionReason,
            ]);

            $this->showRejectModal = false;
            $this->selectedUserId = null;
            session()->flash('message', "{$user->name}'s registration was rejected.");
        }
    }

    public function render()
    {
        $currentUser = Auth::user();

        $pendingYouths = User::where('role', 'youth')
            ->where('status', 'pending')
            ->when($currentUser->isChairperson(), fn($q) => $q->where('parish_id', $currentUser->parish_id))
            ->with('parish')
            ->latest('created_at')
            ->get();

        $approvedYouthsCount = User::where('role', 'youth')
            ->where('status', 'approved')
            ->when($currentUser->isChairperson(), fn($q) => $q->where('parish_id', $currentUser->parish_id))
            ->count();

        return view('livewire.chairperson-approval', [
            'pendingYouths' => $pendingYouths,
            'approvedYouthsCount' => $approvedYouthsCount,
            'parish' => $currentUser->parish,
        ])->layout('components.layouts.app', ['title' => 'Parish Youth Approvals • Diocese of Livingstone']);
    }
}
