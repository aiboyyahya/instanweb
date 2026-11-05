<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Store;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class CategoryForm
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
                ->required();

        return $schema
            ->components([
                Section::make('Detail Kategori')
                    ->description('Kategori membantu pelanggan menemukan produk dengan cepat.')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        $storeField,

                        TextInput::make('category_name')
                            ->label('Nama Kategori')
                            ->placeholder('Contoh: Makanan Ringan')
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('store_id', $get('store_id')),
                            ),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->disabled()
                            ->hint('Slug dibuat otomatis dari nama kategori'),

                        FileUpload::make('icon')
                            ->label('Ikon Kategori')
                            ->helperText('Unggah ikon persegi agar tampil rapi pada filter katalog.')
                            ->image()
                            ->directory('categories')
                            ->disk('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
