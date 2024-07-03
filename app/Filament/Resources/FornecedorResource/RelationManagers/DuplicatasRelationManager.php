<?php

namespace App\Filament\Resources\FornecedorResource\RelationManagers;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DuplicatasRelationManager extends RelationManager
{
    protected static string $relationship = 'duplicatas';

    protected static ?string $modelLabel = 'produto';

    protected static ?string $pluralModelLabel = 'produtos';

    protected function getTableQuery(): Builder | Relation
    {
        return parent::getTableQuery()->distinct('produto_id');
    }

    protected static ?string $recordTitleAttribute = 'produto.nome';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('produto.nome'),
                Tables\Columns\TextColumn::make('valor_vendido')->money('BRL'),
                Tables\Columns\TextColumn::make('sacos_40'),
                Tables\Columns\TextColumn::make('sacos_50'),
                Tables\Columns\TextColumn::make('sacos_60'),
                Tables\Columns\TextColumn::make('toneladas'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('Exportar'),
            ]);
    }
}
