<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use App\Models\Motorista;
use App\Models\Produto;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendasRelationManager extends RelationManager
{
    protected static string $relationship = 'vendas';

    protected static ?string $modelLabel = 'teste';

    protected static ?string $pluralModelLabel = 'teste';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('produto_id')
                    ->label('Produto')
                    ->options(Produto::all()->pluck('nome', 'id')),
                Select::make('tipo_quantidade')
                    ->label('Tipo da quantidade')
                    ->options([
                        'toneladas' => 'Tonelandas',
                        'sacos40' => 'Sacos 40kg',
                        'sacos50' => 'Sacos 50kg',
                        'sacos60' => 'Sacos 60kg',
                    ]),
                TextInput::make('quantidade')
                    ->numeric(),
                TextInput::make('prazo')
                    ->numeric()
                    ->suffix('dias'),
                Repeater::make('motoristaVenda')
                    ->schema([
                        Select::make('motorista_id')
                            ->label('Motorista')
                            ->options(Motorista::all()->pluck('nome', 'id')),
                        TextInput::make('placa'),
                    ])
                    ->relationship()
                    ->columns(2),
                TextInput::make('folguista'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Código'),
                TextColumn::make('produto.nome'),
                TextColumn::make('created_at')->dateTime()->label('Criado em')
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
