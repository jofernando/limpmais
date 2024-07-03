<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Models\Duplicata;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class DuplicatasRelationManager extends RelationManager
{
    protected static string $relationship = 'duplicatas';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form->schema(Duplicata::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Código'),
                TextColumn::make('valor')->money('BRL'),
                TextColumn::make('pagamento_restante')->money('BRL'),
                TextColumn::make('pagamento_efetuado')->money('BRL'),
                TextColumn::make('vencimento')->date(),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => fn ($state): bool => $state === 'pago',
                        'danger' => fn ($state): bool => $state === 'vencido',
                        'warning' => fn ($state): bool => $state === 'pendente',
                    ]),
                Tables\Columns\TextColumn::make('motorista.nome')->sortable(),
                Tables\Columns\TextColumn::make('fornecedor.empresa')->sortable(),
            ])
            ->filters([
                Duplicata::statusFilter(),
                Duplicata::statusVencimento(),
            ])->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('Exportar')
                    ->extraViewData(fn ($livewire) => ['cliente' => $livewire->ownerRecord]),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
