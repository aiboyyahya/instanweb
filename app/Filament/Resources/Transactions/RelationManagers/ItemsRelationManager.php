<?php

namespace App\Filament\Resources\Transactions\RelationManagers;

use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\CreateAction as ActionsCreateAction;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('product_name')
                    ->label('Nama Produk')
                    ->required(),
                TextInput::make('price')
                    ->label('Harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                TextInput::make('quantity')
                    ->label('Kuantitas')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.product_name')
                    ->label('Nama Produk')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR'),
                TextColumn::make('quantity')
                    ->label('Kuantitas'),
                TextColumn::make('subtotal')
                    ->label('Total')
                    ->state(fn($record) => $record->price * $record->quantity)
                    ->money('IDR'),
            ])
            ->filters([
                //
            ])

            ->actions([
                // Removed edit and delete actions to prevent modification
            ])
            ->bulkActions([
                // Removed bulk actions to prevent deletion
            ]);
    }
}
