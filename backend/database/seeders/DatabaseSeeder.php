<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Outlets
        $outlet1 = Outlet::create([
            'name' => 'Toko Retail Utama',
            'code' => 'RET001',
            'business_type' => 'retail',
            'address' => 'Jl. Merdeka No. 123',
            'phone' => '081234567890',
            'enable_qr_order' => false,
        ]);

        $outlet2 = Outlet::create([
            'name' => 'Minimarket Sejahtera',
            'code' => 'MKT001',
            'business_type' => 'minimarket',
            'address' => 'Jl. Sudirman No. 456',
            'phone' => '081234567891',
            'enable_qr_order' => false,
        ]);

        $outlet3 = Outlet::create([
            'name' => 'Warung Makan Sedap',
            'code' => 'FNB001',
            'business_type' => 'fnb',
            'address' => 'Jl. Ahmad Yani No. 789',
            'phone' => '081234567892',
            'enable_qr_order' => true,
        ]);

        // Create Users
        User::create([
            'name' => 'Owner',
            'email' => 'owner@kasir.app',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'outlet_id' => null, // Can access all outlets
        ]);

        User::create([
            'name' => 'Supervisor Retail',
            'email' => 'supervisor@kasir.app',
            'password' => bcrypt('password'),
            'role' => 'supervisor',
            'outlet_id' => $outlet1->id,
        ]);

        User::create([
            'name' => 'Kasir Retail',
            'email' => 'kasir@kasir.app',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'outlet_id' => $outlet1->id,
        ]);

        User::create([
            'name' => 'Kasir Minimarket',
            'email' => 'kasir2@kasir.app',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'outlet_id' => $outlet2->id,
        ]);

        User::create([
            'name' => 'Kasir F&B',
            'email' => 'kasir3@kasir.app',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'outlet_id' => $outlet3->id,
        ]);

        User::create([
            'name' => 'Kitchen F&B',
            'email' => 'kitchen@kasir.app',
            'password' => bcrypt('password'),
            'role' => 'kitchen',
            'outlet_id' => $outlet3->id,
        ]);

        // Create Categories
        $categories = [
            // Retail/Minimarket categories (produk kemasan)
            ['name' => 'Makanan', 'slug' => 'makanan', 'color' => '#EF4444'],
            ['name' => 'Minuman', 'slug' => 'minuman', 'color' => '#3B82F6'],
            ['name' => 'Snack', 'slug' => 'snack', 'color' => '#F59E0B'],
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'color' => '#8B5CF6'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'color' => '#EC4899'],
            ['name' => 'Kebutuhan Rumah', 'slug' => 'kebutuhan-rumah', 'color' => '#10B981'],
            
            // F&B categories (makanan/minuman siap saji)
            ['name' => 'Makanan FNB', 'slug' => 'makanan-fnb', 'color' => '#DC2626'],
            ['name' => 'Minuman FNB', 'slug' => 'minuman-fnb', 'color' => '#2563EB'],
            ['name' => 'Snack FNB', 'slug' => 'snack-fnb', 'color' => '#D97706'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Create Products
        $products = [
            // Makanan Retail/Minimarket (produk kemasan)
            ['name' => 'Indomie Goreng', 'category_id' => 1, 'selling_price' => 3500, 'cost_price' => 2500, 'stock' => 100],
            ['name' => 'Roti Tawar', 'category_id' => 1, 'selling_price' => 15000, 'cost_price' => 12000, 'stock' => 50],
            ['name' => 'Mie Sedaap', 'category_id' => 1, 'selling_price' => 3000, 'cost_price' => 2200, 'stock' => 120],
            
            // Minuman Retail/Minimarket (kemasan)
            ['name' => 'Aqua 600ml', 'category_id' => 2, 'selling_price' => 4000, 'cost_price' => 3000, 'stock' => 200],
            ['name' => 'Coca Cola 330ml', 'category_id' => 2, 'selling_price' => 7000, 'cost_price' => 5000, 'stock' => 150],
            ['name' => 'Teh Botol Sosro', 'category_id' => 2, 'selling_price' => 5000, 'cost_price' => 3500, 'stock' => 180],
            
            // Snack Retail/Minimarket (kemasan)
            ['name' => 'Chitato', 'category_id' => 3, 'selling_price' => 10000, 'cost_price' => 7500, 'stock' => 80],
            ['name' => 'Oreo', 'category_id' => 3, 'selling_price' => 8500, 'cost_price' => 6500, 'stock' => 60],
            ['name' => 'Tango', 'category_id' => 3, 'selling_price' => 5000, 'cost_price' => 3500, 'stock' => 100],
            
            // Makanan FNB (siap saji)
            ['name' => 'Nasi Goreng', 'category_id' => 7, 'selling_price' => 25000, 'cost_price' => 15000, 'track_stock' => false],
            ['name' => 'Mie Goreng', 'category_id' => 7, 'selling_price' => 20000, 'cost_price' => 12000, 'track_stock' => false],
            ['name' => 'Ayam Goreng', 'category_id' => 7, 'selling_price' => 15000, 'cost_price' => 8000, 'track_stock' => false],
            ['name' => 'Sate Ayam', 'category_id' => 7, 'selling_price' => 18000, 'cost_price' => 10000, 'track_stock' => false],
            
            // Minuman FNB (siap saji)
            ['name' => 'Es Teh Manis', 'category_id' => 8, 'selling_price' => 5000, 'cost_price' => 2000, 'track_stock' => false],
            ['name' => 'Es Jeruk', 'category_id' => 8, 'selling_price' => 8000, 'cost_price' => 4000, 'track_stock' => false],
            ['name' => 'Kopi Hitam', 'category_id' => 8, 'selling_price' => 10000, 'cost_price' => 5000, 'track_stock' => false],
            ['name' => 'Jus Alpukat', 'category_id' => 8, 'selling_price' => 12000, 'cost_price' => 7000, 'track_stock' => false],
            
            // Snack FNB (siap saji)
            ['name' => 'Pisang Goreng', 'category_id' => 9, 'selling_price' => 8000, 'cost_price' => 4000, 'track_stock' => false],
            ['name' => 'Tahu Isi', 'category_id' => 9, 'selling_price' => 6000, 'cost_price' => 3000, 'track_stock' => false],
        ];

        foreach ($products as $index => $prod) {
            $sku = 'SKU-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT);
            $barcode = $this->generateEAN13($index + 1);
            
            Product::create(array_merge($prod, [
                'sku' => $sku,
                'barcode' => $barcode,
                'stock' => $prod['stock'] ?? 0,
                'min_stock' => 10,
                'track_stock' => $prod['track_stock'] ?? true,
            ]));
        }

        // Create Tables for F&B outlet
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'outlet_id' => $outlet3->id,
                'table_number' => 'Table ' . $i,
                'capacity' => 4,
                'status' => 'available',
            ]);
        }
    }

    private function generateEAN13($productId): string
    {
        $code = '200' . str_pad($productId, 9, '0', STR_PAD_LEFT);
        
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$code[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        
        return $code . $checkDigit;
    }
}
