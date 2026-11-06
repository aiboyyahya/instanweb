<?php

namespace App\Filament\Pages;

use App\Models\Store;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
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

    protected ?string $subheading = 'Kelola identitas toko, kontak, dan asal pengiriman.';

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
                    ->description('Tampil di header dan footer toko.')
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
                            ->label('Logo')
                            ->helperText('Logo persegi/bulat, latar transparan bila tersedia.')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->directory('logos')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Ceritakan singkat toko Anda.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Banner Toko')
                    ->description('Maksimal 3 gambar untuk hero carousel. Kosongkan untuk pakai bawaan.')
                    ->icon('heroicon-o-photo')
                    ->columns(3)
                    ->schema([
                        FileUpload::make('banner_1')
                            ->label('Banner 1')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->directory('store-banners')
                            ->visibility('public')
                            ->maxSize(10240),

                        FileUpload::make('banner_2')
                            ->label('Banner 2')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->directory('store-banners')
                            ->visibility('public')
                            ->maxSize(10240),

                        FileUpload::make('banner_3')
                            ->label('Banner 3')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->directory('store-banners')
                            ->visibility('public')
                            ->maxSize(10240),
                    ]),

                Section::make('Kontak & Media Sosial')
                    ->description('Kanal komunikasi dan marketplace toko.')
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
                    ->description('Titik lokasi toko. Ongkir dihitung dari titik ini.')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        ViewField::make('store_location_map')
                            ->view('filament.forms.store-map-picker')
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->placeholder('-6.2000000')
                            ->helperText('Terisi otomatis saat Anda mengklik peta.'),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->placeholder('106.8166667')
                            ->helperText('Terisi otomatis saat Anda mengklik peta.'),

                        TextInput::make('origin_city_id')
                            ->label('Origin City ID (opsional)')
                            ->helperText('ID kota asal pengiriman dari RajaOngkir, bila masih digunakan.')
                            ->columnSpanFull(),

                        TextInput::make('origin_district_id')
                            ->label('Origin District ID (opsional)')
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
