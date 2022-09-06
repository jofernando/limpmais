<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuplicataResource\Pages;
use App\Models\Customer;
use App\Models\Duplicata;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Str;

class DuplicataResource extends Resource
{
    protected static ?string $model = Duplicata::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('customer_id')
                    ->required()
                    ->label('Código do customer')
                    ->options(Customer::all()->pluck('id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function (\Closure $set, $state) {
                        $set('customer_name', $state);
                    }),
                Select::make('customer_name')
                    ->required()
                    ->label('Nome do customer')
                    ->options(Customer::all()->pluck('nome', 'id'))
                    ->searchable()
                    ->afterStateUpdated(function (\Closure $set, $state) {
                        $set('customer_id', $state);
                    }),
                TextInput::make('valor')
                    ->required()
                    ->numeric(),
                DatePicker::make('vencimento')
                    ->required(),
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
                Tables\Columns\TextColumn::make('id')->label('Código'),
                Tables\Columns\TextColumn::make('customer.identificacao'),
                Tables\Columns\TextColumn::make('valor'),
                // Tables\Columns\TextColumn::make('observacao'),
                // Tables\Columns\TextColumn::make('vencimento')
                //     ->date(),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => fn ($state): bool => $state === 'pago',
                        'danger' => fn ($state): bool => $state === 'vencido',
                        'warning' => fn ($state): bool => $state === 'pendente',
                    ]),
                // Tables\Columns\BooleanColumn::make('quitada'),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime(),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime(),
            ])
            ->filters([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDuplicatas::route('/'),
        ];
    }
}
