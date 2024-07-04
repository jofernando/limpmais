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

class DuplicatasRelationManager extends RelationManager
{
    protected static string $relationship = 'duplicatas';

    protected static ?string $modelLabel = 'produto';

    protected static ?string $pluralModelLabel = 'produtos';

    protected function getTableQuery(): Builder | Relation
    {
        return parent::getTableQuery()
            ->orderBy('produto_id')
            ->orderBy('tipo_quantidade')
            ->groupBy('produto_id', 'tipo_quantidade')
            ->select('produto_id', 'tipo_quantidade', DB::raw('SUM(quantidade) as total_quantidade'), DB::raw('SUM(valor) as total_valor'));
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
                Tables\Columns\TextColumn::make('total_valor')->money('BRL')->label('Valor das duplicatas'),
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
                                fn (Builder $query, $date): Builder => $query->whereDate('venda', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('venda', '<=', $date),
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
