<?php

namespace App\Filament\Resources\FornecedorResource\RelationManagers;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Models\Motorista;
use App\Models\Produto;
use App\Models\Veiculo;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Leandrocfe\FilamentPtbrFormFields\PtbrMoney;

class ContratosRelationManager extends RelationManager
{
    protected static string $relationship = 'contratos';

    protected static ?string $recordTitleAttribute = 'valor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('produto_id')
                    ->label('Produto')
                    ->options(Produto::all()->pluck('nome', 'id')),
                TextInput::make('n_contrato')
                    ->label('Nº do contrato'),
                Grid::make()
                    ->schema([
                        PtbrMoney::make('valor')
                            ->required(),
                        PtbrMoney::make('pago'),
                    ])
                    ->columns(2),
                Grid::make()
                    ->schema([
                        Forms\Components\DatePicker::make('data'),
                        Forms\Components\DatePicker::make('vigencia')->label('Vigência'),
                    ])
                    ->columns(2),
                MarkdownEditor::make('observacao')
                    ->label('Observação'),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'sacas' => 'Sacas',
                        'toneladas' => 'Toneladas/kg',
                    ])->required()
                    ->reactive(),
                Forms\Components\TextInput::make('toneladas')
                    ->numeric()
                    ->reactive()
                    ->requiredIf('tipo', 'toneladas')
                    ->hidden(fn (\Closure $get) => $get('tipo') != 'toneladas'),
                PtbrMoney::make('valor_kg')
                    ->hidden(fn (\Closure $get) => $get('tipo') != 'toneladas')
                    ->reactive(),
                Forms\Components\TextInput::make('sacas')
                    ->numeric()
                    ->requiredIf('tipo', 'sacas')
                    ->hidden(fn (\Closure $get) => $get('tipo') != 'sacas'),
                Grid::make()
                    ->schema([
                        Forms\Components\Repeater::make('entregas')
                            ->schema([
                                Forms\Components\Select::make('motorista_id')
                                    ->label('Motorista')
                                    ->options(Motorista::all()->pluck('nome', 'id'))
                                    ->required(),
                                Forms\Components\Select::make('veiculo_id')
                                    ->label('Veículo')
                                    ->options(Veiculo::all()->pluck('placa', 'id')),
                                Forms\Components\DatePicker::make('data'),
                                Forms\Components\TextInput::make('toneladas')
                                    ->numeric()
                                    ->requiredIf('tipo', 'toneladas')
                                    ->hidden(fn (\Closure $get) => $get('../../tipo') != 'toneladas'),
                                Forms\Components\TextInput::make('sacas')
                                    ->numeric()
                                    ->requiredIf('tipo', 'sacas')
                                    ->hidden(fn (\Closure $get) => $get('../../tipo') != 'sacas'),

                            ])
                            ->relationship()
                            ->reactive()
                            ->columns(2),
                    ])->columns(1),
                Grid::make()
                    ->schema([
                        Placeholder::make('sacas_resgatadas')
                            ->label('Quantidade retirada')
                            ->content(function ($get) {
                                return collect($get('entregas'))->pluck('sacas')->sum();
                            })
                            ->hidden(fn ($get) => $get('tipo') != 'sacas'),
                        Placeholder::make('sacas_restante')
                            ->label('Quantidade restante')
                            ->content(function ($get) {
                                $sacas = $get('sacas');
                                $result = collect($get('entregas'))->pluck('sacas')->sum();

                                return intval($sacas) - intval($result);
                            })
                            ->hidden(fn ($get) => $get('tipo') != 'sacas'),
                    ])
                    ->columns(2),
                Grid::make()
                    ->schema([
                        Placeholder::make('toneladas_resgatadas')
                            ->label('Quantidade retirada')
                            ->content(function ($get) {
                                return number_format(collect($get('entregas'))->pluck('toneladas')->sum(), 2, ',', '.');
                            })
                            ->hidden(fn ($get) => $get('tipo') != 'toneladas'),
                        Placeholder::make('toneladas_restante')
                            ->label('Quantidade restante')
                            ->content(function ($get) {
                                $toneladas = $get('toneladas');
                                $result = collect($get('entregas'))->pluck('toneladas')->sum();

                                return number_format(floatval($toneladas) - floatval($result), 2, ',', '.');
                            })
                            ->hidden(fn ($get) => $get('tipo') != 'toneladas'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('produto.nome'),
                Tables\Columns\TextColumn::make('valor')->money('BRL'),
                Tables\Columns\TextColumn::make('data')->date(),
                Tables\Columns\TextColumn::make('vigencia')->date()->label('Vigência'),
                Tables\Columns\TextColumn::make('resgatada')->label('Qtd retirada'),
                Tables\Columns\TextColumn::make('restante')->label('Qtd restante'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('Exportar'),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
