<?php

namespace App\Filament\Resources\ThlDetailResource\Pages;

use App\Filament\Resources\ThlDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThlDetails extends ListRecords
{
    protected static string $resource = ThlDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
