<?php

namespace App\Filament\Resources\RujukanDetailResource\Pages;

use App\Filament\Resources\RujukanDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRujukanDetail extends EditRecord
{
    protected static string $resource = RujukanDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
