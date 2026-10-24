<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               TextInput::make('category_name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->disabled()
                    ->hint('Slug dibuat otomatis dari nama kategori'),

                FileUpload::make('icon')
                    ->label('Ikon')
                    
            ]);
    }
}
