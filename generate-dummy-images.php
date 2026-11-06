<?php
/**
 * Generate placeholder images for dummy stores, banners, and products.
 * Run: php generate-dummy-images.php
 */

$base = __DIR__ . '/storage/app/public';
@mkdir($base . '/logos', 0777, true);
@mkdir($base . '/store-banners', 0777, true);
@mkdir($base . '/product-imgs', 0777, true);

// ── Helpers ────────────────────────────────────────────────────────────────

function hexToRgb(string $hex): array
{
    $hex = ltrim($hex, '#');
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2)),
    ];
}

function mixColors(array $c1, array $c2, float $t): array
{
    return [
        'r' => (int)($c1['r'] + ($c2['r'] - $c1['r']) * $t),
        'g' => (int)($c1['g'] + ($c2['g'] - $c1['g']) * $t),
        'b' => (int)($c1['b'] + ($c2['b'] - $c1['b']) * $t),
    ];
}

function contrastTextColor(array $bg): array
{
    $lum = (0.299 * $bg['r'] + 0.587 * $bg['g'] + 0.114 * $bg['b']) / 255;
    return $lum > 0.5
        ? ['r' => 0,   'g' => 0,   'b' => 0  ]
        : ['r' => 255, 'g' => 255, 'b' => 255];
}

function findFont(): string
{
    foreach (
        ['/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
         '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
         '/usr/share/fonts/truetype/noto/NotoSans-Regular.ttf',
         'C:\\Windows\\Fonts\\arial.ttf'] as $f
    ) {
        if (file_exists($f)) return $f;
    }
    return '';
}

/**
 * Draw a horizontal gradient image with a text label in the centre.
 */
function renderImage(
    string $path,
    array $palette,
    int $width = 800,
    int $height = 400,
    string $format = 'jpg',
    int $quality = 85,
    ?string $label = null
): void {
    $img = imagecreatetruecolor($width, $height);

    $c1 = hexToRgb($palette[0]);
    $c2 = hexToRgb($palette[1] ?? $palette[0]);
    $c3 = hexToRgb($palette[2] ?? $palette[1] ?? $palette[0]);
    $c4 = hexToRgb($palette[3] ?? $palette[2] ?? $palette[1] ?? $palette[0]);

    for ($y = 0; $y < $height; $y++) {
        $ratio = $y / $height;
        if ($ratio < 0.5) {
            $col = mixColors($c1, $c2, $ratio * 2);
        } else {
            $col = mixColors($c3, $c4, ($ratio - 0.5) * 2);
        }
        $color = imagecolorallocate($img, $col['r'], $col['g'], $col['b']);
        imageline($img, 0, $y, $width - 1, $y, $color);
    }

    // Text label
    if ($label === null) {
        $label = basename($path);
        $label = preg_replace('/\.\w+$/', '', $label);
        $label = str_replace(['-', '_'], ' ', ucfirst($label));
    }

    $textCol = contrastTextColor($c1);
    $textColor = imagecolorallocate($img, $textCol['r'], $textCol['g'], $textCol['b']);

    $fontSize = max(14, (int)($height / 18));
    $font = findFont();

    if ($font !== '' && function_exists('imagettftext')) {
        $bbox = imagettfbbox($fontSize, 0, $font, $label);
        $txtW = $bbox[2] - $bbox[0];
        $txtH = $bbox[1] - $bbox[7];
        $tx = (int)(($width - $txtW) / 2);
        $ty = (int)(($height + $txtH) / 2) - 4;
        imagettftext($img, $fontSize, 0, $tx, $ty, $textColor, $font, $label);
    } else {
        $cw = imagestringwidth($textColor, 5, $label);
        $ch = imagefontheight(5);
        imagestring($img, 5, (int)(($width - $cw) / 2), (int)(($height - $ch) / 2), $label, $textColor);
    }

    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0777, true);
    }

    match ($format) {
        'png' => imagepng($img, $path, 8),
        default => imagejpeg($img, $path, $quality),
    };
    imagedestroy($img);
}

// ── Palettes ───────────────────────────────────────────────────────────────

