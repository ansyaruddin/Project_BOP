<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RujukanDetailResource\Pages;
use App\Filament\Resources\RujukanDetailResource\RelationManagers;
use App\Models\RujukanDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RujukanDetailResource extends Resource
{
    protected static ?string $model = RujukanDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket.title')
                    ->label('Ticket')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('coaTag.name')
                    ->label('COA Tag')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRujukanDetails::route('/'),
            'create' => Pages\CreateRujukanDetail::route('/create'),
            'edit' => Pages\EditRujukanDetail::route('/{record}/edit'),
        ];
    }
}
