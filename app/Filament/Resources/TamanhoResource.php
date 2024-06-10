<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TamanhoResource\Pages;
use App\Filament\Resources\TamanhoResource\RelationManagers;
use App\Models\Tamanho;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TamanhoResource extends Resource
{
    protected static ?string $model = Tamanho::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tamanho')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('observacao'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tamanho'),
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
            'index' => Pages\ManageTamanhos::route('/'),
        ];
    }    
}
