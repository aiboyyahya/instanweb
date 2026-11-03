<?php

namespace App\Filament\Pages;

use App\Models\Store;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class Pengaturan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|UnitEnum|null $navigationGroup = 'Sistem';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $title = 'Pengaturan Toko';

    protected ?string $subheading = 'Kelola identitas toko, kanal kontak, pembayaran, dan asal pengiriman.';

    protected string $view = 'filament.pages.pengaturan';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $store = $user?->store ?: Store::first();

        if ($store) {
            $this->form->fill($store->toArray());
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Identitas Toko')
                    ->description('Informasi ini tampil pada storefront, header, dan footer toko.')
                    ->icon('heroicon-o-building-storefront')
                    ->columns(2)
                    ->schema([
                        TextInput::make('store_name')
                            ->label('Nama Toko')
                            ->placeholder('Contoh: Toko Kopi Nusantara')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('address')
                            ->label('Alamat')
                            ->placeholder('Alamat lengkap toko')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        FileUpload::make('logo')
                            ->label('Logo Toko')
                            ->helperText('Gunakan logo persegi atau bulat dengan latar transparan bila tersedia.')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->directory('logos')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Deskripsi Toko')
                            ->placeholder('Ceritakan singkat produk dan nilai toko Anda.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Kontak & Sosial Media')
                    ->description('Hubungkan pelanggan dengan kanal komunikasi dan marketplace toko.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->columns(2)
                    ->schema([
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->placeholder('08xxxxxxxxxx')
                            ->prefixIcon('heroicon-o-phone'),

                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->placeholder('@namatoko')
                            ->prefixIcon('heroicon-o-link'),

                        TextInput::make('tiktok')
                            ->label('TikTok')
                            ->placeholder('@namatoko')
                            ->prefixIcon('heroicon-o-link'),

                        TextInput::make('facebook')
                            ->label('Facebook')
                            ->prefixIcon('heroicon-o-link'),

                        TextInput::make('shopee')
                            ->label('Toko Shopee')
                            ->prefixIcon('heroicon-o-shopping-bag'),

                        TextInput::make('tokopedia')
                            ->label('Toko Tokopedia')
                            ->prefixIcon('heroicon-o-shopping-bag'),
                    ]),

                // Section::make('Pembayaran Online')
                //     ->description('Kredensial Midtrans digunakan untuk memproses pembayaran pelanggan.')
                //     ->icon('heroicon-o-credit-card')
                //     ->columns(1)
                //     ->schema([
                //         TextInput::make('midtrans_client_key')
                //             ->label('Midtrans Client Key')
                //             ->required()
                //             ->columnSpanFull(),

                //         TextInput::make('midtrans_server_key')
                //             ->label('Midtrans Server Key')
                //             ->required()
                //             ->columnSpanFull(),

                //         Toggle::make('is_production')
                //             ->label('Mode Produksi')
                //             ->helperText('Aktifkan untuk transaksi nyata. Nonaktifkan untuk mode sandbox.')
                //             ->columnSpanFull(),
                //     ]),

                Section::make('Asal Pengiriman')
                    ->description('Tentukan lokasi asal agar perhitungan ongkir sesuai.')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        TextInput::make('origin_city_id')
                            ->label('Origin City ID')
                            ->helperText('ID kota asal pengiriman dari RajaOngkir.')
                            ->columnSpanFull(),

                        TextInput::make('origin_district_id')
                            ->label('Origin District ID')
                            ->helperText('ID kecamatan asal pengiriman dari RajaOngkir (opsional, lebih spesifik).')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1)
            ->statePath('data');
    }

    public function save()
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $store = $user?->store ?: Store::first() ?: new Store();

        if (! $store->user_id && $user) {
            $store->user_id = $user->id;
        }

        $store->fill($data);
        $store->save();

        cache()->forget('store_data');

        Notification::make()
            ->title('Pengaturan berhasil disimpan!')
            ->success()
            ->send();
    }
}
