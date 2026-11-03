<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Produk')
                    ->icon('heroicon-o-cube')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('product_name')
                            ->label('Nama Produk')
                            ->weight('semibold'),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->copyable()
                            ->color('gray'),

                        TextEntry::make('category.category_name')
                            ->label('Kategori')
                            ->badge()
                            ->color('gray')
                            ->placeholder('Tanpa kategori'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => $state === 'active' ? 'Aktif' : 'Nonaktif')
                            ->color(fn (?string $state): string => $state === 'active' ? 'success' : 'gray'),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),

                Section::make('Harga & Stok')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('purchase_price')
                            ->label('Harga Beli')
                            ->money('IDR'),

                        TextEntry::make('selling_price')
                            ->label('Harga Jual')
                            ->money('IDR')
                            ->weight('semibold'),

                        TextEntry::make('stock')
                            ->label('Stok')
                            ->numeric(),
                    ]),

                Section::make('Media & Riwayat')
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('image')
                            ->label('Gambar Utama')
                            ->disk('public')
                            ->imageSize(180),

                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y • H:i'),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime('d M Y • H:i'),
                    ]),
            ]);
    }
}
