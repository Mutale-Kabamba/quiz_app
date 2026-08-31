<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\DiocesanCompetition;
use App\Models\RallyJoinRequest;
use App\Models\RallyParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RallyAccessService
{
    /**
     * Characters used for secure access code generation (omitting 0, O, 1, I, 5, S for zero ambiguity)
     */
    protected const CODE_CHARSET = '2346789ABCDEFGHJKLMNPQRTUVWXYZ';

    /**
     * Generate a cryptographically secure, collision-safe, formatted access code
     * Format: LV26-XXXX-XX (e.g. LV26-K7X9-P2)
     */
    public function generateSecureAccessCode(DiocesanCompetition $rally, User $user): string
    {
        $maxAttempts = 10;
        $charsetLen = strlen(self::CODE_CHARSET);

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $part1 = '';
            for ($i = 0; $i < 4; $i++) {
                $part1 .= self::CODE_CHARSET[random_int(0, $charsetLen - 1)];
            }

            $part2 = '';
            for ($i = 0; $i < 2; $i++) {
                $part2 .= self::CODE_CHARSET[random_int(0, $charsetLen - 1)];
            }

            $code = 'LV26-' . $part1 . '-' . $part2;

            // Check uniqueness in database
            $exists = RallyParticipant::where('access_code', $code)->exists();
            if (!$exists) {
                return $code;
            }
        }

        // Fallback with high-entropy UUID segment if collisions occur
        return 'LV26-' . strtoupper(substr(Str::uuid()->toString(), 0, 4)) . '-' . strtoupper(substr(Str::uuid()->toString(), 4, 2));
    }

    /**
     * Comprehensive Server-Side Access & Eligibility Validation
     */
    public function validateRallyAccess(
        DiocesanCompetition $rally,
        User $user,
        ?string $providedCode = null
    ): array {
        // 1. Role validation: Super Admin & Chairperson can manage/preview; Youth are primary participants
        if (!$user->isYouth() && !$user->isSuperAdmin() && !$user->isChairperson()) {
            return [
                'allowed' => false,
                'error_code' => 'WRONG_ROLE',
                'message' => 'Rally participation is designated for verified Parish Youth members.',
                'participant' => null,
            ];
        }

        // Super admins have universal access
        if ($user->isSuperAdmin()) {
            $participant = RallyParticipant::firstOrCreate(
                ['rally_id' => $rally->id, 'user_id' => $user->id],
                ['status' => 'active', 'joined_at' => now()]
            );
            return [
                'allowed' => true,
                'error_code' => null,
                'message' => 'Super Administrator access granted.',
                'participant' => $participant,
            ];
        }

        // 2. Rally Status Validation
        $status = strtolower($rally->status ?? 'scheduled');
        if (in_array($status, ['draft', 'cancelled', 'archived', 'closed'])) {
            return [
                'allowed' => false,
                'error_code' => 'RALLY_CLOSED',
                'message' => "This Rally is currently {$status} and cannot be entered.",
                'participant' => null,
            ];
        }

        // 3. Single Attempt Check (Rallies allow only 1 official attempt)
        $completedParticipant = RallyParticipant::where('rally_id', $rally->id)
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->first();

        if ($completedParticipant) {
            return [
                'allowed' => false,
                'error_code' => 'ALREADY_COMPLETED',
                'message' => "You have already completed your official attempt for '{$rally->title}' (Score: {$completedParticipant->score} pts). Rallies allow only 1 attempt.",
                'participant' => $completedParticipant,
            ];
        }

        // 4. Time Window Validation
        $now = now();
        if ($rally->start_time && $now->lt($rally->start_time)) {
            return [
                'allowed' => false,
                'error_code' => 'NOT_STARTED',
                'message' => "This Rally is scheduled to open on " . $rally->start_time->format('d M Y \a\t H:i') . ".",
                'participant' => null,
            ];
        }

        if ($rally->end_time && $now->gt($rally->end_time)) {
            return [
                'allowed' => false,
                'error_code' => 'ENDED',
                'message' => "This Rally closed on " . $rally->end_time->format('d M Y \a\t H:i') . ".",
                'participant' => null,
            ];
        }

        // 4. Clean provided code
        $cleanProvidedCode = $providedCode ? strtoupper(trim($providedCode)) : null;

        // 5. Scope-Specific Eligibility & Code Validation
        $scope = strtolower($rally->scope_type ?? 'diocese');

        switch ($scope) {
            case 'custom':
                // Check if user is an authorized participant
                $participant = RallyParticipant::where('rally_id', $rally->id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$participant) {
                    return [
                        'allowed' => false,
                        'error_code' => 'NOT_INVITED',
                        'message' => 'This is a Custom Invitational Rally. Your account is not on the participant roster.',
                        'participant' => null,
                    ];
                }

                if ($participant->status === 'removed') {
                    return [
                        'allowed' => false,
                        'error_code' => 'REMOVED',
                        'message' => 'Your participation in this Rally has been revoked by an administrator.',
                        'participant' => null,
                    ];
                }

                if ($participant->status === 'rejected') {
                    return [
                        'allowed' => false,
                        'error_code' => 'REJECTED',
                        'message' => 'Your join request for this Rally was not approved.',
                        'participant' => null,
                    ];
                }

                // If code supplied, verify identity binding
                if ($cleanProvidedCode !== null) {
                    if ($participant->access_code !== $cleanProvidedCode) {
                        // Check if code belongs to someone else to provide secure specific error
                        $otherUserParticipant = RallyParticipant::where('rally_id', $rally->id)
                            ->where('access_code', $cleanProvidedCode)
                            ->first();

                        if ($otherUserParticipant && $otherUserParticipant->user_id !== $user->id) {
                            return [
                                'allowed' => false,
                                'error_code' => 'CODE_MISMATCH',
                                'message' => 'This Rally access code is not assigned to your account.',
                                'participant' => null,
                            ];
                        }

                        return [
                            'allowed' => false,
                            'error_code' => 'INVALID_CODE',
                            'message' => 'Invalid Rally access code provided.',
                            'participant' => null,
                        ];
                    }
                }

                // Mark participant active on valid entry
                if ($participant->status !== 'completed') {
                    $participant->update([
                        'status' => 'active',
                        'joined_at' => $participant->joined_at ?? now(),
                    ]);
                }

                return [
                    'allowed' => true,
                    'error_code' => null,
                    'message' => 'Access authorized for custom rally participant.',
                    'participant' => $participant,
                ];

            case 'deanery':
                // User must belong to a parish under the rally's target deanery
                $userParish = $user->parish;
                if (!$userParish || !$rally->deanery_id || $userParish->deanery_id !== $rally->deanery_id) {
                    $deaneryName = $rally->deanery?->name ?? 'the target deanery';
                    return [
                        'allowed' => false,
                        'error_code' => 'DEANERY_MISMATCH',
                        'message' => "This Rally is restricted to parishes within {$deaneryName}.",
                        'participant' => null,
                    ];
                }
                break;

            case 'parish':
                // User must belong to the rally's target parish
                if (!$user->parish_id || $user->parish_id !== $rally->parish_id) {
                    $parishName = $rally->parish?->name ?? 'the target parish';
                    return [
                        'allowed' => false,
                        'error_code' => 'PARISH_MISMATCH',
                        'message' => "This Rally is restricted to members of {$parishName}.",
                        'participant' => null,
                    ];
                }
                break;

            case 'diocese':
            default:
                // User must belong to the diocese (has parish affiliation)
                if (!$user->parish_id) {
                    return [
                        'allowed' => false,
                        'error_code' => 'NO_PARISH',
                        'message' => 'You must be registered under a parish in the Catholic Diocese of Livingstone.',
                        'participant' => null,
                    ];
                }
                break;
        }

        // Shared rally pin / code check for Diocese / Deanery / Parish if configured
        if (!empty($rally->rally_pin) && $cleanProvidedCode !== null) {
            if (strtoupper($rally->rally_pin) !== $cleanProvidedCode) {
                return [
                    'allowed' => false,
                    'error_code' => 'INVALID_PIN',
                    'message' => 'Incorrect Rally PIN entered.',
                    'participant' => null,
                ];
            }
        }

        // Create or get participant record for Diocese/Deanery/Parish scope
        $participant = RallyParticipant::firstOrCreate(
            ['rally_id' => $rally->id, 'user_id' => $user->id],
            [
                'status' => 'active',
                'joined_at' => now(),
                'access_code' => $this->generateSecureAccessCode($rally, $user),
            ]
        );

        if ($participant->status === 'removed') {
            return [
                'allowed' => false,
                'error_code' => 'REMOVED',
                'message' => 'Your participation in this Rally has been revoked by an administrator.',
                'participant' => null,
            ];
        }

        return [
            'allowed' => true,
            'error_code' => null,
            'message' => 'Access authorized.',
            'participant' => $participant,
        ];
    }

    /**
     * Add a participant to a Custom Rally with a fresh, unique, cryptographically secure code
     */
    public function addCustomParticipant(
        DiocesanCompetition $rally,
        User $user,
        ?User $admin = null
    ): RallyParticipant {
        return DB::transaction(function () use ($rally, $user, $admin) {
            $code = $this->generateSecureAccessCode($rally, $user);

            $participant = RallyParticipant::updateOrCreate(
                [
                    'rally_id' => $rally->id,
                    'user_id' => $user->id,
                ],
                [
                    'access_code' => $code,
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => $admin?->id,
                    'joined_at' => now(),
                ]
            );

            // Create Audit Log
            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $admin?->id ?? $user->id,
                'action' => 'rally_participant_added',
                'entity_type' => RallyParticipant::class,
                'entity_id' => (string) $participant->id,
                'new_values' => [
                    'rally_id' => $rally->id,
                    'rally_title' => $rally->title,
                    'participant_id' => $user->id,
                    'participant_name' => $user->name,
                    'access_code_prefix' => substr($code, 0, 4) . '***',
                ],
            ]);

            return $participant;
        });
    }

    /**
     * Remove a participant from a Rally (Immediate code revocation)
     */
    public function removeParticipant(
        DiocesanCompetition $rally,
        User $user,
        ?User $admin = null
    ): bool {
        return DB::transaction(function () use ($rally, $user, $admin) {
            $participant = RallyParticipant::where('rally_id', $rally->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$participant) {
                return false;
            }

            $oldCode = $participant->access_code;

            $participant->update([
                'status' => 'removed',
                'access_code' => null, // Immediately invalidate code
            ]);

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $admin?->id ?? $user->id,
                'action' => 'rally_participant_removed',
                'entity_type' => RallyParticipant::class,
                'entity_id' => (string) $participant->id,
                'old_values' => [
                    'status' => 'approved',
                    'access_code_prefix' => $oldCode ? substr($oldCode, 0, 4) . '***' : null,
                ],
                'new_values' => [
                    'status' => 'removed',
                    'access_code' => null,
                ],
            ]);

            return true;
        });
    }

    /**
     * Regenerate a participant's access code (invalidates old code immediately)
     */
    public function regenerateParticipantCode(
        RallyParticipant $participant,
        ?User $admin = null
    ): string {
        return DB::transaction(function () use ($participant, $admin) {
            $oldCode = $participant->access_code;
            $newCode = $this->generateSecureAccessCode($participant->rally, $participant->user);

            $participant->update([
                'access_code' => $newCode,
                'status' => in_array($participant->status, ['removed', 'rejected']) ? 'approved' : $participant->status,
            ]);

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $admin?->id ?? $participant->user_id,
                'action' => 'rally_code_regenerated',
                'entity_type' => RallyParticipant::class,
                'entity_id' => (string) $participant->id,
                'old_values' => [
                    'access_code_prefix' => $oldCode ? substr($oldCode, 0, 4) . '***' : null,
                ],
                'new_values' => [
                    'access_code_prefix' => substr($newCode, 0, 4) . '***',
                ],
            ]);

            return $newCode;
        });
    }

    /**
     * Submit a join request for a public Rally
     */
    public function submitJoinRequest(
        DiocesanCompetition $rally,
        User $user,
        ?string $message = null
    ): RallyJoinRequest {
        return DB::transaction(function () use ($rally, $user, $message) {
            // Prevent duplicate pending requests
            $existing = RallyJoinRequest::where('rally_id', $rally->id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($existing) {
                return $existing;
            }

            $request = RallyJoinRequest::create([
                'id' => (string) Str::uuid(),
                'rally_id' => $rally->id,
                'user_id' => $user->id,
                'status' => 'pending',
                'message' => $message,
            ]);

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'action' => 'rally_join_request_submitted',
                'entity_type' => RallyJoinRequest::class,
                'entity_id' => (string) $request->id,
                'new_values' => [
                    'rally_id' => $rally->id,
                    'rally_title' => $rally->title,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                ],
            ]);

            return $request;
        });
    }

    /**
     * Approve a Rally Join Request (generates unique code, creates participant record, logs action)
     */
    public function approveJoinRequest(
        RallyJoinRequest $request,
        User $admin
    ): RallyParticipant {
        return DB::transaction(function () use ($request, $admin) {
            $rally = $request->rally;
            $user = $request->user;

            $request->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $code = $this->generateSecureAccessCode($rally, $user);

            $participant = RallyParticipant::updateOrCreate(
                [
                    'rally_id' => $rally->id,
                    'user_id' => $user->id,
                ],
                [
                    'access_code' => $code,
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => $admin->id,
                    'joined_at' => now(),
                ]
            );

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $admin->id,
                'action' => 'rally_join_request_approved',
                'entity_type' => RallyJoinRequest::class,
                'entity_id' => (string) $request->id,
                'new_values' => [
                    'rally_id' => $rally->id,
                    'user_id' => $user->id,
                    'participant_id' => $participant->id,
                    'access_code_prefix' => substr($code, 0, 4) . '***',
                ],
            ]);

            return $participant;
        });
    }

    /**
     * Reject a Rally Join Request with optional reason
     */
    public function rejectJoinRequest(
        RallyJoinRequest $request,
        User $admin,
        ?string $reason = null
    ): bool {
        return DB::transaction(function () use ($request, $admin, $reason) {
            $request->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $admin->id,
                'action' => 'rally_join_request_rejected',
                'entity_type' => RallyJoinRequest::class,
                'entity_id' => (string) $request->id,
                'new_values' => [
                    'rally_id' => $request->rally_id,
                    'user_id' => $request->user_id,
                    'rejection_reason' => $reason,
                ],
            ]);

            return true;
        });
    }

    /**
     * Get discoverable / available rallies for a specific youth user
     */
    public function getAvailableRalliesForUser(User $user): Collection
    {
        // Get IDs of rallies where user is already an approved, active, or pending participant
        $userParticipantRallyIds = RallyParticipant::where('user_id', $user->id)
            ->whereNotIn('status', ['rejected', 'removed'])
            ->pluck('rally_id')
            ->toArray();

        // Get IDs of rallies where user has a pending join request
        $pendingRequestRallyIds = RallyJoinRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('rally_id')
            ->toArray();

        $excludedRallyIds = array_unique(array_merge($userParticipantRallyIds, $pendingRequestRallyIds));

        return DiocesanCompetition::with(['deanery', 'parish', 'category'])
            ->where('is_public', true)
            ->whereNotIn('status', ['draft', 'cancelled', 'archived', 'concluded'])
            ->whereNotIn('id', $excludedRallyIds)
            ->orderByRaw("CASE WHEN status = 'live' THEN 1 WHEN status = 'scheduled' THEN 2 ELSE 3 END")
            ->orderBy('start_time')
            ->get()
            ->filter(function (DiocesanCompetition $rally) use ($user) {
                // Scope check
                $scope = strtolower($rally->scope_type ?? 'diocese');
                if ($scope === 'deanery') {
                    return $user->parish?->deanery_id === $rally->deanery_id;
                }
                if ($scope === 'parish') {
                    return $user->parish_id === $rally->parish_id;
                }
                if ($scope === 'custom') {
                    // For custom rallies, only show in discovery if join requests are enabled
                    return (bool) ($rally->join_requests_enabled ?? false);
                }
                return true;
            });
    }

    /**
     * Get all rallies connected to a specific user (My Rallies)
     */
    public function getUserRallies(User $user): array
    {
        $participations = RallyParticipant::with('rally.category')
            ->where('user_id', $user->id)
            ->get();

        $active = [];
        $upcoming = [];
        $completed = [];

        foreach ($participations as $p) {
            $rally = $p->rally;
            if (!$rally) continue;

            if ($p->status === 'completed' || $rally->status === 'completed') {
                $completed[] = [
                    'participant' => $p,
                    'rally' => $rally,
                ];
            } elseif ($rally->isLiveNow()) {
                $active[] = [
                    'participant' => $p,
                    'rally' => $rally,
                ];
            } else {
                $upcoming[] = [
                    'participant' => $p,
                    'rally' => $rally,
                ];
            }
        }

        $pendingRequests = RallyJoinRequest::with('rally.category')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->get();

        return [
            'active' => $active,
            'upcoming' => $upcoming,
            'completed' => $completed,
            'pending_requests' => $pendingRequests,
        ];
    }
}
