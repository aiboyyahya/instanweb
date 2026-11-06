<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\User;
use App\Services\ShippingCostService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan Pesanan')
                    ->description('Data pesanan dari pelanggan.')
                    ->icon('heroicon-o-receipt-percent')
                    ->columns(2)
                    ->schema([
                        TextInput::make('order_code')
                            ->label('Kode Pesanan')
                            ->disabled()
                            ->required(),

                        Select::make('customer_id')
                            ->label('Pelanggan')
                            ->disabled()
                            ->options(User::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        ToggleButtons::make('status')
                            ->label('Status Pesanan')
                            ->inline()
                            ->options([
                                'pending' => 'Menunggu',
                                'packing' => 'Dikemas',
                                'sent' => 'Dikirim',
                                'done' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->icons([
                                'pending' => 'heroicon-o-clock',
                                'packing' => 'heroicon-o-archive-box',
                                'sent' => 'heroicon-o-truck',
                                'done' => 'heroicon-o-check-circle',
                                'cancelled' => 'heroicon-o-x-circle',
                            ])
                            ->colors([
                                'pending' => 'warning',
                                'packing' => 'info',
                                'sent' => 'primary',
                                'done' => 'success',
                                'cancelled' => 'danger',
                            ])
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('total')
                            ->label('Total Pembayaran')
                            ->disabled()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0.0),

                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'transfer_bank' => 'Transfer Bank',
                                'cod' => 'Bayar di Tempat (COD)',
                                'qris' => 'QRIS / E-Wallet',
                                'manual' => 'Konfirmasi Manual',
                                'midtrans' => 'Midtrans (Online)',
                            ])
                            ->default('manual')
                            ->disabled()
                            ->required(),
                    ]),

                Section::make('Alamat & Kurir')
                    ->description('Isi nomor resi saat pesanan dikirim.')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        Textarea::make('address')
                            ->label('Alamat')
                            ->disabled()
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('province')
                            ->label('Provinsi')
                            ->disabled(),

                        TextInput::make('city')
                            ->label('Kota')
                            ->disabled(),

                        TextInput::make('district')
                            ->label('Kecamatan')
                            ->disabled(),

                        TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->maxLength(5)
                            ->disabled()
                            ->helperText('Ditetapkan saat pelanggan menyelesaikan checkout.'),

                        Select::make('courier')
                            ->label('Kurir')
                            ->options(app(ShippingCostService::class)->courierOptions())
                            ->disabled()
                            ->helperText('Kurir dipilih pelanggan saat checkout dan tidak dapat diubah.'),

                        TextInput::make('courier_service')
                            ->label('Layanan Kurir')
                            ->disabled()
                            ->helperText('Otomatis disesuaikan dari kurir yang dipilih pelanggan.'),

                        TextInput::make('shipping_cost')
                            ->label('Ongkos Kirim')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->disabled()
                            ->helperText('Dihitung otomatis dari jarak dan tarif kurir saat checkout.'),

                        TextInput::make('distance_km')
                            ->label('Jarak Pengiriman')
                            ->numeric()
                            ->suffix('km')
                            ->disabled()
                            ->placeholder('Belum dihitung'),

                        TextInput::make('tracking_number')
                            ->label('Nomor Resi')
                            ->disabled()
                            ->placeholder('Tracking number akan muncul otomatis saat status dikirim')
                            ->columnSpanFull(),
                    ]),

                Section::make('Catatan')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->disabled()
                            ->default(null)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
