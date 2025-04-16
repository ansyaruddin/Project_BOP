<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Ticket extends Model
{

    protected $casts = [
        'owner_id' => 'integer',
    ];
    
    protected $fillable = [
        'title',
        'content',
        'status',
        'priority',
        'phone',
        'expected_transfer_date',

        // Budgeting
        'bop_budget',
        'thl_budget',
        'rujukan_budget',
        'jasamedis_budget',

        // Approval flow
        'responsible_role',
        'responsible_area',
        'responsible_unit',
        'current_step_order',

        // Foreign keys
        'project_id',
        'owner_id',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }


    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bopDetails(): HasMany
    {
        return $this->hasMany(BopDetail::class);
    }

    public function thlDetails(): HasMany
    {
        return $this->hasMany(ThlDetail::class);
    }

    public function rujukanDetails(): HasMany
    {
        return $this->hasMany(RujukanDetail::class);
    }

    public function jasamedisDetails(): HasMany
    {
        return $this->hasMany(JasaMedisDetail::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvalSteps(): Collection
    {
        return $this->project?->approvalSteps ?? collect();
    }

    public function sortedApprovalSteps(): Collection
    {
        return $this->approvalSteps()->sortBy('step_order')->values();
    }

    public function currentApprovalStep(): ?ApprovalStep
    {
        return $this->approvalSteps()
            ->where('step_order', $this->current_step_order)
            ->first();
    }

    public function finalApprovalStep(): ?ApprovalStep
    {
        return $this->sortedApprovalSteps()->last();
    }

    public function moveToStep(ApprovalStep $step): void
    {
        $this->current_step_order = $step->step_order;
        $this->responsible_role = $step->role;
        $this->responsible_area = $step->area;
        $this->responsible_unit = $step->unit;
    }

    public function isUserResponsible(User $user): bool
    {
        $step = $this->currentApprovalStep();

        if (! $step) return false;

        $matchesRole = $user->role === $step->role;

        $matchesArea = match ($step->area) {
            'branch' => $user->region === $this->owner?->region,
            'main'   => $user->unit === $step->unit,
            default  => false
        };

        return $matchesRole && $matchesArea;
    }

    public function scopeVisibleToUser($query, User $user)
{
    return $query->where(function ($query) use ($user) {
        $query->where('owner_id', $user->id)
            ->orWhere(function ($query) use ($user) {
                $query->where('responsible_role', $user->role)
                      ->where(function ($subQuery) use ($user) {
                          $subQuery->where(function ($q) use ($user) {
                              $q->where('responsible_area', 'branch')
                                ->whereHas('owner', fn ($q) => $q->where('region', $user->region));
                          })->orWhere(function ($q) use ($user) {
                              $q->where('responsible_area', 'main')
                                ->where('responsible_unit', $user->unit);
                          });
                      });
            })
            ->orWhereHas('viewableUsers', fn ($q) => $q->where('users.id', $user->id));
    });
}


    public function viewableUsers()
    {
        return $this->belongsToMany(User::class, 'ticket_user_views')->withTimestamps();
    }


    public function approve(string $comment, User $user)
    {
        // $this->addComment($comment, $user);

        $steps = $this->sortedApprovalSteps();
        $currentIndex = $steps->search(fn($step) => $step->step_order === $this->current_step_order);
        $nextStep = $steps->get($currentIndex + 1);

        if ($nextStep) {
            $this->moveToStep($nextStep);
            $this->status = 'Waiting for approval: ' . $nextStep->role;
        } else {
            $this->status = 'approved';
        }
        $this->viewableUsers()->syncWithoutDetaching($user->id);
        $this->save();
    }

    public function reject(string $comment, User $user)
    {
        // $this->addComment($comment, $user);

        $steps = $this->sortedApprovalSteps();
        $currentIndex = $steps->search(fn($step) => $step->step_order === $this->current_step_order);
        $prevStep = $steps->get($currentIndex - 1);

        if ($prevStep) {
            $this->moveToStep($prevStep);
            $this->status = 'Waiting for approval: ' . $prevStep->role;
        } else {
            $this->status = 'revision';
        }
        $this->viewableUsers()->syncWithoutDetaching($user->id);
        $this->save();
    }

    // public function addComment(string $comment, User $user): void
    // {
    //     $this->comments()->create([
    //         'user_id' => $user->id,
    //         'comment' => $comment,
    //     ]);
    // }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            $steps = $ticket->project?->approvalSteps;

            $firstStep = $steps
                ?->where('role', '!=', 'requester')
                ->sortBy('step_order')
                ->first();

            if ($firstStep) {
                $ticket->moveToStep($firstStep);
            }
        });
    }

}
