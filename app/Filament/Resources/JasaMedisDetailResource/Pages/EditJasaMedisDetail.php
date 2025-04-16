<?php

namespace App\Filament\Resources\JasaMedisDetailResource\Pages;

use App\Filament\Resources\JasaMedisDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJasaMedisDetail extends EditRecord
{
    protected static string $resource = JasaMedisDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
