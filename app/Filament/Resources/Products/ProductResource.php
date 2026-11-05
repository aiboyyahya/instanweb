<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Products\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Schemas\ProductInfolist;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon ='heroicon-s-cube';

    protected static string|UnitEnum|null $navigationGroup = 'Katalog';
    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';
    
    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    /**
     * Batasi daftar produk pada toko milik pemilik yang sedang masuk.
     * Admin lama tanpa relasi toko tetap melihat seluruh data.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $store = auth()->user()?->store;

        return $store
            ? $query->where('store_id', $store->id)
            : $query;
    }

    public static function getRelations(): array
    {
        return [
          ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
