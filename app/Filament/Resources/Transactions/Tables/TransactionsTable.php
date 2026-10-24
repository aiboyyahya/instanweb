<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\SelectColumn;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable(),
                TextColumn::make('province')
                    ->label('Provinsi'),
                TextColumn::make('city')
                    ->label('Kota'),
                TextColumn::make('district')
                    ->label('Kecamatan'),
                TextColumn::make('postal_code')
                    ->label('Kode Pos'),
                TextColumn::make('order_code')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->badge(),
                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'midtrans' => 'primary',
                    }),
                TextColumn::make('payment_status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'settlement' => 'success',
                        'cancel' => 'danger',
                        'expire' => 'gray',
                        'deny' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('courier')
                    ->label('Kurir')
                    ->badge(),
                TextColumn::make('courier_service')
                    ->label('Layanan Kurir'),
                    TextColumn::make('shipping_cost')
                    ->label('Ongkir')
                    ->money('IDR'),
                TextColumn::make('total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('tracking_number')
                    ->label('Nomor Resi')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
