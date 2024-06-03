<?php

namespace App\Filament\Resources\FornecedorResource\RelationManagers;

use App\Forms\Components\Dinheiro;
use App\Models\Motorista;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Leandrocfe\FilamentPtbrFormFields\PtbrMoney;

class ContratosRelationManager extends RelationManager
{
    protected static string $relationship = 'contratos';

    protected static ?string $recordTitleAttribute = 'valor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                PtbrMoney::make('valor')
                    ->required(),
                PtbrMoney::make('pago'),
                Forms\Components\DatePicker::make('data'),
                Grid::make()
                    ->schema([
                        MarkdownEditor::make('observacao')
                            ->label('Observação'),
                    ])->columns(1),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'sacas' => 'Sacas',
                        'toneladas' => 'Toneladas',
                    ])->required()
                    ->reactive(),
                Forms\Components\TextInput::make('toneladas')
                    ->numeric()
                    ->requiredIf('tipo', 'toneladas')
                    ->hidden(fn (\Closure $get) => $get('tipo') != 'toneladas'),
                Forms\Components\TextInput::make('sacas')
                    ->numeric()
                    ->requiredIf('tipo', 'sacas')
                    ->hidden(fn (\Closure $get) => $get('tipo') != 'sacas'),
                Grid::make()
                    ->schema([
                        Forms\Components\Repeater::make('entregas')
                            ->schema([
                                Forms\Components\DatePicker::make('data'),
                                Forms\Components\Select::make('motorista_id')
                                    ->label('Motorista')
                                    ->options(Motorista::all()->pluck('nome', 'id'))
                                    ->required(),
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
                            ->columns(3)
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('valor')->money('BRL'),
                Tables\Columns\TextColumn::make('data')->date(),
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
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
