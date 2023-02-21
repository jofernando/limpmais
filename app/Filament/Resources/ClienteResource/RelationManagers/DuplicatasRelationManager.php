<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\HasManyRelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class DuplicatasRelationManager extends HasManyRelationManager
{
    protected static string $relationship = 'duplicatas';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('valor')
                    ->numeric()
                    ->required(),
                DatePicker::make('vencimento')
                    ->required()
                    ->default(Carbon::now()->addDays(30)),
                Grid::make()
                    ->schema([
                        MarkdownEditor::make('observacao')
                            ->label('Observação'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Código'),
                TextColumn::make('valor'),
                TextColumn::make('vencimento')->date(),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => fn ($state): bool => $state === 'pago',
                        'danger' => fn ($state): bool => $state === 'vencido',
                        'warning' => fn ($state): bool => $state === 'pendente',
                    ]),
            ])
            ->filters([
                //
            ]);
    }
}