$storePalettes = [
    'gadget-hub'          => ['#1a1a2e', '#16213e', '#0f3460', '#e94560'],
    'fashion-nusantara'   => ['#2d1b69', '#6b2fa0', '#c77dff', '#e0aaff'],
    'kuliner-padang'      => ['#b5179e', '#7b2cbf', '#4a0e4e', '#f72585'],
    'home-living'         => ['#264653', '#2a9d8f', '#e9c46a', '#f4a261'],
    'olahraga-center'     => ['#6a040f', '#d00000', '#e85d04', '#ffb703'],
    'beauty-corner'       => ['#ffbedd', '#fff181', '#a5f3fc', '#ff7eb3'],
];

$productPalettes = [
    'smartphone-x-pro'           => ['#1a1a2e', '#0f3460', '#e94560', '#533483'],
    'laptop-ultra-slim'          => ['#2d3436', '#636e72', '#b2bec3', '#dfe6e9'],
    'earbuds-wireless-tws'       => ['#0f3460', '#16213e', '#e94560', '#533483'],
    'power-bank-20000mah'        => ['#264653', '#2a9d8f', '#e9c46a', '#f4a261'],
    'gamis-syar-i-premium'       => ['#2d1b69', '#6b2fa0', '#c77dff', '#e0aaff'],
    'kemeja-casual-pria'         => ['#1b4332', '#2d6a4f', '#52b788', '#95d5b2'],
    'hijab-segi-empat-polos'     => ['#7b2cbf', '#c77dff', '#e0aaff', '#ffbedd'],
    'sneakers-pria-white'        => ['#f8f9fa', '#dee2e6', '#adb5bd', '#6c757d'],
    'rendang-daging-sapi-500gr'  => ['#7f4924', '#936639', '#a67c52', '#b8956a'],
    'ayam-pop-500gr'             => ['#fff3b0', '#ffe066', '#ffd93d', '#ffcc00'],
    'dendeng-balado-250gr'       => ['#d00000', '#e85d04', '#ff7800', '#ff9500'],
    'sambal-rempah-padang-200gr' => ['#9d0208', '#d00000', '#e85d04', '#ff7800'],
    'meja-tamu-minimalis'        => ['#6a493c', '#8b6f4e', '#b08968', '#ddb892'],
    'rak-buku-5-tingkat'         => ['#6a493c', '#8b6f4e', '#b08968', '#ddb892'],
    'lampu-hias-led-dekorasi'    => ['#ffd60a', '#ffea00', '#e5ff00', '#c9ff00'],
    'sepatu-lari-aeromax'        => ['#ff595e', '#ff7c43', '#ffca3a', '#8ac926'],
    'jersey-latihan-pro'         => ['#1982c4', '#18aabf', '#17c3a2', '#0bfeb0'],
    'dumbbell-5kg-pair'          => ['#363636', '#575757', '#797979', '#9c9c9c'],
    'serum-wajah-glow-up'        => ['#ffc8dd', '#ffafcc', '#f49ac2', '#e051c1'],
    'sunscreen-spf-50'           => ['#fff176', '#ffee5e', '#ffeb3b', '#fdd835'],
    'lipstik-matte-velvet'       => ['#c9184a', '#a91764', '#832559', '#5c2557'],
];

// ── Generate Store Logos ───────────────────────────────────────────────────

$logos = [
    ['logos/gadget-hub.jpg',             'gadget-hub',          'Gadget Hub Jakarta'],
    ['logos/fashion-nusantara.jpg',      'fashion-nusantara',   'Fashion Nusantara'],
    ['logos/kuliner-padang.jpg',         'kuliner-padang',      'Kuliner Padang Raya'],
    ['logos/home-living.jpg',            'home-living',         'Home & Living Store'],
    ['logos/olahraga-center.jpg',        'olahraga-center',     'Olahraga Center'],
    ['logos/beauty-corner.jpg',          'beauty-corner',       'Beauty Corner'],
];

foreach ($logos as [$rel, $key, $label]) {
    $path = $base . '/' . $rel;
    $pal = $storePalettes[$key] ?? ['#666', '#999', '#ccc', '#fff'];
    renderImage($path, $pal, 800, 400, 'jpg', 85, $label);
    echo "✓ $rel\n";
}

