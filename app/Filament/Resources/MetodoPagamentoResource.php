<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MetodoPagamentoResource\Pages;
use App\Filament\Resources\MetodoPagamentoResource\RelationManagers;
use App\Models\MetodoPagamento;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MetodoPagamentoResource extends Resource
{
    protected static ?string $model = MetodoPagamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $modelLabel = 'método de pagamento';

    protected static ?string $pluralModelLabel = 'métodos de pagamento';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tipo')->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo'),
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
            'index' => Pages\ManageMetodoPagamentos::route('/'),
        ];
    }
}
