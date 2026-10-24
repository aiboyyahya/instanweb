<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required(),

                TextInput::make('slug')
                    ->unique(ignoreRecord: true)
                    ->label('Slug'),

                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('Gambar Utama')
                    ->disk('public')
                    ->columnSpanFull()
                    ->required(),

                FileUpload::make('thumbnail')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->columnSpanFull()
                    ->required(),
                    
                Hidden::make('user_id')
                    ->label('Penulis')
                    ->default(fn() => auth()->id()),
            ]);
    }
}
