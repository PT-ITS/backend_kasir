<?php

namespace Database\Seeders;

use App\Models\CatatanStock;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Langganan;
use App\Models\Invoice;
use App\Models\Kasir;
use App\Models\Manager;
use App\Models\Product;
use App\Models\TambahStock;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Owner
        User::create([
            'name' => 'owner',
            'email' => 'owner@gmail.com',
            'level' => '0',
            'status' => '1',
            'password' => Hash::make('12345'),
        ]);

        // Manager
        User::create([
            'name' => 'manager',
            'email' => 'manager@gmail.com',
            'level' => '1',
            'status' => '1',
            'password' => Hash::make('12345'),
        ]);
        Manager::create([
            'nama_manager' => 'Nama Manager',
            'hp_manager' => '081',
            'alamat_manager' => 'Alamat Manager',
            'fk_id_user' => '2',
        ]);

        // Toko
        Toko::create([
            'nama_toko' => 'Nama Toko',
            'hp_toko' => '081',
            'alamat_toko' => 'Alamat Toko',
            'fk_id_manager' => '2',
        ]);

        // Kasir
        User::create([
            'name' => 'kasir',
            'email' => 'kasir@gmail.com',
            'level' => '2',
            'status' => '1',
            'password' => Hash::make('12345'),
        ]);
        Kasir::create([
            'nama_kasir' => 'Nama Kasir',
            'hp_kasir' => '081',
            'alamat_kasir' => 'Alamat Kasir',
            'fk_id_toko' => '1',
            'fk_id_user' => '3',
        ]);

        // Produk
        $namaProduks = [
            'Indomie Goreng',
            'Kopi ABC',
            'Teh Gelas',
            'Aqua 600ml',
            'Susu Bendera',
            'Roti Tawar',
            'Gula Pasir',
            'Minyak Goreng',
            'Sabun Lifebuoy',
            'Sikat Gigi',
            'Shampoo Sunsilk',
            'Sarden ABC',
            'Kecap Bango',
            'Saos Sambal Indofood',
            'Telur Ayam',
            'Mie Sedap',
            'Kopi Kapal Api',
            'Susu Dancow',
            'Rokok Gudang Garam',
            'Snack Chitato',
            'Energen',
            'Pop Mie',
            'Garam Dapur',
            'Tissue Paseo',
            'Baterai ABC',
            'Air Mineral Club',
            'Kornet Pronas',
            'Bubuk Coklat',
            'Teh Celup Sariwangi',
            'Kopi Good Day',
            'Minuman Pocari',
            'Minyak Telon',
            'Makanan Kucing',
            'Sabun Cuci Sunlight',
            'Minyak Kayu Putih',
            'Cap Badak',
        ];

        for ($i = 0; $i < 100; $i++) {
            $nama = $namaProduks[array_rand($namaProduks)] . ' ' . Str::random(3);

            // 1. Create Product
            $product = Product::create([
                'nama_product' => $nama,
                'stock_product' => rand(10, 100),
                'harga_jual' => rand(3000, 25000),
                'barcode' => strtoupper(Str::random(10)),
                'fk_id_toko' => 1,
            ]);

            // 2. Create CatatanStock (invoice)
            $catatan = CatatanStock::create([
                'total_harga' => 0, // will be updated after TambahStock created
                'bukti_nota' => 'nota_' . Str::random(6) . '.jpg',
                'tanggal_belanja' => Carbon::now()->subDays(rand(0, 30))->toDateString(),
                'fk_id_toko' => 1,
            ]);

            // 3. Create TambahStock (stock detail for the product)
            $jumlah = rand(10, 50);
            $hargaBeli = rand(2000, 20000);

            TambahStock::create([
                'jumlah' => $jumlah,
                'harga_beli' => $hargaBeli,
                'expired' => Carbon::now()->addMonths(rand(1, 12))->toDateString(),
                'fk_id_catatan_stock' => $catatan->id,
                'fk_id_product' => $product->id,
            ]);

            // Update CatatanStock total_harga
            $catatan->update([
                'total_harga' => $jumlah * $hargaBeli,
            ]);
        }

        // Transaksi
        $today = Carbon::today();

        // Create 20 transactions
        for ($i = 0; $i < 20; $i++) {
            $jumlahItems = rand(1, 5); // each transaction has 1–5 items
            $totalBayar = 0;
            $totalModal = 0;

            // Create the transaction record first (will update totals later)
            $transaksi = Transaksi::create([
                'total_bayar' => 0,
                'total_modal' => 0,
                'jenis_transaksi' => '0',
                'fk_id_kasir' => 1,
                'fk_id_toko' => 1,
                'created_at' => $today,
                'updated_at' => $today,
            ]);

            // Pick random products
            $products = Product::inRandomOrder()->take($jumlahItems)->get();

            foreach ($products as $product) {
                $qty = rand(1, 5);
                $hargaJual = $product->harga_jual;

                // Find average harga_beli from TambahStock for the product
                $hargaBeli = TambahStock::where('fk_id_product', $product->id)->avg('harga_beli') ?? 0;

                $totalBayar += $hargaJual * $qty;
                $totalModal += $hargaBeli * $qty;

                // Create TransaksiItem
                TransaksiItem::create([
                    'jumlah_product' => $qty,
                    'harga_jual_product' => $hargaJual,
                    'fk_id_product' => $product->id,
                    'fk_id_transaksi' => $transaksi->id,
                    'created_at' => $today,
                    'updated_at' => $today,
                ]);
            }

            // Update totals
            $transaksi->update([
                'total_bayar' => $totalBayar,
                'total_modal' => $totalModal,
            ]);
        }
    }
}
