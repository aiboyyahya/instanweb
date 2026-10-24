<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')
                ->label('Nama Produk')
                    ->searchable(),
                TextColumn::make('category.category_name')
                    ->label('kategori')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('purchase_price')
                ->label('Harga Beli')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('selling_price')
                ->label('Harga Jual')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock')
                ->label('Stok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status'),
                ImageColumn::make('image')
                ->label('Gambar')
                    ->square()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
