<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Transaction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\User;
use Filament\Forms\Components\ToggleButtons;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_code')
                    ->disabled()
                    ->required(),
                Select::make('customer_id')
                    ->disabled()
                    ->label('Customer')
                    ->options(User::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Textarea::make('address')
                    ->disabled()
                    ->label('Alamat')
                    ->required()
                    ->columnSpanFull(),
                ToggleButtons::make('status')
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
                    ->required(),
                TextInput::make('total')
                    ->disabled()
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0.0),
                Textarea::make('notes')
                    ->disabled()
                    ->label('Catatan')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('payment_method')
                    ->disabled()
                    ->label('Metode Pembayaran')
                    ->options([
                        'midtrans' => 'Midtrans',
                    ])
                    ->default('midtrans')
                    ->required(),
                TextInput::make('province')
                    ->disabled()
                    ->label('Provinsi'),
                TextInput::make('city')
                    ->disabled()
                    ->label('Kota'),
                TextInput::make('district')
                    ->disabled()
                    ->label('Kecamatan'),
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
                    ->placeholder('Tracking number akan muncul otomatis saat status dikirim'),
            ]);
    }
}
