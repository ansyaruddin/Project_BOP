<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;



use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Grid::make(2)->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),
                Select::make('role')
                    ->label('Role')
                    ->required()
                    ->options([
                        'requester' => 'Requester',
                        'bm' => 'Branch Manager',
                        'reviewer' => 'Reviewer',
                        'manager' => 'Manager',
                        'cashier' => 'Cashier',
                    ]),
                Select::make('area')
                    ->label('Area')
                    ->required()
                    ->options([
                        'branch' => 'Branch',
                        'main' => 'Main',
                    ])
                    ->reactive(), // important for showing unit
                Select::make('unit')
                    ->label('Unit')
                    ->options([
                        'finance' => 'Finance',
                        'operational' => 'Operational',
                        'sales' => 'Sales',
                    ])
                    ->visible(fn ($get) => $get('area') === 'main'),

                Select::make('region')
                    ->label('Region')
                    ->required()
                    ->options([
                        'jakarta' => 'Jakarta',
                        'jawa-barat' => 'Jawa Barat',
                        'jawa-tengah' => 'Jawa Tengah',
                        'jawa-timur' => 'Jawa Timur',
                        'bali' => 'Bali',
                    ])
                    ->visible(fn ($get) => $get('area') === 'branch'),
                
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => \Hash::make($state))
                    ->required(fn ($context) => $context === 'create')
                    ->label('Password'),
            ]),
        ]);
    
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email'),
                TextColumn::make('role'),
                TextColumn::make('area'),
                TextColumn::make('unit'),
                TextColumn::make('region'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
