<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Store;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        $store = auth()->user()?->store;

        $storeField = $store
            ? Hidden::make('store_id')->default($store->id)
            : Select::make('store_id')
                ->label('Toko')
                ->options(Store::query()->orderBy('store_name')->pluck('store_name', 'id'))
                ->searchable()
                ->preload()
                ->live()
                ->required();

        return $schema
            ->components([
                $storeField,

                Section::make('Informasi Produk')
                    ->description('Data utama yang tampil pada katalog dan halaman produk.')
                    ->icon('heroicon-o-cube')
                    ->columns(2)
                    ->schema([
                        TextInput::make('product_name')
                            ->label('Nama Produk')
                            ->placeholder('Contoh: Kopi Arabika Gayo 250 gram')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->placeholder('kopi-arabika-gayo-250-gram')
                            ->helperText('Alamat unik produk pada tautan storefront.')
                            ->required()
                            ->maxLength(255),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship(
                                'category',
                                'category_name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->when(
                                    auth()->user()?->store?->id ?? $get('store_id'),
                                    fn (Builder $query, int $storeId) => $query->where('store_id', $storeId),
                                ),
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label('Status Tayang')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('active')
                            ->required(),

                        Textarea::make('description')
                            ->label('Deskripsi Produk')
                            ->placeholder('Jelaskan bahan, ukuran, dan keunggulan produk.')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Harga & Stok')
                    ->description('Atur harga jual, harga beli, dan ketersediaan barang.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextInput::make('purchase_price')
                            ->label('Harga Beli')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('selling_price')
                            ->label('Harga Jual')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ]),

                Section::make('Media Produk')
                    ->description('Gunakan foto persegi dengan pencahayaan terang agar produk terlihat jelas.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('image')
                            // ->label('Gambar Utama Produk')
                            // ->image()
                            // ->imageEditor()
                            // ->disk('public')
                            // ->required()
                            // ->columnSpanFull(),
                            ->label('Gambar Utama Produk')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
