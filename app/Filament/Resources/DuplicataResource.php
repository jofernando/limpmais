<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\DuplicataResource\Pages;
use App\Models\Cliente;
use App\Models\Duplicata;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;

class DuplicataResource extends Resource
{
    protected static ?string $model = Duplicata::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        $formulario = Duplicata::getForm();
        $grid = Grid::make()
            ->schema([
                Select::make('cliente_id')
                    ->required()
                    ->label('Código do cliente')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => Cliente::where('nome', 'ilike', "%{$search}%")->orWhere('id', intval($search))->limit(50)->pluck('nome', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => Cliente::find($value)?->nome),
            ])->columns(1);
        array_unshift($formulario, $grid);

        return $form->schema($formulario);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Código'),
                Tables\Columns\TextColumn::make('cliente.nome')->sortable(),
                Tables\Columns\TextColumn::make('valor')->money('BRL')->sortable(),
                Tables\Columns\TextColumn::make('pagamento_restante')->money('BRL'),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('Exportar'),
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDuplicatas::route('/'),
            'create' => Pages\CreateDuplicata::route('/create'),
            'edit' => Pages\EditDuplicata::route('/{record}/edit'),
        ];
    }
}
