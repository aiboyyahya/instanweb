<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Store;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleForm
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
                Section::make('Konten Artikel')
                    ->description('Tulis informasi atau cerita untuk pelanggan.')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        $storeField,

                        TextInput::make('title')
                            ->label('Judul')
                            ->placeholder('Contoh: Tips Merawat Produk Kulit')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->placeholder('tips-merawat-produk-kulit')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Dibuat otomatis dari judul jika dikosongkan.'),

                        RichEditor::make('description')
                            ->label('Isi Artikel')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Media')
                    ->description('Gambar landscape berkualitas baik.')
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Gambar Utama')
                            ->image()
                            ->imageEditor()
                            ->imageResizeTargetWidth(1600)
                            ->imageResizeMode('contain')
                            ->maxSize(10240)
                            ->disk('public')
                            ->required(),

                        FileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->image()
                            ->imageEditor()
                            ->imageResizeTargetWidth(600)
                            ->imageResizeMode('contain')
                            ->maxSize(10240)
                            ->disk('public')
                            ->required(),
                    ]),

                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
            ]);
    }
}
