<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Artikel')
                    ->description('Tulis informasi, tips, atau cerita yang relevan untuk pelanggan.')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
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

                Section::make('Media Artikel')
                    ->description('Gunakan gambar landscape berkualitas baik untuk tampilan artikel dan daftar artikel.')
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Gambar Utama')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->required(),

                        FileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->required(),
                    ]),

                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
            ]);
    }
}
