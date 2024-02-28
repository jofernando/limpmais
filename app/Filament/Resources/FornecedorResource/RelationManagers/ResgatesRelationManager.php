<?php

namespace App\Filament\Resources\FornecedorResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Leandrocfe\FilamentPtbrFormFields\PtbrMoney;

class ResgatesRelationManager extends RelationManager
{
    protected static string $relationship = 'resgates';

    protected static ?string $recordTitleAttribute = 'valor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                PtbrMoney::make('valor')
                    ->required(),
                Forms\Components\DatePicker::make('data'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('valor')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('data')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
            ]);
    }
}