// ── Generate Store Banners ─────────────────────────────────────────────────

$banners = [
    ['store-banners/gadget-hub-1.jpg',      'gadget-hub',    'Gadget Hub Jakarta'],
    ['store-banners/gadget-hub-2.jpg',      'gadget-hub',    'Gadget Hub Jakarta — Promo'],
    ['store-banners/gadget-hub-3.jpg',      'gadget-hub',    'Gadget Hub Jakarta — Sale'],
    ['store-banners/fashion-nusantara-1.jpg','fashion-nusantara','Fashion Nusantara'],
    ['store-banners/fashion-nusantara-2.jpg','fashion-nusantara','Fashion Nusantara — Koleksi Baru'],
    ['store-banners/kuliner-padang-1.jpg',  'kuliner-padang','Kuliner Padang Raya'],
    ['store-banners/kuliner-padang-2.jpg',  'kuliner-padang','Kuliner Padang Raya — Spesial'],
    ['store-banners/kuliner-padang-3.jpg',  'kuliner-padang','Kuliner Padang Raya — Paket Keluarga'],
    ['store-banners/home-living-1.jpg',     'home-living',   'Home & Living Store'],
    ['store-banners/home-living-2.jpg',     'home-living',   'Home & Living Store — Diskon'],
    ['store-banners/olahraga-center-1.jpg', 'olahraga-center','Olahraga Center'],
    ['store-banners/beauty-corner-1.jpg',   'beauty-corner', 'Beauty Corner'],
    ['store-banners/beauty-corner-2.jpg',   'beauty-corner', 'Beauty Corner — New Arrivals'],
];

foreach ($banners as [$rel, $key, $label]) {
    $path = $base . '/' . $rel;
    $pal = $storePalettes[$key] ?? ['#666', '#999', '#ccc', '#fff'];
    renderImage($path, $pal, 1600, 500, 'jpg', 85, $label);
    echo "✓ $rel\n";
}

// ── Generate Product Images ───────────────────────────────────────────────

