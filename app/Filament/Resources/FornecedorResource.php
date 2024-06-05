<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FornecedorResource\Pages;
use App\Filament\Resources\FornecedorResource\RelationManagers;
use App\Forms\Components\CpfCnpj;
use App\Models\Fornecedor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Leandrocfe\FilamentPtbrFormFields\PtbrCpfCnpj;
use Leandrocfe\FilamentPtbrFormFields\PtbrMoney;

class FornecedorResource extends Resource
{
    protected static ?string $model = Fornecedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'fornecedores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('empresa')
                    ->required()
                    ->maxLength(255),
                PtbrCpfCnpj::make('cpf_cnpj')
                    ->rule('cpf_ou_cnpj')
                    ->label('CPF/CNPJ'),
                PtbrMoney::make('saldo_receber')
                    ->label('Saldo a receber')
                    ->visibleOn('view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('empresa'),
                Tables\Columns\TextColumn::make('cpf_cnpj')
                    ->label('CPF/CNPJ'),
                Tables\Columns\TextColumn::make('saldo_receber')
                    ->label('Saldo a receber')
                    ->money('BRL'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ContratosRelationManager::class,
            RelationManagers\ResgatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFornecedors::route('/'),
            'create' => Pages\CreateFornecedor::route('/create'),
            'view' => Pages\ViewFornecedor::route('/{record}'),
            'edit' => Pages\EditFornecedor::route('/{record}/edit'),
        ];
    }
}
