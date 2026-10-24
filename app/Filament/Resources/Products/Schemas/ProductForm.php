<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product_name')
                ->label('Nama Produk')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Select::make('category_id')
                    ->label('kategori')
                    ->relationship('category', 'category_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('description')
                ->label('Deskripsi Produk')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('purchase_price')
                ->label('Harga Beli')
                    ->required()
                    ->numeric(),
                TextInput::make('selling_price')
                ->label('Harga Jual')
                    ->required()
                    ->numeric(),
                TextInput::make('stock')
                ->label('Stok')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
                FileUpload::make('image')
                ->label('Gambar Utama Produk')
                    ->image()
                    ->disk('public')
                    ->required(),
            ]);
    }
}
