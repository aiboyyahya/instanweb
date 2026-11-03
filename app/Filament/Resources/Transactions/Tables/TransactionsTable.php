<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode Pesanan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('semibold'),

                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tanpa nama'),

                TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'packing' => 'Dikemas',
                        'sent' => 'Dikirim',
                        'done', 'success' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst((string) $state),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'packing' => 'info',
                        'sent' => 'primary',
                        'done', 'success' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Belum dibayar',
                        'settlement', 'paid', 'capture' => 'Lunas',
                        'cancel' => 'Dibatalkan',
                        'expire', 'expired' => 'Kedaluwarsa',
                        'deny' => 'Ditolak',
                        default => ucfirst((string) $state),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'settlement', 'paid', 'capture' => 'success',
                        'pending' => 'warning',
                        'cancel', 'deny', 'expire', 'expired' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y • H:i')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'midtrans' => 'Midtrans',
                        default => ucfirst((string) $state),
                    })
                    ->color(fn (?string $state): string => $state === 'midtrans' ? 'primary' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('courier')
                    ->label('Kurir')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => strtoupper((string) $state))
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('courier_service')
                    ->label('Layanan Kurir')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tracking_number')
                    ->label('Nomor Resi')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Belum tersedia')
                    ->toggleable(),

                TextColumn::make('shipping_cost')
                    ->label('Ongkir')
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('province')
                    ->label('Provinsi')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('city')
                    ->label('Kota')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('district')
                    ->label('Kecamatan')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('postal_code')
                    ->label('Kode Pos')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y • H:i')
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
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->emptyStateHeading('Belum ada pesanan')
            ->emptyStateDescription('Pesanan pelanggan akan muncul di sini untuk diproses.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Buat Pesanan'),
            ]);
    }
}
