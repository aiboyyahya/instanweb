<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Pesanan Terbaru';

    protected static ?string $description = '10 pesanan terakhir masuk.';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->ordersQuery())
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode Pesanan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('semibold'),

                TextColumn::make('customer.name')
                    ->label('Pelanggan')
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

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y • H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated(false)
            ->recordUrl(fn (Transaction $record): string => TransactionResource::getUrl('edit', ['record' => $record]))
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->emptyStateHeading('Belum ada pesanan')
            ->emptyStateDescription('Pesanan yang masuk dari pelanggan akan tampil di sini.');
    }

    /**
     * Pesanan terbaru hanya dari produk milik toko yang sedang masuk.
     */
    private function ordersQuery(): Builder
    {
        return Transaction::query()
            ->when(
                auth()->user()?->store?->id,
                fn (Builder $query, int $storeId) => $query->whereHas(
                    'items.product',
                    fn (Builder $query) => $query->where('store_id', $storeId),
                ),
            )
            ->latest()
            ->limit(10);
    }
}
