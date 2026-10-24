<?php

namespace App\Filament\Resources\Articles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Judul'),
                TextColumn::make('user.name')
                    ->searchable()
                    ->label('Penulis'),
                ImageColumn::make('image')
                    ->label('Gambar Utama')
                    ->disk('public')
                    ->circular(),
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('category.category_name')
                    ->label('Kategori')
                    ->placeholder('Tidak ada kategori'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Ditulis pada'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
