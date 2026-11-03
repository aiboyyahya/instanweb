<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Kategori')
                    ->description('Kategori membantu pelanggan menemukan produk dengan cepat.')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('category_name')
                            ->label('Nama Kategori')
                            ->placeholder('Contoh: Makanan Ringan')
                            ->required()
                            ->maxLength(255),

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