$productImages = [
    ['product-imgs/smartphone-x-pro-128gb-main.jpg',       'smartphone-x-pro',           'Smartphone X Pro 128GB'],
    ['product-imgs/smartphone-x-pro-128gb-2.jpg',          'smartphone-x-pro',           'Smartphone X Pro 128GB — Side View'],
    ['product-imgs/laptop-ultra-slim-14-main.jpg',         'laptop-ultra-slim',          'Laptop Ultra Slim 14 inchi'],
    ['product-imgs/laptop-ultra-slim-14-2.jpg',            'laptop-ultra-slim',          'Laptop Ultra Slim 14 inchi — Open'],
    ['product-imgs/earbuds-wireless-tws-main.jpg',         'earbuds-wireless-tws',       'Earbuds Wireless TWS'],
    ['product-imgs/earbuds-wireless-tws-2.jpg',            'earbuds-wireless-tws',       'Earbuds Wireless TWS — Case'],
    ['product-imgs/power-bank-20000mah-main.jpg',          'power-bank-20000mah',        'Power Bank 20000mAh'],
    ['product-imgs/power-bank-20000mah-2.jpg',             'power-bank-20000mah',        'Power Bank 20000mAh — Charging'],
    ["product-imgs/gamis-syar-i-premium-main.jpg",         'gamis-syar-i-premium',       "Gamis Syar'i Premium"],
    ["product-imgs/gamis-syar-i-premium-2.jpg",            'gamis-syar-i-premium',       "Gamis Syar'i Premium — Detail"],
    ["product-imgs/gamis-syar-i-premium-3.jpg",            'gamis-syar-i-premium',       "Gamis Syar'i Premium — Back View"],
    ['product-imgs/kemeja-casual-pria-main.jpg',           'kemeja-casual-pria',         'Kemeja Casual Pria'],
    ['product-imgs/kemeja-casual-pria-2.jpg',              'kemeja-casual-pria',         'Kemeja Casual Pria — Folded'],
    ['product-imgs/kemeja-casual-pria-3.jpg',              'kemeja-casual-pria',         'Kemeja Casual Pria — Detail'],
    ['product-imgs/hijab-segi-empat-polos-main.jpg',       'hijab-segi-empat-polos',     'Hijab Segi Empat Polos'],
    ['product-imgs/sneakers-pria-white-main.jpg',          'sneakers-pria-white',        'Sneakers Pria White'],
    ['product-imgs/sneakers-pria-white-2.jpg',             'sneakers-pria-white',        'Sneakers Pria White — Sole'],
    ['product-imgs/rendang-daging-sapi-500gr-main.jpg',    'rendang-daging-sapi-500gr',  'Rendang Daging Sapi 500gr'],
    ['product-imgs/rendang-daging-sapi-500gr-2.jpg',       'rendang-daging-sapi-500gr',  'Rendang Daging Sapi — Close Up'],
    ['product-imgs/rendang-daging-sapi-500gr-3.jpg',       'rendang-daging-sapi-500gr',  'Rendang Daging Sapi — Packaging'],
    ['product-imgs/ayam-pop-500gr-main.jpg',               'ayam-pop-500gr',             'Ayam Pop 500gr'],
    ['product-imgs/ayam-pop-500gr-2.jpg',                  'ayam-pop-500gr',             'Ayam Pop — Close Up'],
    ['product-imgs/ayam-pop-500gr-3.jpg',                  'ayam-pop-500gr',             'Ayam Pop — Packaging'],
    ['product-imgs/dendeng-balado-250gr-main.jpg',         'dendeng-balado-250gr',       'Dendeng Balado 250gr'],
    ['product-imgs/sambal-rempah-padang-200gr-main.jpg',   'sambal-rempah-padang-200gr', 'Sambal Rempah Padang 200gr'],
    ['product-imgs/meja-tamu-minimalis-main.jpg',          'meja-tamu-minimalis',        'Meja Tamu Minimalis'],
    ['product-imgs/rak-buku-5-tingkat-main.jpg',           'rak-buku-5-tingkat',         'Rak Buku 5 Tingkat'],
    ['product-imgs/lampu-hias-led-dekorasi-main.jpg',      'lampu-hias-led-dekorasi',    'Lampu Hias LED Dekorasi'],
    ['product-imgs/sepatu-lari-aeromax-main.jpg',          'sepatu-lari-aeromax',        'Sepatu Lari AeroMax'],
    ['product-imgs/sepatu-lari-aeromax-2.jpg',             'sepatu-lari-aeromax',        'Sepatu Lari AeroMax — Side'],
    ['product-imgs/jersey-latihan-pro-main.jpg',           'jersey-latihan-pro',         'Jersey Latihan Pro'],
    ['product-imgs/dumbbell-5kg-pair-main.jpg',            'dumbbell-5kg-pair',          'Dumbbell 5kg Pair'],
    ['product-imgs/serum-wajah-glow-up-main.jpg',          'serum-wajah-glow-up',        'Serum Wajah Glow Up'],
    ['product-imgs/serum-wajah-glow-up-2.jpg',             'serum-wajah-glow-up',        'Serum Wajah Glow Up — Texture'],
    ['product-imgs/sunscreen-spf-50+-main.jpg',            'sunscreen-spf-50',           'Sunscreen SPF 50+'],
    ['product-imgs/sunscreen-spf-50+-2.jpg',               'sunscreen-spf-50',           'Sunscreen SPF 50+ — Application'],
    ['product-imgs/lipstik-matte-velvet-main.jpg',         'lipstik-matte-velvet',       'Lipstik Matte Velvet'],
];

foreach ($productImages as [$rel, $key, $label]) {
    $path = $base . '/' . $rel;
    $pal = $productPalettes[$key] ?? ['#666666', '#999999', '#cccccc', '#ffffff'];
    renderImage($path, $pal, 800, 600, 'jpg', 85, $label);
    echo "✓ $rel\n";
}

echo "\n✅ Semua gambar dummy selesai dibuat di storage/app/public/\n";
