<?php

namespace App\Filament\Resources\RujukanDetailResource\Pages;

use App\Filament\Resources\RujukanDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRujukanDetails extends ListRecords
{
    protected static string $resource = RujukanDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
