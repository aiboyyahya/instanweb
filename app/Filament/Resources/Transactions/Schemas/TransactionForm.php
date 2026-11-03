<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\User;
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
                    ->description('Data pesanan dari pelanggan. Sebagian data dikunci agar riwayat transaksi tetap utuh.')
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
                            ->disabled()
                            ->options([
                                'midtrans' => 'Midtrans',
                            ])
                            ->default('midtrans')
                            ->required(),
                    ]),

                Section::make('Alamat & Pengiriman')
                    ->description('Lengkapi informasi kurir dan nomor resi saat pesanan dikirim.')
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
                            ->numeric(),

                        Select::make('courier')
                            ->label('Kurir')
                            ->options([
                                'jne' => 'JNE',
                                'tiki' => 'TIKI',
                                'pos' => 'POS Indonesia',
                                'jnt' => 'J&T Express',
                            ]),

                        TextInput::make('courier_service')
                            ->label('Layanan Kurir'),

                        TextInput::make('tracking_number')
                            ->label('Nomor Resi')
                            ->disabled()
                            ->placeholder('Tracking number akan muncul otomatis saat status dikirim')
                            ->columnSpanFull(),
                    ]),

                Section::make('Catatan Pelanggan')
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
