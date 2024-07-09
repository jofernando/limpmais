<?php

namespace App\Filament\Resources\FornecedorResource\RelationManagers;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItensRelationManager extends RelationManager
{
    protected static string $relationship = 'itens';

    protected static ?string $modelLabel = 'produto';

    protected static ?string $pluralModelLabel = 'produtos';

    protected function getTableQuery(): Builder | Relation
    {
        return parent::getTableQuery()
            ->join('duplicatas', 'items.duplicata_id', 'duplicatas.id')
            ->orderBy('items.produto_id')
            ->orderBy('items.tipo_quantidade')
            ->groupBy('items.produto_id', 'items.tipo_quantidade')
            ->select('items.produto_id', 'items.tipo_quantidade', DB::raw('SUM(items.quantidade) as total_quantidade'), DB::raw('SUM(duplicatas.valor) as total_valor'), DB::raw('SUM(duplicatas.gastos) as total_gastos'), DB::raw('SUM(duplicatas.compra) as total_compra'));
    }

    public function getTableRecordKey(Model $record): string
    {
        return uniqid();
    }

    protected static ?string $recordTitleAttribute = 'produto.nome';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('produto.nome'),
                Tables\Columns\TextColumn::make('tipo_quantidade')->label('Tipo'),
                Tables\Columns\TextColumn::make('total_quantidade')->label('Quantidade'),
                Tables\Columns\TextColumn::make('total_valor')->money('BRL')->label('Valor de venda das duplicatas'),
                Tables\Columns\TextColumn::make('total_compra')->money('BRL')->label('Valor de compra das duplicatas'),
                Tables\Columns\TextColumn::make('total_gastos')->money('BRL')->label('Valor de gastos das duplicatas'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Inicio')->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('created_until')->label('Fim')->default(now()->endOfMonth()),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['created_from'] and ! $data['created_until']) {
                            return null;
                        }
                        $msg = 'Duplicatas ';
                        if ($data['created_from']) {
                            $msg = $msg.' a partir de '.Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until']) {
                            $msg = $msg.' até '.Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $msg;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('duplicatas.venda', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('duplicatas.venda', '<=', $date),
                            );
                    })
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
