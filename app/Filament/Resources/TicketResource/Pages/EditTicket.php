<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }


    protected function getFormActions(): array
    {
        $user = Auth::user();

        if (! $this->record->isUserResponsible($user)) {
            return []; // No buttons if not responsible
        }

        return [
            Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-m-check')
                ->action(function () use ($user) {
                    $this->record->approve('Approved without comment', $user); 
                    // $this->notify('success', 'Ticket approved successfully.');
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            
            Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-m-x-mark')
                ->action(function () use ($user) {
                    $this->record->reject('Rejected without comment', $user); 
                    // $this->notify('success', 'Ticket rejected successfully.');
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
