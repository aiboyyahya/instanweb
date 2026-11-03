<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
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
                ImageColumn::make('image')
                    // ->label('Gambar')
                    // ->disk('public')
                    // ->visibility('public')
                    // ->square()
                    // ->imageSize(55),
                    ->label('Gambar')
                    ->disk('public')
                    ->visibility('public')
                    ->square()
                    ->imageSize(55),

                TextColumn::make('product_name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->placeholder('Belum diberi nama'),

                TextColumn::make('category.category_name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Tanpa kategori'),

                TextColumn::make('purchase_price')
                    ->label('Harga Beli')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        (int) $state <= 0 => 'danger',
                        (int) $state <= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'active' ? 'Aktif' : 'Nonaktif')
                    ->color(fn (?string $state): string => $state === 'active' ? 'success' : 'gray'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y • H:i')
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
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateHeading('Belum ada produk')
            ->emptyStateDescription('Tambahkan produk pertama agar tampil di storefront toko Anda.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Tambah Produk'),
            ]);
    }
}
