<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\DuplicatasRelationManager;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $recordTitleAttribute = 'nome';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('nome')
                            ->required()
                            ->maxLength(255),
                    ])->columns(1),
                Forms\Components\TextInput::make('rua')
                    ->maxLength(255),
                Forms\Components\TextInput::make('numero')
                    ->label('Número')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cidade')
                    ->maxLength(255),
                Forms\Components\TextInput::make('estado')
                    ->maxLength(255),
                Forms\Components\TextInput::make('divida')
                    ->label('Dívida')
                    ->visibleOn(Pages\ViewCustomer::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Código'),
                Tables\Columns\TextColumn::make('nome'),
                Tables\Columns\TextColumn::make('endereco')->label('Endereço'),
                Tables\Columns\TextColumn::make('divida')
                    ->label('Dívida'),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->dateTime(),
//                Tables\Columns\TextColumn::make('updated_at')
//                    ->dateTime(),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DuplicatasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
