<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuplicataResource\Pages;
use App\Filament\Resources\DuplicataResource\RelationManagers;
use App\Models\Customer;
use App\Models\Duplicata;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DuplicataResource extends Resource
{
    protected static ?string $model = Duplicata::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Select::make('customer_id')
                            ->required()
                            ->label('Código do customer')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => Customer::where('nome', 'ilike', "%{$search}%")->orWhere('id', intval($search))->limit(50)->pluck('nome', 'id'))
                            ->getOptionLabelUsing(fn ($value): ?string => Customer::find($value)?->nome),
                    ])->columns(1),
                TextInput::make('valor')
                    ->required()
                    ->numeric(),
                DatePicker::make('vencimento')
                    ->required()
                    ->default(now()->addDays(30)),
                Grid::make()
                    ->schema([
                        MarkdownEditor::make('observacao')
                            ->label('Observação'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Código'),
                Tables\Columns\TextColumn::make('customer.identificacao'),
                Tables\Columns\TextColumn::make('valor'),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => fn ($state): bool => $state === 'pago',
                        'danger' => fn ($state): bool => $state === 'vencido',
                        'warning' => fn ($state): bool => $state === 'pendente',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDuplicatas::route('/'),
            'create' => Pages\CreateDuplicata::route('/create'),
            'edit' => Pages\EditDuplicata::route('/{record}/edit'),
        ];
    }
}
