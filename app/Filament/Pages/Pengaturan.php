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
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog';
    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 100;
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $title = 'Pengaturan';

    protected string $view = 'filament.pages.pengaturan';

    public ?array $data = [];

    public function mount(): void
    {
        $store = Store::first();
        if ($store) {
            $this->form->fill($store->toArray());
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
          
                Section::make('Informasi Toko')
                    ->schema([
                        TextInput::make('store_name')
                            ->label('Nama Toko')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),

                        TextInput::make('address')
                            ->label('Alamat')
                            ->maxLength(255)
                            ->columnSpan('full'),

                        FileUpload::make('logo')
                            ->label('Logo')
                            ->disk('public')
                            ->image()
                            ->directory('logos')
                            ->imageEditor()
                            ->columnSpan('full'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpan('full'),
                    ])
                    ->columns(2),

              
                Section::make()
                    ->schema([
                        
                        Section::make('Sosial Media')
                            ->schema([
                                TextInput::make('instagram')
                                    ->label('Instagram')
                                    ->prefixIcon('heroicon-o-link'),
                                TextInput::make('tiktok')
                                    ->label('Tiktok')
                                    ->prefixIcon('heroicon-o-link'),
                                TextInput::make('whatsapp')
                                    ->label('WhatsApp')
                                    ->prefixIcon('heroicon-o-phone'),
                                TextInput::make('facebook')
                                    ->label('Facebook')
                                    ->prefixIcon('heroicon-o-link'),
                            ])
                            ->columns(1),

                       
                        Section::make('Marketplace')
                            ->schema([
                                TextInput::make('shopee')->label('Shopee'),
                                TextInput::make('tokopedia')->label('Tokopedia'),
                            ])
                            ->columns(1),
                    ])
                    ->columns(2), 

              
                Section::make('Midtrans Payment Gateway')
                    ->schema([
                        TextInput::make('midtrans_client_key')
                            ->label('Midtrans Client Key')
                            ->required()
                            ->columnSpan('full'),

                        TextInput::make('midtrans_server_key')
                            ->label('Midtrans Server Key')
                            ->required()
                            ->columnSpan('full'),

                        Toggle::make('is_production')
                            ->label('Production Mode')
                            ->helperText('Aktifkan untuk mode produksi, nonaktifkan untuk sandbox.')
                            ->columnSpan('full'),
                    ])
                    ->columns(1),

                Section::make('Lokasi Asal Pengiriman')
                    ->schema([
                        TextInput::make('origin_city_id')
                            ->label('Origin City ID')
                            ->helperText('ID kota asal pengiriman dari RajaOngkir')
                            ->columnSpan('full'),

                        TextInput::make('origin_district_id')
                            ->label('Origin District ID')
                            ->helperText('ID kecamatan asal pengiriman dari RajaOngkir (opsional, lebih spesifik)')
                            ->columnSpan('full'),
                    ])
                    ->columns(1),
            ])
            ->columns(1)
            ->statePath('data');
    }

    public function save()
    {
        $data = $this->form->getState();

        $store = Store::first() ?? new Store();
        $store->fill($data);
        $store->save();

        cache()->forget('store_data');

        Notification::make()
            ->title('Pengaturan berhasil disimpan!')
            ->success()
            ->send();
    }
}
