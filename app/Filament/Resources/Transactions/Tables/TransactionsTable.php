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
                    ->label('Status')
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
                        'pending' => 'Belum',
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

                TextColumn::make('courier')
                    ->label('Kurir')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tracking_number')
                    ->label('Resi')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y • H:i')
                    ->sortable(),
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
            ->paginated(15)
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->emptyStateHeading('Belum ada pesanan')
            ->emptyStateDescription('Pesanan pelanggan akan muncul di sini.')
            ->emptyStateActions([
                CreateAction::make()->label('Buat Pesanan'),
            ]);
    }
}
