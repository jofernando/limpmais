<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\PagamentoResource\Pages;
use App\Filament\Resources\PagamentoResource\RelationManagers;
use App\Models\Pagamento;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagamentoResource extends Resource
{
    protected static ?string $model = Pagamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('duplicata.cliente.nome')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('clientes.nome', $direction);
                    }),
                TextColumn::make('valor')->money('BRL')->sortable(),
                TextColumn::make('metodoPagamento.tipo')->sortable(),
                TextColumn::make('data')->date()->sortable()->label('Data do pagamento'),
                TextColumn::make('duplicata.valor')->money('BRL')->sortable()->label('Valor da duplicata'),
                TextColumn::make('duplicata.compra')->money('BRL')->sortable()->label('Valor de compra'),
                TextColumn::make('duplicata.gastos')->money('BRL')->sortable()->label('Valor dos gastos'),
                TextColumn::make('duplicata.pagamento_restante')->money('BRL')->label('Pagamento restante'),
                TextColumn::make('duplicata.pagamento_efetuado')->money('BRL')->label('Pagamento efetuado'),
                TextColumn::make('duplicata.venda')->date()->sortable()->label('Data da venda'),
                TextColumn::make('duplicata.vencimento')->date()->sortable()->label('Data do vencimento'),
                BadgeColumn::make('duplicata.status')
                    ->colors([
                        'success' => fn ($state): bool => $state === 'pago',
                        'danger' => fn ($state): bool => $state === 'vencido',
                        'warning' => fn ($state): bool => $state === 'pendente',
                    ])->label('Status'),
                TextColumn::make('duplicata.motorista.nome'),
                TextColumn::make('duplicata.fornecedores_nomes')->label('Fornecedores'),
            ])
            ->filters([
                Filter::make('data')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Inicio')->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('created_until')->label('Fim')->default(now()->endOfMonth()),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['created_from'] and ! $data['created_until']) {
                            return null;
                        }
                        $msg = 'Pagamentos ';
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
                                fn (Builder $query, $date): Builder => $query->whereDate('data', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data', '<=', $date),
                            );
                    })
            ])
            ->actions([
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('Exportar'),
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
            'index' => Pages\ListPagamentos::route('/'),
        ];
    }
}
