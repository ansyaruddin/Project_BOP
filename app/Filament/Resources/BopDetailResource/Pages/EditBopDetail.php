<?php

namespace App\Filament\Resources\BopDetailResource\Pages;

use App\Filament\Resources\BopDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBopDetail extends EditRecord
{
    protected static string $resource = BopDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
