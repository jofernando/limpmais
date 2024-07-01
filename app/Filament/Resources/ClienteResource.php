<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers\DuplicatasRelationManager;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Leandrocfe\FilamentPtbrFormFields\PtbrCpfCnpj;
use Leandrocfe\FilamentPtbrFormFields\PtbrMoney;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function getGloballySearchableAttributes(): array
    {
        return ['nome', 'id'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->nome;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Código')
                            ->required()
                            ->maxLength(255)
                            ->visibleOn(Pages\ViewCliente::class),
                        Forms\Components\TextInput::make('nome')
                            ->required()
                            ->maxLength(255),
                    ])->columns(1),
                PtbrCpfCnpj::make('cpf_cnpj')
                    ->label('CPF/CNPJ')
                    ->rule('cpf_ou_cnpj'),
                Forms\Components\TextInput::make('rua')
                    ->maxLength(255),
                Forms\Components\TextInput::make('celular')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cidade')
                    ->label('Cidade/Sítio')
                    ->maxLength(255),
                Forms\Components\TextInput::make('estado')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ponto_referencia')
                    ->label('Ponto de referência')
                    ->maxLength(255),
                Forms\Components\TextInput::make('setor')
                    ->maxLength(255),
                PtbrMoney::make('divida')
                    ->label('Dívida')
                    ->visibleOn(Pages\ViewCliente::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Código'),
                Tables\Columns\TextColumn::make('nome')->sortable(),
                Tables\Columns\TextColumn::make('endereco')->label('Endereço'),
                Tables\Columns\TextColumn::make('celular'),
                Tables\Columns\TextColumn::make('divida')->money('BRL')
                    ->label('Dívida'),
            ])
            ->defaultSort('nome')
            ->bulkActions([
                FilamentExportBulkAction::make('Exportar'),
            ])
            ->filters([
                Cliente::statusFilter(),
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'view' => Pages\ViewCliente::route('/{record}'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
