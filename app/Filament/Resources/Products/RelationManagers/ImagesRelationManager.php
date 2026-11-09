<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload as ComponentsFileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $recordTitleAttribute = 'image_file';


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsFileUpload::make('image_file')
                    ->label('Gambar Produk')
                    ->directory('products')
                    ->image()
                    ->imageResizeTargetWidth(1200)
                    ->imageResizeMode('contain')
                    ->disk('public')
                    ->maxSize(10240)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_file')
                    ->label('Gambar')
                    ->disk('public')
                    ->square()
                    ->imageSize(52),

                TextColumn::make('created_at')
                    ->label('Diupload Pada')
                    ->dateTime('d M Y • H:i'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateHeading('Belum ada gambar tambahan')
            ->emptyStateDescription('Tambahkan foto lain agar pelanggan dapat melihat produk lebih detail.')
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Gambar'),
            ])
            ->actions([
                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
