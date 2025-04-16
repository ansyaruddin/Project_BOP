<?php

namespace App\Filament\Resources\JasaMedisDetailResource\Pages;

use App\Filament\Resources\JasaMedisDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJasaMedisDetails extends ListRecords
{
    protected static string $resource = JasaMedisDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
