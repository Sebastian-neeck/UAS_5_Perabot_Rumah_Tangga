<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data in correct order (child tables first)
        \App\Models\Cart::truncate();
        \App\Models\OrderItem::truncate();
        \App\Models\Order::truncate();
        \App\Models\Furniture::truncate();
        \App\Models\Category::truncate();
        \App\Models\User::truncate();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ============ CREATE USERS ============
        $this->command->info('Creating users...');
        
        // Create Admin User
        \App\Models\User::create([
            'name' => 'Admin FurniStock',
            'email' => 'admin@furnistock.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1, Jakarta Selatan',
            'email_verified_at' => now()
        ]);

        // Create Regular Users
        \App\Models\User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'phone' => '081298765432',
            'address' => 'Jl. Contoh No. 123, Surabaya',
            'email_verified_at' => now()
        ]);

        \App\Models\User::create([
            'name' => 'Sari Dewi',
            'email' => 'sari@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'phone' => '081277788899',
            'address' => 'Jl. Mekar No. 45, Bandung',
            'email_verified_at' => now()
        ]);

        \App\Models\User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'phone' => '081355566677',
            'address' => 'Jl. Melati No. 78, Yogyakarta',
            'email_verified_at' => now()
        ]);

        // ============ CREATE CATEGORIES ============
        $this->command->info('Creating categories...');
        
        $categories = [
            ['name' => 'Ruang Tamu', 'icon' => 'sofa', 'description' => 'Sofa, meja tamu, dan dekorasi ruang tamu'],
            ['name' => 'Ruang Makan', 'icon' => 'utensils', 'description' => 'Meja makan, kursi makan, buffet'],
            ['name' => 'Kamar Tidur', 'icon' => 'bed', 'description' => 'Tempat tidur, lemari pakaian, meja rias'],
            ['name' => 'Ruang Kerja', 'icon' => 'monitor', 'description' => 'Meja kerja, kursi ergonomis, rak buku'],
            ['name' => 'Dekorasi', 'icon' => 'sparkles', 'description' => 'Lampu, vas, lukisan, aksesoris'],
            ['name' => 'Penyimpanan', 'icon' => 'package', 'description' => 'Rak, lemari, kabinet penyimpanan'],
            ['name' => 'Outdoor', 'icon' => 'tree-pine', 'description' => 'Furniture taman dan teras'],
            ['name' => 'Anak-anak', 'icon' => 'baby', 'description' => 'Furniture khusus untuk anak']
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }

        // ============ CREATE FURNITURE ITEMS ============
        $this->command->info('Creating furniture items...');
        
        // Furniture items dengan images sebagai URL Unsplash yang SESUAI dengan produk
        $furnitureItems = [
            // Ruang Tamu (Category 1) - SOFA & KURSI TAMU
            [
                'name' => 'Sofa Velvet Emerald 3 Seater',
                'description' => 'Sofa 3 seater dengan bahan velvet premium warna emerald, frame kayu jati solid, bantal busa high density. Nyaman dan elegan untuk ruang tamu Anda.',
                'price' => 4500000,
                'stock' => 12,
                'category_id' => 1,
                'images' => [
                    'https://media.istockphoto.com/id/1129333399/id/foto/desain-interior-untuk-ruang-tamu-atau-penerimaan-dengan-sofa-kursi-berlengan-menanam-lemari-di.webp?a=1&b=1&s=612x612&w=0&k=20&c=BNB1z0ZZK0yLOcTEaMPl7Qtsgbe399uW33RFJyVeceA=', // Sofa hijau
                      // Sofa dari samping
                ],
                'status' => 'available',
                'sku' => 'FUR-001'
            ],
            [
                'name' => 'Sofa Minimalis L-Shape',
                'description' => 'Sofa L-shape dengan desain minimalis, bahan katun premium, cocok untuk ruang tamu kecil. Tersedia dalam 3 warna pilihan.',
                'price' => 5200000,
                'stock' => 7,
                'category_id' => 1,
                'images' => [
                    'https://images.unsplash.com/photo-1759722665935-0967b4e0da93?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8U29mYSUyME1pbmltYWxpcyUyMEwtU2hhcGV8ZW58MHx8MHx8fDA%3D' // Sofa L-shape abu
                ],
                'status' => 'available',
                'sku' => 'FUR-002'
            ],
            [
                'name' => 'Meja Tamu Kayu Jati',
                'description' => 'Meja tamu minimalis dari kayu jati asli, finishing natural. Ukuran 120x60cm, dengan rak penyimpanan bawah.',
                'price' => 1850000,
                'stock' => 0,
                'category_id' => 1,
                'images' => [
                    'https://images.unsplash.com/photo-1732575966442-b2d665c080d2?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fG1lamElMjB0YW11JTIwa2F5dSUyMGphdGl8ZW58MHx8MHx8fDA%3D' // Meja tamu kayu
                ],
                'status' => 'out_of_stock',
                'sku' => 'FUR-003'
            ],
            [
                'name' => 'Kursi Tamu Scandinavian',
                'description' => 'Set 2 kursi tamu dengan desain Scandinavian, material kayu oak dan kain linen. Nyaman dan stylish.',
                'price' => 2800000,
                'stock' => 4,
                'category_id' => 1,
                'images' => [
                    'https://images.unsplash.com/photo-1758565204105-4085d36b8221?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8S3Vyc2klMjBUYW11JTIwU2NhbmRpbmF2aWFufGVufDB8fDB8fHww', // Kursi tamu coklat
                      // Kursi tamu putih
                ],
                'status' => 'low_stock',
                'sku' => 'FUR-004'
            ],

            // Ruang Makan (Category 2) - MEJA & KURSI MAKAN
            [
                'name' => 'Meja Makan Minimalis Jati 6 Kursi',
                'description' => 'Set meja makan minimalis dari kayu jati asli, termasuk 6 kursi dengan bantal dudukan nyaman. Cocok untuk keluarga 4-6 orang.',
                'price' => 8500000,
                'stock' => 8,
                'category_id' => 2,
                'images' => [
                    'https://plus.unsplash.com/premium_photo-1673214881759-4bd60b76acae?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8TWVqYSUyME1ha2FuJTIwTWluaW1hbGlzJTIwSmF0aSUyMDYlMjBLdXJzaXxlbnwwfHwwfHx8MA%3D%3D', // Meja makan kayu
                      // Set meja kursi makan
                ],
                'status' => 'available',
                'sku' => 'FUR-005'
            ],
            [
                'name' => 'Set Kursi Makan Scandinavian 4 Pcs',
                'description' => 'Set 4 kursi makan dengan desain Scandinavian, material kayu oak, bantal dudukan removable. Elegant dan nyaman.',
                'price' => 3200000,
                'stock' => 10,
                'category_id' => 2,
                'images' => [
                    'https://images.unsplash.com/photo-1624870701178-fcb7b1226f91?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjB8fFNldCUyMEt1cnNpJTIwTWFrYW4lMjBTY2FuZGluYXZpYW4lMjA0JTIwUGNzfGVufDB8fDB8fHww', // Kursi makan kayu
                     // Kursi makan modern
                ],
                'status' => 'available',
                'sku' => 'FUR-006'
            ],
            [
                'name' => 'Buffet Kayu Solid',
                'description' => 'Buffet penyimpanan dari kayu solid dengan 3 laci dan 2 pintu. Cocok untuk menyimpan peralatan makan.',
                'price' => 4200000,
                'stock' => 2,
                'category_id' => 2,
                'images' => [
                    'https://images.unsplash.com/photo-1656646523508-3a9f563fa853?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTZ8fEJ1ZmZldCUyMHBlbnlpbXBhbmFuJTIwZGFyaSUyMGtheXUlMjBzb2xpZCUyMGRlbmdhbiUyMDMlMjBsYWNpJTIwZGFuJTIwMiUyMHBpbnR1LiUyMENvY29rJTIwdW50dWslMjBtZW55aW1wYW4lMjBwZXJhbGF0YW4lMjBtYWthbnxlbnwwfHwwfHx8MA%3D%3D' // Kabinet penyimpanan
                ],
                'status' => 'low_stock',
                'sku' => 'FUR-007'
            ],

            // Kamar Tidur (Category 3) - TEMPAT TIDUR & LEMARI
            [
                'name' => 'Tempat Tidur King Size Minimalis',
                'description' => 'Tempat tidur king size dengan desain minimalis, headboard berlapis kain, frame kayu solid. Ukuran 200x180cm.',
                'price' => 6500000,
                'stock' => 5,
                'category_id' => 3,
                'images' => [
                    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=800&auto=format&fit=crop', // Tempat tidur modern
                      // Kamar tidur lengkap
                ],
                'status' => 'low_stock',
                'sku' => 'FUR-008'
            ],
            [
                'name' => 'Lemari Pakaian 3 Pintu',
                'description' => 'Lemari pakaian dengan 3 pintu, 2 laci, dan rak sepatu. Material MDF dengan finishing high gloss.',
                'price' => 3800000,
                'stock' => 6,
                'category_id' => 3,
                'images' => [
                    'https://plus.unsplash.com/premium_photo-1674815329029-6473c5a1b70f?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8TGVtYXJpJTIwcGFrYWlhbiUyMGRlbmdhbiUyMDMlMjBwaW50dSUyQyUyMDIlMjBsYWNpJTJDJTIwZGFuJTIwcmFrJTIwc2VwYXR1LiUyME1hdGVyaWFsJTIwTURGJTIwZGVuZ2FuJTIwZmluaXNoaW5nJTIwaGlnaCUyMGdsb3NzfGVufDB8fDB8fHww', // Lemari pakaian putih
                     // Lemari pakaian
                ],
                'status' => 'available',
                'sku' => 'FUR-009'
            ],
            [
                'name' => 'Meja Rias Minimalis',
                'description' => 'Meja rias dengan cermin besar dan 3 laci. Desain minimalis, cocok untuk kamar tidur modern.',
                'price' => 1750000,
                'stock' => 9,
                'category_id' => 3,
                'images' => [
                    'https://plus.unsplash.com/premium_photo-1675615649309-84e1c110fb0e?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8TWVqYSUyMHJpYXMlMjBkZW5nYW4lMjBjZXJtaW4lMjBiZXNhciUyMGRhbiUyMDMlMjBsYWNpLiUyMERlc2FpbiUyMG1pbmltYWxpcyUyQyUyMGNvY29rJTIwdW50dWslMjBrYW1hciUyMHRpZHVyJTIwbW9kZXJufGVufDB8fDB8fHww', // Meja rias dengan cermin
                     // Meja rias angle lain
                ],
                'status' => 'available',
                'sku' => 'FUR-010'
            ],

            // Ruang Kerja (Category 4) - MEJA & KURSI KERJA
            [
                'name' => 'Kursi Kerja Ergonomis Premium',
                'description' => 'Kursi kerja ergonomis dengan adjustable height, lumbar support, bahan mesh breathable. Mendukung postur tubuh yang baik.',
                'price' => 2100000,
                'stock' => 15,
                'category_id' => 4,
                'images' => [
                    'https://images.unsplash.com/photo-1688578735352-9a6f2ac3b70a?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8S3Vyc2klMjBrZXJqYSUyMGVyZ29ub21pcyUyMGRlbmdhbiUyMGFkanVzdGFibGUlMjBoZWlnaHQlMkMlMjBsdW1iYXIlMjBzdXBwb3J0JTJDJTIwYmFoYW4lMjBtZXNoJTIwYnJlYXRoYWJsZS4lMjBNZW5kdWt1bmclMjBwb3N0dXIlMjB0dWJ1aCUyMHlhbmclMjBiYWlrfGVufDB8fDB8fHww', // Kursi kerja mesh hitam
                      // Kursi kerja ergonomis
                ],
                'status' => 'available',
                'sku' => 'FUR-011'
            ],
            [
                'name' => 'Meja Kerja Minimalis',
                'description' => 'Meja kerja minimalis dengan rak buku terintegrasi. Material kayu MDF tebal, ukuran 140x60cm.',
                'price' => 1950000,
                'stock' => 8,
                'category_id' => 4,
                'images' => [
                    'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?q=80&w=800&auto=format&fit=crop', // Meja kerja kayu
                     // Meja kerja angle lain
                ],
                'status' => 'available',
                'sku' => 'FUR-012'
            ],
            [
                'name' => 'Rak Buku Minimalis 5 Susun',
                'description' => 'Rak buku minimalis dengan 5 susun, material MDF tebal, finishing natural wood. Cocok untuk home office.',
                'price' => 1250000,
                'stock' => 3,
                'category_id' => 4,
                'images' => [
                    'https://images.unsplash.com/photo-1604062527894-55b0712bbee3?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8UmFrJTIwYnVrdSUyMG1pbmltYWxpcyUyMGRlbmdhbiUyMDUlMjBzdXN1biUyQyUyMG1hdGVyaWFsJTIwTURGJTIwdGViYWwlMkMlMjBmaW5pc2hpbmclMjBuYXR1cmFsJTIwd29vZC4lMjBDb2NvayUyMHVudHVrJTIwaG9tZSUyMG9mZmljZXxlbnwwfHwwfHx8MA%3D%3D', // Rak buku kayu
                      // Rak buku minimalis
                ],
                'status' => 'low_stock',
                'sku' => 'FUR-013'
            ],

            // Dekorasi (Category 5) - LAMPU & VAS
            [
                'name' => 'Lampu Hias Gantung Modern',
                'description' => 'Lampu gantung modern dengan material metal dan kaca, cocok untuk ruang makan atau ruang tamu. 3 warna pilihan.',
                'price' => 750000,
                'stock' => 20,
                'category_id' => 5,
                'images' => [
                    'https://images.unsplash.com/photo-1764962171631-b7b831e80842?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', // Lampu gantung
                      // Lampu modern
                ],
                'status' => 'available',
                'sku' => 'FUR-014'
            ],
            [
                'name' => 'Vas Bunga Keramik',
                'description' => 'Vas bunga keramik dengan motif tradisional. Tinggi 40cm, diameter 25cm. Cocok untuk dekorasi meja.',
                'price' => 350000,
                'stock' => 25,
                'category_id' => 5,
                'images' => [
                    'https://images.unsplash.com/photo-1730708526003-e5f4f2dfc191?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8dmFzJTIwYnVuZ2ElMjBrZXJhbWlrfGVufDB8fDB8fHww', // Vas bunga keramik
                     // Vas bunga angle lain
                ],
                'status' => 'available',
                'sku' => 'FUR-015'
            ],
            [
                'name' => 'Lampu Meja LED',
                'description' => 'Lampu meja LED dengan adjustable brightness dan color temperature. Charging USB, baterai tahan 8 jam.',
                'price' => 450000,
                'stock' => 0,
                'category_id' => 5,
                'images' => [
                    'https://images.unsplash.com/photo-1540932239986-30128078f3c5?w=800&auto=format&fit=crop', // Lampu meja modern
                    'https://images.unsplash.com/photo-1540932239986-30128078f3c5?w=800&auto=format&fit=crop&ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=60' // Lampu meja angle lain
                ],
                'status' => 'out_of_stock',
                'sku' => 'FUR-016'
            ],

            // Penyimpanan (Category 6) - RAK & LEMARI PENYIMPANAN
            [
                'name' => 'Rak Sepatu Kayu',
                'description' => 'Rak sepatu dengan 4 tingkat, material kayu solid. Kapasitas 12-16 pasang sepatu.',
                'price' => 850000,
                'stock' => 12,
                'category_id' => 6,
                'images' => [
                    'https://media.istockphoto.com/id/2227390662/id/foto/lemari-sepatu-putih-di-dalam-ruangan.webp?a=1&b=1&s=612x612&w=0&k=20&c=2N5OLkXYofiNq1S8AJrTHCggyzUsdwmEz5FW6OSdJ0U=', // Rak sepatu kayu
                    // Rak sepatu angle lain
                ],
                'status' => 'available',
                'sku' => 'FUR-017'
            ],
            [
                'name' => 'Lemari Kotak Multifungsi',
                'description' => 'Lemari kotak dengan 9 kompartemen, bisa untuk menyimpan mainan, buku, atau barang lainnya.',
                'price' => 950000,
                'stock' => 6,
                'category_id' => 6,
                'images' => [
                    'https://plus.unsplash.com/premium_photo-1724155541135-93b3f5519b22?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8TGVtYXJpJTIwa290YWslMjBkZW5nYW4lMjA5JTIwa29tcGFydGVtZW4lMkMlMjBiaXNhJTIwdW50dWslMjBtZW55aW1wYW4lMjBtYWluYW4lMkMlMjBidWt1JTJDJTIwYXRhdSUyMGJhcmFuZyUyMGxhaW5ueWF8ZW58MHx8MHx8fDA%3D', // Lemari kotak
                     // Lemari penyimpanan
                ],
                'status' => 'available',
                'sku' => 'FUR-018'
            ],

            // Outdoor (Category 7) - KURSI TAMAN
            [
                'name' => 'Kursi Taman Rotan',
                'description' => 'Set 2 kursi taman dari rotan sintetis, tahan cuaca. Cocok untuk teras atau taman kecil.',
                'price' => 1850000,
                'stock' => 8,
                'category_id' => 7,
                'images' => [
                    'https://images.unsplash.com/photo-1760382739174-0aef6957788f?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8U2V0JTIwMiUyMGt1cnNpJTIwdGFtYW4lMjBkYXJpJTIwcm90YW4lMjBzaW50ZXRpcyUyQyUyMHRhaGFuJTIwY3VhY2EuJTIwQ29jb2slMjB1bnR1ayUyMHRlcmFzJTIwYXRhdSUyMHRhbWFuJTIwa2VjaWx8ZW58MHx8MHx8fDA%3D', // Kursi taman rotan
                      // Set kursi taman
                ],
                'status' => 'available',
                'sku' => 'FUR-019'
            ],

            // Anak-anak (Category 8) - TEMPAT TIDUR ANAK
            [
                'name' => 'Tempat Tidur Anak',
                'description' => 'Tempat tidur anak dengan safety rail, ukuran 160x80cm. Desain lucu dengan motif kartun.',
                'price' => 3200000,
                'stock' => 4,
                'category_id' => 8,
                'images' => [
                    'https://images.unsplash.com/photo-1699799462235-53a0ca5a7a43?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTZ8fHRlbXBhdCUyMHRpZHVyJTIwYW5ha3xlbnwwfHwwfHx8MA%3D%3D', // Tempat tidur anak
                      // Kamar anak lengkap
                ],
                'status' => 'low_stock',
                'sku' => 'FUR-020'
            ]
        ];

        foreach ($furnitureItems as $item) {
            \App\Models\Furniture::create($item);
        }

        // ============ CREATE SAMPLE ORDERS ============
        $this->command->info('Creating sample orders...');
        
        // Order 1 - Budi (completed/delivered)
        $order1 = \App\Models\Order::create([
            'user_id' => 2,
            'order_code' => 'ORD-' . date('Ymd') . '-0001',
            'total_amount' => 6000000,
            'status' => 'delivered',
            'shipping_address' => 'Jl. Contoh No. 123, RT 01/RW 02, Surabaya, Jawa Timur 60245',
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081298765432',
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'bank_name' => 'BCA',
            'notes' => 'Tolong dikirim sebelum jam 5 sore',
            'created_at' => Carbon::now()->subDays(15)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order1->id,
            'furniture_id' => 1,  // Sofa Velvet Emerald
            'quantity' => 1,
            'unit_price' => 4500000,
            'subtotal' => 4500000
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order1->id,
            'furniture_id' => 14, // Lampu Hias Gantung Modern
            'quantity' => 2,
            'unit_price' => 750000,
            'subtotal' => 1500000
        ]);

        // Order 2 - Sari (processing)
        $order2 = \App\Models\Order::create([
            'user_id' => 3,
            'order_code' => 'ORD-' . date('Ymd') . '-0002',
            'total_amount' => 6100000,
            'status' => 'processing',
            'shipping_address' => 'Jl. Mekar No. 45, Bandung, Jawa Barat 40135',
            'customer_name' => 'Sari Dewi',
            'customer_phone' => '081277788899',
            'payment_method' => 'e_wallet',
            'payment_status' => 'paid',
            'wallet_type' => 'OVO',
            'notes' => 'Kirim ke alamat kantor, resepsionis',
            'created_at' => Carbon::now()->subDays(3)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order2->id,
            'furniture_id' => 11,  // Kursi Kerja Ergonomis
            'quantity' => 1,
            'unit_price' => 2100000,
            'subtotal' => 2100000
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order2->id,
            'furniture_id' => 6,   // Set Kursi Makan Scandinavian
            'quantity' => 1,
            'unit_price' => 3200000,
            'subtotal' => 3200000
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order2->id,
            'furniture_id' => 14, // Lampu Hias Gantung Modern
            'quantity' => 1,
            'unit_price' => 750000,
            'subtotal' => 750000
        ]);

        // Order 3 - Budi (pending/waiting payment)
        $order3 = \App\Models\Order::create([
            'user_id' => 2,
            'order_code' => 'ORD-' . date('Ymd') . '-0003',
            'total_amount' => 1250000,
            'status' => 'pending',
            'shipping_address' => 'Jl. Contoh No. 123, Surabaya',
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081298765432',
            'payment_method' => 'bank_transfer',
            'payment_status' => 'waiting_payment',
            'bank_name' => 'Mandiri',
            'notes' => 'Stok terakhir ya',
            'created_at' => Carbon::now()->subDays(1)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order3->id,
            'furniture_id' => 13, // Rak Buku Minimalis
            'quantity' => 1,
            'unit_price' => 1250000,
            'subtotal' => 1250000
        ]);

        // Order 4 - Ahmad (shipped)
        $order4 = \App\Models\Order::create([
            'user_id' => 4,
            'order_code' => 'ORD-' . date('Ymd') . '-0004',
            'total_amount' => 5200000,
            'status' => 'shipped',
            'shipping_address' => 'Jl. Melati No. 78, Yogyakarta 55281',
            'customer_name' => 'Ahmad Fauzi',
            'customer_phone' => '081355566677',
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'bank_name' => 'BRI',
            'notes' => 'Pakai kurir yang cepat',
            'created_at' => Carbon::now()->subDays(5)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order4->id,
            'furniture_id' => 2,  // Sofa Minimalis L-Shape
            'quantity' => 1,
            'unit_price' => 5200000,
            'subtotal' => 5200000
        ]);

        // Order 5 - Sari (cancelled)
        $order5 = \App\Models\Order::create([
            'user_id' => 3,
            'order_code' => 'ORD-' . date('Ymd') . '-0005',
            'total_amount' => 2800000,
            'status' => 'cancelled',
            'shipping_address' => 'Jl. Mekar No. 45, Bandung',
            'customer_name' => 'Sari Dewi',
            'customer_phone' => '081277788899',
            'payment_method' => 'e_wallet',
            'payment_status' => 'failed',
            'wallet_type' => 'DANA',
            'notes' => 'Mengubah rencana pembelian',
            'created_at' => Carbon::now()->subDays(10)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order5->id,
            'furniture_id' => 4,  // Kursi Tamu Scandinavian
            'quantity' => 1,
            'unit_price' => 2800000,
            'subtotal' => 2800000
        ]);

        // Order 6 - Admin (pending verification)
        $order6 = \App\Models\Order::create([
            'user_id' => 1,
            'order_code' => 'ORD-' . date('Ymd') . '-0006',
            'total_amount' => 950000,
            'status' => 'pending',
            'shipping_address' => 'Jl. Admin No. 1, Jakarta Selatan',
            'customer_name' => 'Admin FurniStock',
            'customer_phone' => '081234567890',
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending_verification',
            'bank_name' => 'BNI',
            'notes' => 'Untuk keperluan testing',
            'created_at' => Carbon::now()->subHours(3)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order6->id,
            'furniture_id' => 17, // Rak Sepatu Kayu
            'quantity' => 1,
            'unit_price' => 850000,
            'subtotal' => 850000
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order6->id,
            'furniture_id' => 15, // Vas Bunga Keramik
            'quantity' => 1,
            'unit_price' => 350000,
            'subtotal' => 350000
        ]);

        // Order 7 - Ahmad (expired)
        $order7 = \App\Models\Order::create([
            'user_id' => 4,
            'order_code' => 'ORD-' . date('Ymd') . '-0007',
            'total_amount' => 1950000,
            'status' => 'cancelled',
            'shipping_address' => 'Jl. Melati No. 78, Yogyakarta',
            'customer_name' => 'Ahmad Fauzi',
            'customer_phone' => '081355566677',
            'payment_method' => 'e_wallet',
            'payment_status' => 'expired',
            'wallet_type' => 'GoPay',
            'notes' => 'Kadaluarsa karena tidak dibayar',
            'created_at' => Carbon::now()->subDays(25)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order7->id,
            'furniture_id' => 12, // Meja Kerja Minimalis
            'quantity' => 1,
            'unit_price' => 1950000,
            'subtotal' => 1950000
        ]);

        // ============ CREATE PAYMENT RECORDS ============
        $this->command->info('Creating payment records...');
        
        // Payment for Order 1 (paid)
        \App\Models\Payment::create([
            'payment_code' => 'PAY-' . date('Ymd') . '-0001',
            'order_id' => $order1->id,
            'method' => 'bank_transfer',
            'bank_name' => 'BCA',
            'amount' => 6000000,
            'status' => 'paid',
            'paid_at' => Carbon::now()->subDays(14),
            'expired_at' => Carbon::now()->subDays(14)->addHours(24)
        ]);

        // Payment for Order 2 (paid)
        \App\Models\Payment::create([
            'payment_code' => 'PAY-' . date('Ymd') . '-0002',
            'order_id' => $order2->id,
            'method' => 'e_wallet',
            'wallet_type' => 'OVO',
            'amount' => 6100000,
            'status' => 'paid',
            'paid_at' => Carbon::now()->subDays(3),
            'expired_at' => Carbon::now()->subDays(3)->addHours(24)
        ]);

        // Payment for Order 3 (waiting payment)
        \App\Models\Payment::create([
            'payment_code' => 'PAY-' . date('Ymd') . '-0003',
            'order_id' => $order3->id,
            'method' => 'bank_transfer',
            'bank_name' => 'Mandiri',
            'amount' => 1250000,
            'status' => 'waiting_payment',
            'expired_at' => Carbon::now()->addHours(20) // 20 jam lagi expired
        ]);

        // Payment for Order 4 (paid)
        \App\Models\Payment::create([
            'payment_code' => 'PAY-' . date('Ymd') . '-0004',
            'order_id' => $order4->id,
            'method' => 'bank_transfer',
            'bank_name' => 'BRI',
            'amount' => 5200000,
            'status' => 'paid',
            'paid_at' => Carbon::now()->subDays(4),
            'expired_at' => Carbon::now()->subDays(4)->addHours(24)
        ]);

        // Payment for Order 5 (failed)
        \App\Models\Payment::create([
            'payment_code' => 'PAY-' . date('Ymd') . '-0005',
            'order_id' => $order5->id,
            'method' => 'e_wallet',
            'wallet_type' => 'DANA',
            'amount' => 2800000,
            'status' => 'failed',
            'expired_at' => Carbon::now()->subDays(9)->addHours(24),
            'notes' => 'Pelanggan membatalkan pembayaran'
        ]);

        // Payment for Order 6 (pending verification)
        \App\Models\Payment::create([
            'payment_code' => 'PAY-' . date('Ymd') . '-0006',
            'order_id' => $order6->id,
            'method' => 'bank_transfer',
            'bank_name' => 'BNI',
            'amount' => 950000,
            'status' => 'pending_verification',
            'payment_proof' => 'payment-proof-1.jpg',
            'expired_at' => Carbon::now()->addHours(21)
        ]);

        // Payment for Order 7 (expired)
        \App\Models\Payment::create([
            'payment_code' => 'PAY-' . date('Ymd') . '-0007',
            'order_id' => $order7->id,
            'method' => 'e_wallet',
            'wallet_type' => 'GoPay',
            'amount' => 1950000,
            'status' => 'expired',
            'expired_at' => Carbon::now()->subDays(24)
        ]);

        // ============ CREATE SAMPLE CART ITEMS ============
        $this->command->info('Creating sample cart items...');
        
        // Budi's cart
        \App\Models\Cart::create([
            'user_id' => 2,
            'furniture_id' => 6,   // Set Kursi Makan Scandinavian
            'quantity' => 2
        ]);

        \App\Models\Cart::create([
            'user_id' => 2,
            'furniture_id' => 14,  // Lampu Hias
            'quantity' => 1
        ]);

        \App\Models\Cart::create([
            'user_id' => 2,
            'furniture_id' => 9,   // Lemari Pakaian
            'quantity' => 1
        ]);

        // Sari's cart
        \App\Models\Cart::create([
            'user_id' => 3,
            'furniture_id' => 5,   // Meja Makan Jati
            'quantity' => 1
        ]);

        \App\Models\Cart::create([
            'user_id' => 3,
            'furniture_id' => 10,  // Meja Rias Minimalis
            'quantity' => 1
        ]);

        // Ahmad's cart
        \App\Models\Cart::create([
            'user_id' => 4,
            'furniture_id' => 8,   // Tempat Tidur King
            'quantity' => 1
        ]);

        \App\Models\Cart::create([
            'user_id' => 4,
            'furniture_id' => 19,  // Kursi Taman Rotan
            'quantity' => 2
        ]);

        // Guest cart (session)
        \App\Models\Cart::create([
            'session_id' => 'guest_session_123',
            'furniture_id' => 3,   // Meja Tamu Kayu Jati
            'quantity' => 1
        ]);

        \App\Models\Cart::create([
            'session_id' => 'guest_session_123',
            'furniture_id' => 15,  // Vas Bunga
            'quantity' => 3
        ]);

        \App\Models\Cart::create([
            'session_id' => 'guest_session_123',
            'furniture_id' => 18,  // Lemari Kotak Multifungsi
            'quantity' => 1
        ]);

        // ============ FINAL MESSAGE ============
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📊 STATISTICS:');
        $this->command->info('   👥 Users: 4 (1 admin, 3 customers)');
        $this->command->info('   📦 Categories: 8');
        $this->command->info('   🛋️  Furniture: ' . count($furnitureItems) . ' items');
        $this->command->info('   🛒 Orders: 7 (with all status types)');
        $this->command->info('   💳 Payments: 7 records');
        $this->command->info('   🛍️  Cart items: 9 (3 users, 1 guest)');
        $this->command->info('');
        $this->command->info('🔐 LOGIN CREDENTIALS:');
        $this->command->info('   👑 Admin: admin@furnistock.com / password123');
        $this->command->info('   👤 Customer 1: budi@example.com / password123');
        $this->command->info('   👤 Customer 2: sari@example.com / password123');
        $this->command->info('   👤 Customer 3: ahmad@example.com / password123');
        $this->command->info('');
        $this->command->info('🚀 Dashboard URL: http://127.0.0.1:8000/admin/dashboard');
        $this->command->info('🛍️  Catalog URL: http://127.0.0.1:8000');
        $this->command->info('💰 Checkout Test: Add items to cart → Checkout → Use payment method');
        $this->command->info('');
        $this->command->info('⚠️  IMPORTANT: For testing payment verification:');
        $this->command->info('   1. Login as admin: admin@furnistock.com');
        $this->command->info('   2. Go to Orders → Find "Pending Verification"');
        $this->command->info('   3. Verify or reject payment proof');       
    }
}