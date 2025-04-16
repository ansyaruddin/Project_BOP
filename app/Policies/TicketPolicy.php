<?php
namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {

        \Log::info('Ticket view check', [
            'user_id' => $user->id,
            'owner_id' => $ticket->owner_id,
            'is_owner' => $user->id === $ticket->owner_id,
            'is_responsible' => $ticket->isUserResponsible($user),
            'is_viewable_user' => $ticket->viewableUsers()->where('user_id', $user->id)->exists(),
        ]);
        
        return
            $user->id ==+ $ticket->owner_id ||
            $ticket->isUserResponsible($user) ||
            $ticket->viewableUsers()->where('user_id', $user->id)->exists();
    }

    
    public function viewAny(User $user): bool
    {   
        return true; 
    }

        public function update(User $user, Ticket $ticket): bool
    {
        return $ticket->isUserResponsible($user);
    }


    public function approve(User $user, Ticket $ticket): bool
    {
        return $ticket->isUserResponsible($user) && $ticket->status === 'pending';

    }

    public function reject(User $user, Ticket $ticket): bool
    {
        return $ticket->isUserResponsible($user);

    }

    public function finish(User $user, Ticket $ticket): bool
    {
        return $ticket->isUserResponsible($user);

    }
}
