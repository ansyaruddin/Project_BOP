<?php

namespace App\Filament\Resources\ThlDetailResource\Pages;

use App\Filament\Resources\ThlDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditThlDetail extends EditRecord
{
    protected static string $resource = ThlDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
