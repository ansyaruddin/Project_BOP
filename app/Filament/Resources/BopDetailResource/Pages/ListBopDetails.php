<?php

namespace App\Filament\Resources\BopDetailResource\Pages;

use App\Filament\Resources\BopDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBopDetails extends ListRecords
{
    protected static string $resource = BopDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
