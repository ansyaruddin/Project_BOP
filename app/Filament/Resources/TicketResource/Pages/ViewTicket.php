<?php

namespace App\Filament\Resources\TicketResource\Pages;

use Filament\Forms\Components\Textarea;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Filament\Resources\TicketResource;


class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public ?string $comment = null;

}

