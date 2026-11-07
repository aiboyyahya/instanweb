<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Rating;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedStores();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedTransactions();
        $this->seedRatings();
    }

    /**
     * Customer IDs after seeding (role=user): 1-5
     * Store IDs: auto-assigned
     * Products: 8+ per store, 40 total
     */
    private function seedUsers(): void
    {
        $customers = [
            ['name' => 'Andi Pratama', 'email' => 'andi.pratama@mail.com', 'phone_number' => '081234567890', 'address' => 'Jl. Merdeka No. 12, Jakarta Selatan'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@mail.com', 'phone_number' => '082345678901', 'address' => 'Jl. Sudirman No. 45, Bandung'],
            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@mail.com', 'phone_number' => '083456789012', 'address' => 'Jl. Diponegoro No. 7, Surabaya'],
            ['name' => 'Rina Wulandari', 'email' => 'rina.wulandari@mail.com', 'phone_number' => '084567890123', 'address' => 'Jl. Ahmad Yani No. 33, Yogyakarta'],
            ['name' => 'Dimas Arya', 'email' => 'dimas.arya@mail.com', 'phone_number' => '085678901234', 'address' => 'Jl. Gatot Subroto No. 8, Semarang'],
        ];

        foreach ($customers as $cust) {
            User::updateOrCreate(
                ['email' => $cust['email']],
                [
                    'name' => $cust['name'],
                    'phone_number' => $cust['phone_number'],
                    'phone' => $cust['phone_number'],
                    'address' => $cust['address'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ]
            );
        }
    }

    private function seedStores(): void
    {
        // Only seed if stores with these slugs don't exist yet
        $existing = Store::whereIn('slug', [
            'warung-tegal-sederhana', 'batik-madura-asli', 'kopi-ndsari',
            'jamuan-kue-kering', 'tukang-jahit-bu-sri',
        ])->pluck('slug')->toArray();

        if (count($existing) >= 5) {
            return;
        }

        $stores = [
            [
                'store_name' => 'Warung Tegal Sederhana',
                'slug' => 'warung-tegal-sederhana',
                'description' => 'Masakan rumahan Tegal autentik. Nasi goring, soto, dan mie ayam buat teman makan siang.',
                'address' => 'Gg. Kenanga No. 12, Tegal',
                'logo' => 'logos/warung-tegal.jpg',
                'banner_1' => 'store-banners/warung-tegal-1.jpg',
                'banner_2' => null,
                'banner_3' => null,
                'instagram' => '@warung.tegal.sdrh',
                'whatsapp' => '0878-1234-5678',
                'shopee' => 'warungtegal_sederhana',
                'origin_city_id' => '3374',
                'origin_district_id' => '337402',
            ],
            [
                'store_name' => 'Batik Madura Asli',
                'slug' => 'batik-madura-asli',
                'description' => 'Batik handmade dari pengrajin Madura. Motif tradisional dan kontemporer, harga bersahabat.',
                'address' => 'Jl. Pabian No. 45, Sampang',
                'logo' => 'logos/batik-madura.jpg',
                'banner_1' => 'store-banners/batik-madura-1.jpg',
                'banner_2' => 'store-banners/batik-madura-2.jpg',
                'banner_3' => null,
                'instagram' => '@batikmadura_asli',
                'whatsapp' => '0853-4567-8901',
                'origin_city_id' => '3521',
                'origin_district_id' => '352101',
            ],
            [
                'store_name' => 'Kopi Ndsari',
                'slug' => 'kopi-ndsari',
                'description' => 'Biji kopi pilihan dari tanah Ngawi. Roasting sendiri, fresh every day. Kirim ke seluruh Indonesia.',
                'address' => 'Jl. Adi Sucipto No. 7, Ngawi',
                'logo' => 'logos/kopi-ndsari.jpg',
                'banner_1' => 'store-banners/kopi-ndsari-1.jpg',
                'banner_2' => null,
                'banner_3' => null,
                'instagram' => '@kopindsari',
                'whatsapp' => '0857-2345-6789',
                'origin_city_id' => '3322',
                'origin_district_id' => '332201',
            ],
            [
                'store_name' => 'Jamuan Kue Kering',
                'slug' => 'jamuan-kue-kering',
                'description' => 'Kue kering lebaran dan hampers homemade. Nastar, kastengel, putri salju, semua from scratch.',
                'address' => 'Jl. Gatot Subroto No. 33, Solo',
                'logo' => 'logos/jamuan-kue.jpg',
                'banner_1' => 'store-banners/jamuan-kue-1.jpg',
                'banner_2' => 'store-banners/jamuan-kue-2.jpg',
                'banner_3' => null,
                'instagram' => '@jamuankue_kering',
                'tiktok' => '@jamuankue',
                'whatsapp' => '0813-7890-1234',
                'origin_city_id' => '3224',
                'origin_district_id' => '322401',
            ],
            [
                'store_name' => 'Tukang Jahit Bu Sri',
                'slug' => 'tukang-jahit-bu-sri',
                'description' => 'Jasa jahit dan penjualan busana muslim siap pakai. Gamis, kebaya, seragam sekolah, harga terjangkau.',
                'address' => 'Gg. Melati No. 5, Karawang',
                'logo' => 'logos/bu-sri.jpg',
                'banner_1' => 'store-banners/bu-sri-1.jpg',
                'banner_2' => null,
                'banner_3' => null,
                'instagram' => '@jahit.busri',
                'whatsapp' => '0856-1234-5678',
                'origin_city_id' => '3216',
                'origin_district_id' => '321605',
            ],
        ];

        $reputationMap = [
            'warung-tegal-sederhana'  => 'tinggi',
            'batik-madura-asli'       => 'tinggi',
            'kopi-ndsari'             => 'tinggi',
            'jamuan-kue-kering'       => 'menengah',
            'tukang-jahit-bu-sri'     => 'menengah',
        ];

        foreach ($stores as $storeData) {
            $slug = $storeData['slug'];
            $adminEmail = 'owner_' . str_replace('-', '_', $slug) . '@demo.com';

            $adminUser = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => ucwords(str_replace(['&', '-'], ['dan', ' '], $storeData['store_name'])),
                    'phone_number' => '0812-XXXX-XXXX',
                    'phone' => '0812-XXXX-XXXX',
                    'address' => $storeData['address'],
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ]
            );

            Store::updateOrCreate(
                ['slug' => $slug],
                [...$storeData, 'user_id' => $adminUser->id]
            );
        }
    }

    private function seedCategories(): void
    {
        $storeIds = [
            'warung-tegal-sederhana'  => Store::where('slug', 'warung-tegal-sederhana')->value('id'),
            'batik-madura-asli'       => Store::where('slug', 'batik-madura-asli')->value('id'),
            'kopi-ndsari'             => Store::where('slug', 'kopi-ndsari')->value('id'),
            'jamuan-kue-kering'       => Store::where('slug', 'jamuan-kue-kering')->value('id'),
            'tukang-jahit-bu-sri'     => Store::where('slug', 'tukang-jahit-bu-sri')->value('id'),
        ];

        $categories = [
            // Warung Tegal
            [$storeIds['warung-tegal-sederhana'], 'Makanan', 'warung-makanan'],
            [$storeIds['warung-tegal-sederhana'], 'Minuman', 'warung-minuman'],
            // Batik Madura
            [$storeIds['batik-madura-asli'], 'Kemeja Batik', 'batik-kemeja'],
            [$storeIds['batik-madura-asli'], 'Gamis & Sarung', 'batik-sarung'],
            [$storeIds['batik-madura-asli'], 'Tas & Aksesoris', 'batik-aksesoris'],
            // Kopi Ndsari
            [$storeIds['kopi-ndsari'], 'Biji Kopi', 'kopi-biji'],
            [$storeIds['kopi-ndsari'], 'Gilingan Kopi', 'kopi-giling'],
            [$storeIds['kopi-ndsari'], 'Minuman Siap Seduh', 'kopi-siap-seduh'],
            // Kue Kering
            [$storeIds['jamuan-kue-kering'], 'Kue Kering', 'kue-kering'],
            [$storeIds['jamuan-kue-kering'], 'Hampers', 'hampers'],
            // Jahit
            [$storeIds['tukang-jahit-bu-sri'], 'Gamis & Busana Muslim', 'jahit-muslim'],
            [$storeIds['tukang-jahit-bu-sri'], 'Kebaya & Seragam', 'jahit-kebaya'],
            [$storeIds['tukang-jahit-bu-sri'], 'Jasa Revisi & Jahit', 'jahit-revisi'],
        ];

        foreach ($categories as [$storeId, $name, $slug]) {
            Category::updateOrCreate(
                ['slug' => $slug, 'store_id' => $storeId],
                ['category_name' => $name]
            );
        }
    }

    private function seedProducts(): void
    {
        // Product data keyed by name for easy reference
        $products = [
            // Store 1: Warung Tegal Sederhana (8 products)
            'Nasi Goring Tegal Porsi'        => ['store_slug' => 'warung-tegal-sederhana', 'category_slug' => 'warung-makanan', 'desc' => 'Nasi goring khas Tegal dengan tempe, telur, dan sambal goreng. Porsi kenyang.', 'buy' => 8000, 'sell' => 15000, 'stock' => 50, 'weight' => 400],
            'Soto Daging Tegal'              => ['store_slug' => 'warung-tegal-sederhana', 'category_slug' => 'warung-makanan', 'desc' => 'Soto daging kuah kuning, sayur bening, lontong. Resep turun temurun.', 'buy' => 10000, 'sell' => 18000, 'stock' => 30, 'weight' => 350],
            'Mie Ayam Bakso Spesial'         => ['store_slug' => 'warung-tegal-sederhana', 'category_slug' => 'warung-makanan', 'desc' => 'Mie ayam dengan bakso sapi jumbo, pangsit, dan kuah kaldu spesial.', 'buy' => 7000, 'sell' => 15000, 'stock' => 40, 'weight' => 450],
            'Ayam Goreng Kremes'             => ['store_slug' => 'warung-tegal-sederhana', 'category_slug' => 'warung-makanan', 'desc' => 'Ayam goreng renyah dengan taburan kremes gurih, nasi putih anget.', 'buy' => 9000, 'sell' => 16000, 'stock' => 25, 'weight' => 300],
            'Es Teh Manis Segar'             => ['store_slug' => 'warung-tegal-sederhana', 'category_slug' => 'warung-minuman', 'desc' => 'Teh manis dingin segar, langsung dari kulkas. Gatos untuk cuaca panas.', 'buy' => 2000, 'sell' => 5000, 'stock' => 100, 'weight' => 300],
            'Jus Alpukat Gula Aren'          => ['store_slug' => 'warung-tegal-sederhana', 'category_slug' => 'warung-minuman', 'desc' => 'Jus alpukat kental dicampur gula aren asli, creamy dan legit.', 'buy' => 8000, 'sell' => 15000, 'stock' => 35, 'weight' => 350],
            'Pisang Goreng Crispy'           => ['store_slug' => 'warung-tegal-sederhana', 'category_slug' => 'warung-minuman', 'desc' => 'Pisang goreng tepung crispy, isian coklat keju atau nangka.', 'buy' => 3000, 'sell' => 8000, 'stock' => 60, 'weight' => 150],
            'Sambal Terasi Buatan Rumah'     => ['store_slug' => 'warung-tegal-sederhana', 'category_slug' => 'warung-minuman', 'desc' => 'Sambal terasi homemade, pedas mantap. Cocok untuk teman makan nasi.', 'buy' => 5000, 'sell' => 12000, 'stock' => 40, 'weight' => 200],
            // Store 2: Batik Madura Asli (8 products)
            'Kemeja Batik Madura Modern'     => ['store_slug' => 'batik-madura-asli',      'category_slug' => 'batik-kemeja',    'desc' => 'Kemeja batik motif Madura modern, bahan cotton premium, slim fit.', 'buy' => 85000, 'sell' => 175000, 'stock' => 20, 'weight' => 300],
            'Gamis Batik Solo Classic'       => ['store_slug' => 'batik-madura-asli',      'category_slug' => 'batik-sarung',    'desc' => 'Gamis batik motif solo klasik, bahan voal adem, cocok untuk hijaber.', 'buy' => 120000, 'sell' => 250000, 'stock' => 15, 'weight' => 400],
            'Sarung Batik Premium'           => ['store_slug' => 'batik-madura-asli',      'category_slug' => 'batik-sarung',    'desc' => 'Sarung batik tenun Madura, motif tradisional, nyaman dipakai sholat.', 'buy' => 75000, 'sell' => 150000, 'stock' => 25, 'weight' => 250],
            'Kain Batik Motif Ceplok'        => ['store_slug' => 'batik-madura-asli',      'category_slug' => 'batik-kemeja',    'desc' => 'Kain batik cap motif ceplok, lebar 110cm, panjang 2 meter.', 'buy' => 90000, 'sell' => 185000, 'stock' => 18, 'weight' => 350],
            'Tas Batik Wanita'               => ['store_slug' => 'batik-madura-asli',      'category_slug' => 'batik-aksesoris', 'desc' => 'Tas selempat wanita motif batik, dilapisi tahan air, compact.', 'buy' => 55000, 'sell' => 120000, 'stock' => 30, 'weight' => 200],
            'Sabuk Kulit Batik'              => ['store_slug' => 'batik-madura-asli',      'category_slug' => 'batik-aksesoris', 'desc' => 'Sabuk kulit asli dengan detail motif batik, buckle emas.', 'buy' => 45000, 'sell' => 95000, 'stock' => 22, 'weight' => 150],
            'Kerudung Segi Empat Batik'      => ['store_slug' => 'batik-madura-asli',      'category_slug' => 'batik-sarung',    'desc' => 'Kerudung segi empat bahan paris print batik, instant dan adem.', 'buy' => 35000, 'sell' => 75000, 'stock' => 40, 'weight' => 100],
            'Topi Batik Pria'                => ['store_slug' => 'batik-madura-asli',      'category_slug' => 'batik-aksesoris', 'desc' => 'Topi bucket hat motif batik, unisex, bahan cotton.', 'buy' => 30000, 'sell' => 65000, 'stock' => 28, 'weight' => 120],
            // Store 3: Kopi Ndsari (8 products)
            'Kopi Arabika Ngawi 200gr'       => ['store_slug' => 'kopi-ndsari',            'category_slug' => 'kopi-biji',       'desc' => 'Biji kopi arabika pilihan dari lereng pegunungan Ngawi, medium roast.', 'buy' => 35000, 'sell' => 75000, 'stock' => 50, 'weight' => 250],
            'Kopi Robusta Lamongan 200gr'    => ['store_slug' => 'kopi-ndsari',            'category_slug' => 'kopi-biji',       'desc' => 'Biji kopi robusta asli Lamongan, strong body, cocok untuk espresso.', 'buy' => 25000, 'sell' => 55000, 'stock' => 60, 'weight' => 250],
            'Kopi Blend Ndsari House 200gr'  => ['store_slug' => 'kopi-ndsari',            'category_slug' => 'kopi-biji',       'desc' => 'Blend rahasia kami, arabika + robusta, balanced dan smooth.', 'buy' => 30000, 'sell' => 65000, 'stock' => 45, 'weight' => 250],
            'Kopi Gayo Aceh 200gr'           => ['store_slug' => 'kopi-ndsari',            'category_slug' => 'kopi-biji',       'desc' => 'Biji kopi gayo premium dari Aceh, full body dengan hint fruity.', 'buy' => 45000, 'sell' => 95000, 'stock' => 30, 'weight' => 250],
            'Giling Kopi Tubruk Medium'      => ['store_slug' => 'kopi-ndsari',            'category_slug' => 'kopi-giling',     'desc' => 'Kopi giling medium untuk tubruk, siap seduh, aroma kuat.', 'buy' => 32000, 'sell' => 70000, 'stock' => 55, 'weight' => 250],
            'Giling Kopi Fine Espresso'      => ['store_slug' => 'kopi-ndsari',            'category_slug' => 'kopi-giling',     'desc' => 'Kopi giling halus untuk espresso machine, crema tebal.', 'buy' => 32000, 'sell' => 70000, 'stock' => 40, 'weight' => 250],
            'Kopi Instan Susu 3in1'          => ['store_slug' => 'kopi-ndsari',            'category_slug' => 'kopi-siap-seduh', 'desc' => 'Kopi instan 3in1 dengan susu, manis pas. Isi 15 sachet.', 'buy' => 18000, 'sell' => 35000, 'stock' => 80, 'weight' => 200],
            'Kopi Instan Black 2in1'         => ['store_slug' => 'kopi-ndsari',            'category_slug' => 'kopi-siap-seduh', 'desc' => 'Kopi hitam instan tanpa gula, bersih dan praktis. Isi 15 sachet.', 'buy' => 15000, 'sell' => 30000, 'stock' => 90, 'weight' => 200],
            // Store 4: Jamuan Kue Kering (8 products)
            'Nastar Premium Butter'          => ['store_slug' => 'jamuan-kue-kering',      'category_slug' => 'kue-kering',      'desc' => 'Nastar lembut isi selai nanas homemade, butter premium. Isi 500gr.', 'buy' => 45000, 'sell' => 85000, 'stock' => 30, 'weight' => 550],
            'Kastengel Keju Asli'            => ['store_slug' => 'jamuan-kue-kering',      'category_slug' => 'kue-kering',      'desc' => 'Kastengel gurih dengan keju mozarella asli. Isi 500gr.', 'buy' => 42000, 'sell' => 80000, 'stock' => 25, 'weight' => 550],
            'Putri Salju Coklat'             => ['store_slug' => 'jamuan-kue-kering',      'category_slug' => 'kue-kering',      'desc' => 'Putri salju lumer di mulut, taburan gula halus, isian coklat.', 'buy' => 40000, 'sell' => 75000, 'stock' => 35, 'weight' => 550],
            'Cookie Coklat Chip'             => ['store_slug' => 'jamuan-kue-kering',      'category_slug' => 'kue-kering',      'desc' => 'Cookie renyah dengan dark chocolate chip, premium quality.', 'buy' => 38000, 'sell' => 72000, 'stock' => 28, 'weight' => 500],
            'Kue Lapis Legit Jaret'          => ['store_slug' => 'jamuan-kue-kering',      'category_slug' => 'kue-kering',      'desc' => 'Kue lapis legit berjenjang, resep keluarga 3 generasi.', 'buy' => 55000, 'sell' => 110000, 'stock' => 15, 'weight' => 600],
            'Hampers Lebaran A'              => ['store_slug' => 'jamuan-kue-kering',      'category_slug' => 'hampers',         'desc' => 'Paket hampers lebaran isi nastar, kastengel, putri salju, serial.', 'buy' => 120000, 'sell' => 225000, 'stock' => 10, 'weight' => 1200],
            'Hampers Lebaran B'              => ['store_slug' => 'jamuan-kue-kering',      'category_slug' => 'hampers',         'desc' => 'Paket hampers deluxe isi 4 jenis kue kering premium + cookies.', 'buy' => 150000, 'sell' => 275000, 'stock' => 8, 'weight' => 1400],
            'Kue Sarang Semut'               => ['store_slug' => 'jamuan-kue-kering',      'category_slug' => 'kue-kering',      'desc' => 'Kue tradisional renyah berongga, manis gurih pas. Isi 500gr.', 'buy' => 35000, 'sell' => 65000, 'stock' => 40, 'weight' => 550],
            // Store 5: Tukang Jahit Bu Sri (8 products)
            'Gamis Syari Polos Premium'      => ['store_slug' => 'tukang-jahit-bu-sri',    'category_slug' => 'jahit-muslim',    'desc' => 'Gamis syari bahan katun rayon premium, longgar dan adem. Ready size M-XL.', 'buy' => 65000, 'sell' => 135000, 'stock' => 20, 'weight' => 400],
            'Gamis Brokat Elegan'            => ['store_slug' => 'tukang-jahit-bu-sri',    'category_slug' => 'jahit-muslim',    'desc' => 'Gamis brokat mewah dengan detail payet, cocok untuk acara spesial.', 'buy' => 95000, 'sell' => 195000, 'stock' => 12, 'weight' => 500],
            'Kebaya Jawa Modern'             => ['store_slug' => 'tukang-jahit-bu-sri',    'category_slug' => 'jahit-kebaya',    'desc' => 'Kebaya modern potongan baru, bahan sutra, cocok untuk kondangan.', 'buy' => 75000, 'sell' => 160000, 'stock' => 15, 'weight' => 350],
            'Seragam Sekolah Anak'           => ['store_slug' => 'tukang-jahit-bu-sri',    'category_slug' => 'jahit-kebaya',    'desc' => 'Seragam sekolah lengkap SD/SMP/SMA, jahitan rapi, bahan berkualitas.', 'buy' => 45000, 'sell' => 95000, 'stock' => 25, 'weight' => 300],
            'Hijab Pashmina Instant'         => ['store_slug' => 'tukang-jahit-bu-sri',    'category_slug' => 'jahit-muslim',    'desc' => 'Hijab pashmina instant bahan voal, mudah dipakai, berbagai warna.', 'buy' => 20000, 'sell' => 45000, 'stock' => 50, 'weight' => 80],
            'Mukena Setrika Otomatis'        => ['store_slug' => 'tukang-jahit-bu-sri',    'category_slug' => 'jahit-muslim',    'desc' => 'Mukena bahan sprei premium, tipis ringan, include tas carry.', 'buy' => 35000, 'sell' => 75000, 'stock' => 30, 'weight' => 250],
            'Jasa Jahit Permak'              => ['store_slug' => 'tukang-jahit-bu-sri',    'category_slug' => 'jahit-revisi',    'desc' => 'Jasa permak dan perbaiki pakaian, perbaikan celana, ganti resleting.', 'buy' => 15000, 'sell' => 35000, 'stock' => 999, 'weight' => 0],
            'Jasa Jahit Custom Order'        => ['store_slug' => 'tukang-jahit-bu-sri',    'category_slug' => 'jahit-revisi',    'desc' => 'Jasa jahit custom sesuai desain, konsultasi gratis. Minimal order 2 pcs.', 'buy' => 50000, 'sell' => 120000, 'stock' => 999, 'weight' => 0],
        ];

        $storeIds = Store::pluck('id', 'slug')->toArray();
        $catIds = Category::pluck('id', 'slug')->toArray();

        foreach ($products as $name => $data) {
            $productId = $data['category_slug'];
            unset($data['category_slug']);

            $product = Product::updateOrCreate(
                ['product_name' => $name, 'store_id' => $storeIds[$data['store_slug']]],
                [
                    'description' => $data['desc'],
                    'purchase_price' => $data['buy'],
                    'selling_price' => $data['sell'],
                    'stock' => $data['stock'],
                    'status' => 'active',
                    'weight' => $data['weight'],
                    'category_id' => $catIds[$productId] ?? null,
                    'image' => 'product-imgs/' . Str::slug($name) . '-main.jpg',
                ]
            );

            // Extra images
            $extraCount = match (true) {
                in_array($name, ['Smartphone X Pro 128GB', 'Laptop Ultra Slim 14"']) => 2,
                in_array($name, ['Rendang Daging Sapi 500gr', 'Ayam Pop 500gr']) => 3,
                in_array($name, ['Gamis Syari Premium', 'Kemeja Casual Pria']) => 3,
                in_array($name, ['Sepatu Lari AeroMax']) => 2,
                in_array($name, ['Serum Wajah Glow Up', 'Sunscreen SPF 50+']) => 2,
                default => 1,
            };
            for ($i = 2; $i <= $extraCount; $i++) {
                ProductImage::firstOrCreate(
                    ['product_id' => $product->id, 'image_file' => "product-imgs/" . Str::slug($name) . "-{$i}.jpg"],
                    ['image_file' => "product-imgs/" . Str::slug($name) . "-{$i}.jpg"]
                );
            }
        }
    }

    private function seedTransactions(): void
    {
        $customerIds = User::where('role', 'user')->orderBy('id')->pluck('id')->toArray();
        // [1, 2, 3, 4, 5]

        $storeIds = [
            'warung-tegal-sederhana'  => Store::where('slug', 'warung-tegal-sederhana')->value('id'),
            'batik-madura-asli'       => Store::where('slug', 'batik-madura-asli')->value('id'),
            'kopi-ndsari'             => Store::where('slug', 'kopi-ndsari')->value('id'),
            'jamuan-kue-kering'       => Store::where('slug', 'jamuan-kue-kering')->value('id'),
            'tukang-jahit-bu-sri'     => Store::where('slug', 'tukang-jahit-bu-sri')->value('id'),
        ];

        $productIds = Product::orderBy('id')->pluck('id', 'product_name')->toArray();

        $makeDef = function($storeId, $custIdx, $productNames, $status, $payStatus) use ($productIds, $customerIds) {
            $items = array_map(fn($n) => $productIds[$n] ?? null, $productNames);
            $items = array_filter($items);
            return [$storeId, $customerIds[$custIdx], $items, $status, $payStatus];
        };

        $defs = [
            // --- DONE + PAID transactions ---
            $makeDef($storeIds['warung-tegal-sederhana'], 0, ['Nasi Goring Tegal Porsi', 'Soto Daging Tegal'],       'done',   'paid'),
            $makeDef($storeIds['warung-tegal-sederhana'], 1, ['Mie Ayam Bakso Spesial', 'Ayam Goreng Kremes'],       'done',   'paid'),
            $makeDef($storeIds['warung-tegal-sederhana'], 2, ['Es Teh Manis Segar', 'Jus Alpukat Gula Aren'],         'done',   'paid'),
            $makeDef($storeIds['batik-madura-asli'],      0, ['Kemeja Batik Madura Modern', 'Sarung Batik Premium'],  'done',   'paid'),
            $makeDef($storeIds['batik-madura-asli'],      3, ['Gamis Batik Solo Classic', 'Kerudung Segi Empat Batik'],'done',   'paid'),
            $makeDef($storeIds['kopi-ndsari'],            1, ['Kopi Arabika Ngawi 200gr', 'Giling Kopi Tubruk Medium'],'done',   'paid'),
            $makeDef($storeIds['kopi-ndsari'],            2, ['Kopi Blend Ndsari House 200gr', 'Kopi Instan Susu 3in1'],'done',   'paid'),
            $makeDef($storeIds['jamuan-kue-kering'],      0, ['Nastar Premium Butter', 'Kastengel Keju Asli'],         'done',   'paid'),
            $makeDef($storeIds['jamuan-kue-kering'],      4, ['Hampers Lebaran A', 'Kue Lapis Legit Jaret'],          'done',   'paid'),
            $makeDef($storeIds['tukang-jahit-bu-sri'],    1, ['Gamis Syari Polos Premium', 'Hijab Pashmina Instant'],  'done',   'paid'),
            $makeDef($storeIds['tukang-jahit-bu-sri'],    3, ['Kebaya Jawa Modern', 'Seragam Sekolah Anak'],           'done',   'paid'),
            // --- SENT + PAID transactions ---
            $makeDef($storeIds['warung-tegal-sederhana'], 3, ['Pisang Goreng Crispy', 'Sambal Terasi Buatan Rumah'],  'sent',   'paid'),
            $makeDef($storeIds['batik-madura-asli'],      2, ['Tas Batik Wanita', 'Sabuk Kulit Batik'],               'sent',   'paid'),
            $makeDef($storeIds['kopi-ndsari'],            0, ['Kopi Gayo Aceh 200gr', 'Giling Kopi Fine Espresso'],   'sent',   'paid'),
            $makeDef($storeIds['jamuan-kue-kering'],      2, ['Putri Salju Coklat', 'Cookie Coklat Chip'],            'sent',   'paid'),
            $makeDef($storeIds['tukang-jahit-bu-sri'],    4, ['Mukena Setrika Otomatis'],                            'sent',   'paid'),
            // --- PACKING + PAID transactions ---
            $makeDef($storeIds['warung-tegal-sederhana'], 4, ['Nasi Goring Tegal Porsi'],                            'packing','paid'),
            $makeDef($storeIds['batik-madura-asli'],      4, ['Topi Batik Pria'],                                    'packing','paid'),
            $makeDef($storeIds['kopi-ndsari'],            3, ['Kopi Robusta Lamongan 200gr'],                        'packing','paid'),
            $makeDef($storeIds['jamuan-kue-kering'],      3, ['Hampers Lebaran B'],                                  'packing','paid'),
            $makeDef($storeIds['tukang-jahit-bu-sri'],    0, ['Gamis Brokat Elegan'],                                'packing','paid'),
            // --- PENDING transactions ---
            $makeDef($storeIds['warung-tegal-sederhana'], 1, ['Soto Daging Tegal'],                                  'pending','pending'),
            $makeDef($storeIds['batik-madura-asli'],      1, ['Kain Batik Motif Ceplok'],                            'pending','pending'),
            $makeDef($storeIds['kopi-ndsari'],            4, ['Kopi Instan Black 2in1'],                             'pending','pending'),
            $makeDef($storeIds['jamuan-kue-kering'],      1, ['Kue Sarang Semut'],                                   'pending','pending'),
            $makeDef($storeIds['tukang-jahit-bu-sri'],    2, ['Jasa Jahit Custom Order'],                            'pending','pending'),
            // --- CANCELLED transactions ---
            $makeDef($storeIds['warung-tegal-sederhana'], 2, ['Mie Ayam Bakso Spesial'],                             'cancelled','paid'),
            $makeDef($storeIds['batik-madura-asli'],      0, ['Gamis Batik Solo Classic'],                           'cancelled','failed'),
            $makeDef($storeIds['kopi-ndsari'],            2, ['Kopi Arabika Ngawi 200gr'],                           'cancelled','pending'),
            $makeDef($storeIds['jamuan-kue-kering'],      4, ['Nastar Premium Butter'],                              'cancelled','pending'),
            $makeDef($storeIds['tukang-jahit-bu-sri'],    3, ['Seragam Sekolah Anak'],                               'cancelled','failed'),
        ];

        $cities    = ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang'];
        $provinces = ['DKI Jakarta', 'Jawa Barat', 'Jawa Timur', 'DI Yogyakarta', 'Jawa Tengah'];
        $couriers  = ['JNE', 'AnterAja', 'COD / Kurir Toko'];

        foreach ($defs as [$storeId, $customerId, $productIdsArr, $status, $payStatus]) {
            $customer = User::find($customerId);
            $custIdx = array_search($customerId, $customerIds);
            $cityIdx = $custIdx === false ? 0 : $custIdx;

            $total = 0;
            $itemRecords = [];
            foreach ($productIdsArr as $productId) {
                $product = Product::find($productId);
                if (!$product) continue;
                $total += $product->selling_price;
                $itemRecords[] = [
                    'product_id' => $productId,
                    'price' => $product->selling_price,
                    'quantity' => 1,
                ];
            }
            if (empty($itemRecords)) continue;

            $orderCode = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid((string) random_int(0, 999999))), 0, 6));

            $transaction = Transaction::create([
                'order_code' => $orderCode,
                'customer_id' => $customerId,
                'phone_number' => $customer?->phone_number ?? '0812-XXXX-XXXX',
                'address' => $customer?->address ?? 'Jl. Test No. 1',
                'province' => $provinces[$cityIdx] ?? 'Jawa Barat',
                'city' => $cities[$cityIdx] ?? 'Bandung',
                'district' => 'Centrum',
                'postal_code' => '40115',
                'courier' => $couriers[array_rand($couriers)],
                'courier_service' => 'REG',
                'status' => $status,
                'payment_status' => $payStatus,
                'payment_method' => 'midtrans',
                'total' => $total,
                'notes' => null,
            ]);

            foreach ($itemRecords as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }
        }
    }

    private function seedRatings(): void
    {
        $customerIds = User::where('role', 'user')->orderBy('id')->pluck('id')->toArray();
        // [1, 2, 3, 4, 5]
        $productIds = Product::orderBy('id')->pluck('id', 'product_name')->toArray();

        // Map: product_id => transaction_id by matching transaction items
        $prodToTxn = [];
        foreach (TransactionItem::with('transaction')->get() as $item) {
            $prodToTxn[$item->product_id][] = $item->transaction_id;
        }

        // Ratings: [customer_index(0-4), product_name, rating, comment]
        $ratings = [
            // Warung Tegal
            [0, 'Nasi Goring Tegal Porsi', 5, 'Nasi goring nya enak banget, rasa Tegal authentic!'],
            [1, 'Mie Ayam Bakso Spesial', 5, 'Mie ayam bakso jumbo, porsinya mantap.'],
            [2, 'Es Teh Manis Segar',     4, 'Es teh manis segarnya bikin fresh, murah lagi.'],
            [3, 'Pisang Goreng Crispy',   4, 'Pisang goreng crispy, isiannya pas.'],
            [4, 'Nasi Goring Tegal Porsi', 5, 'Sudah langganan, rasa konsisten enak!'],
            // Batik Madura
            [0, 'Kemeja Batik Madura Modern', 5, 'Kemeja batik nya oke, motif modern, bahan adem.'],
            [0, 'Sarung Batik Premium',       5, 'Sarung batik tenun, kualitas bagus.'],
            [3, 'Gamis Batik Solo Classic',   5, 'Gamis cantik, motif solo nya elegan.'],
            [2, 'Tas Batik Wanita',           4, 'Tas batik unik, compact buat harian.'],
            [4, 'Kain Batik Motif Ceplok',    4, 'Kain batik berkualitas, warnanya bagus.'],
            // Kopi Ndsari
            [1, 'Kopi Arabika Ngawi 200gr', 5, 'Kopi arabikanya mantap, aroma kuat.'],
            [1, 'Giling Kopi Tubruk Medium', 5, 'Giling kopi siap seduh, praktis.'],
            [2, 'Kopi Blend Ndsari House 200gr', 5, 'Blend favorit, smooth dan nikmat.'],
            [0, 'Kopi Gayo Aceh 200gr',      4, 'Kopi gayo premium, worth the price.'],
            [4, 'Kopi Instan Susu 3in1',     4, 'Kopi instan manisnya pas, cocok buat sarapan.'],
            // Jamuan Kue Kering
            [0, 'Nastar Premium Butter',     5, 'Nastar lembut di mulut, selai nanasnya enak.'],
            [0, 'Kastengel Keju Asli',       5, 'Kastengel gurih, keju mozarella asli.'],
            [4, 'Hampers Lebaran A',         5, 'Hampers lengkap, packaging rapi.'],
            [2, 'Putri Salju Coklat',        4, 'Putri salju lumer, isian cokelatnya pas.'],
            [3, 'Hampers Lebaran B',         4, 'Hampers deluxe, isinya lengkap dan enak.'],
            // Jahit Bu Sri
            [1, 'Gamis Syari Polos Premium', 5, 'Gamis adem, jahitan rapi. Size pas.'],
            [1, 'Hijab Pashmina Instant',     4, 'Hijab instant, praktis buat anak muda.'],
            [3, 'Kebaya Jawa Modern',        5, 'Kebaya elegan, cocok buat kondangan.'],
            [0, 'Gamis Brokat Elegan',        5, 'Gamis brokat nya cantik banget, jahitan rapi.'],
            [4, 'Mukena Setrika Otomatis',    4, 'Mukena tipis ringan, tas carry nya bagus.'],
        ];

        foreach ($ratings as [$custIdx, $prodName, $ratingVal, $comment]) {
            $customerId = $customerIds[$custIdx] ?? null;
            $productId  = $productIds[$prodName] ?? null;
            if (!$customerId || !$productId) continue;

            $txnId = null;
            if (isset($prodToTxn[$productId]) && count($prodToTxn[$productId]) > 0) {
                $txnId = $prodToTxn[$productId][0];
            }

            Rating::firstOrCreate(
                [
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                    'transaction_id' => $txnId,
                ],
                [
                    'rating' => $ratingVal,
                    'comment' => $comment,
                ]
            );
        }
    }
}
