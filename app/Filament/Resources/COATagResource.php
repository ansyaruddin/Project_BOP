<?php

namespace App\Filament\Resources;

use App\Filament\Resources\COATagResource\Pages;
use App\Filament\Resources\COATagResource\RelationManagers;
use App\Models\COATag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class COATagResource extends Resource
{
    protected static ?string $model = COATag::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'COA Tags';
    protected static ?string $navigationGroup = 'Budget Setup';
    protected static ?int $navigationSort = 1;
    
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('budget_type')
                ->label('Budget Type')
                ->options([
                    'bop' => 'BOP',
                    'thl' => 'THL',
                    'jasamedis' => 'Jasa Medis',
                    'rujukan' => 'Rujukan',
                ])
                ->required() 
                ->placeholder('Select a budget type'),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('budget_type')
                ->label('Budget Type')
                ->sortable()
                ->searchable(),
        ])
        ->filters([])
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
            'index' => Pages\ListCOATags::route('/'),
            'create' => Pages\CreateCOATag::route('/create'),
            'edit' => Pages\EditCOATag::route('/{record}/edit'),
        ];
    }
}
