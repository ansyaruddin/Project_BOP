<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextEntry;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\TicketResource\Pages\ViewTicket;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->visibleToUser(Auth::user());
    }
    
    protected static function budgetSection(string $prefix, string $label): Forms\Components\Card
    {
        return Forms\Components\Card::make([
            Forms\Components\TextInput::make("{$prefix}_budget")
                ->label("{$label} Budget")
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->live()
                ->rules([
                    fn (Forms\Get $get) => function (string $attribute, $value, \Closure $fail) use ($prefix, $get) {
                        $details = $get("{$prefix}Details") ?? [];
                        $total = collect($details)->sum(fn ($item) => (float) ($item['amount'] ?? 0));
                        if ($total > (float) $value) {
                            $fail(strtoupper($prefix) . " details total (Rp" . number_format($total) . ") exceeds budget (Rp" . number_format((float) $value) . ")");
                        }
                    },
                ]),

            Forms\Components\Placeholder::make("{$prefix}_total_placeholder")
                ->label("{$label} Details Total")
                ->content(function ($get) use ($prefix) {
                    $details = $get("{$prefix}Details") ?? [];
                    $budget = (float) $get("{$prefix}_budget") ?? 0;
                    $total = collect($details)->sum(fn ($d) => is_numeric($d['amount']) ? (float) $d['amount'] : 0);
                    $formattedTotal = 'Rp' . number_format($total, 2, ',', '.');
                    return $total > $budget ? "Over Budget! \nTotal: {$formattedTotal}" : "Current Total: {$formattedTotal}";
                })
                ->extraAttributes(['class' => 'text-sm'])
                ->reactive(),

            Forms\Components\Repeater::make("{$prefix}Details")
                ->label("{$label} Details")
                ->relationship("{$prefix}Details")
                ->schema([
                    Forms\Components\Select::make('coa_tag_id')
                        ->label('COA Tag')
                        ->relationship(
                            name: 'coaTag',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->where('budget_type', $prefix),
                        )
                        ->required(),

                    Forms\Components\TextInput::make('amount')
                        ->label('Amount')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                ])
                ->columns(2)
                ->defaultItems(1)
                ->addActionLabel('Add Detail')
                ->live()
                ->dehydrated(true)
                ->required(),
        ]);
    }



    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()
                ->columns(12)
                ->schema([
                    Forms\Components\Select::make('project_id')
                        ->label('Project')
                        ->relationship('project', 'name')
                        ->required()
                        ->columnSpan(7),

                    Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->columnSpan(7),

                    Forms\Components\TextInput::make('phone')
                        ->label('Phone')
                        ->required()
                        ->columnSpan(5),

                    Forms\Components\Select::make('owner_id')
                        ->label('Owner')
                        ->relationship('owner', 'name')
                        ->default(fn () => auth()->id())
                        ->disabled(fn (string $context) => $context === 'create', 'edit')
                        ->dehydrated(fn (string $context) => $context === 'create') 
                        ->required()
                        ->columnSpan(6),
                    

                    Forms\Components\TextInput::make('status')
                        ->label('Status')
                        ->default('Waiting for approval : Branch Manager')
                        ->disabled()
                        ->columnSpan(5)
                        ->dehydrated(), 
                    

                    Forms\Components\Select::make('priority')
                        ->label('Priority')
                        ->options([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                        ])
                        ->required()
                        ->columnSpan(4),

                    Forms\Components\Textarea::make('content')
                        ->label('Content')
                        ->required()
                        ->columnSpan(12),

                    Forms\Components\DatePicker::make('expected_transfer_date')
                        ->label('Expected Transfer Date')
                        ->required()
                        ->columnSpan(6),
                ]),

            // Budget + Details Blocks
            self::budgetSection('bop', 'BOP'),
            self::budgetSection('thl', 'THL'),
            self::budgetSection('rujukan', 'Rujukan'),
            self::budgetSection('jasamedis', 'Jasa Medis'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('project.name')->label('Project')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('responsible_role')
                    ->label('Responsible')
                    ->formatStateUsing(function ($state, $record) {
                        $roleLabels = [
                            'requester' => 'Requester',
                            'bm' => 'Branch Manager',
                            'reviewer' => 'Reviewer',
                            'manager' => 'Manager',
                            'cashier' => 'Cashier',
                        ];

                        $role = $roleLabels[$record->responsible_role] ?? ucwords($record->responsible_role);
                        $unit = $record->responsible_unit;

                        if ($unit) {
                            return "{$role} (" . ucwords($unit) . ")";
                        }

                        return $role;
                    }),



                Tables\Columns\TextColumn::make('phone')->label('Phone')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'closed' => 'Closed',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'in_progress' => 'warning',
                        'closed' => 'gray',
                        default => 'secondary',
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('expected_transfer_date')
                    ->label('Expected Transfer Date')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                // // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make()
                //      ->visible(fn ($record): bool => auth()->user()->can('update', $record)),

                // Tables\Actions\ViewAction::make()
                    // ->visible(fn ($record): bool => auth()->user()->can('view', $record)),

                Tables\Actions\EditAction::make()
                    ->visible(fn ($record): bool => auth()->user()->can('update', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
{
    return [
        'index' => Pages\ListTickets::route('/'),
        'create' => Pages\CreateTicket::route('/create'),
        'edit' => Pages\EditTicket::route('/{record}/edit'),
        'view' => Pages\ViewTicket::route('/{record}'), 
    ];
}

}
