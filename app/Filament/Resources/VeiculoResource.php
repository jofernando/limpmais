<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VeiculoResource\Pages;
use App\Filament\Resources\VeiculoResource\RelationManagers;
use App\Models\Veiculo;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VeiculoResource extends Resource
{
    protected static ?string $model = Veiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('placa')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('observacao'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('placa'),
                Tables\Columns\TextColumn::make('observacao')->html(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVeiculos::route('/'),
        ];
    }    
}
