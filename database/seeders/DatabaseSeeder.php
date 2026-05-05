<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\SerialNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Roles & Permissions ───────────────────────────────────────────────
        $roles = ['admin', 'cashier', 'owner'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $permissions = [
            'manage-users', 'manage-products', 'manage-categories',
            'manage-suppliers', 'manage-customers', 'process-sales',
            'view-reports', 'manage-settings', 'manage-warranty-claims',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin   = Role::where('name', 'admin')->first();
        $admin->givePermissionTo(Permission::all());

        $cashier = Role::where('name', 'cashier')->first();
        $cashier->givePermissionTo(['process-sales', 'manage-customers', 'manage-warranty-claims']);

        $owner = Role::where('name', 'owner')->first();
        $owner->givePermissionTo(['view-reports']);

        // ─── 2. Admin User ────────────────────────────────────────────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@smartpos.com'],
            [
                'name'      => 'System Admin',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );
        $adminUser->syncRoles('admin');

        // ─── 2b. Default Cashier ──────────────────────────────────────────────────
        $cashierUser = User::firstOrCreate(
            ['email' => 'cashier@smartpos.com'],
            [
                'name'      => 'Default Cashier',
                'password'  => Hash::make('password'),
                'role'      => 'cashier',
                'is_active' => true,
            ]
        );
        $cashierUser->syncRoles('cashier');

        // ─── 3. Brands ────────────────────────────────────────────────────────────
        $brandData = [
            ['name' => 'Samsung',  'country_of_origin' => 'South Korea'],
            ['name' => 'Apple',    'country_of_origin' => 'USA'],
            ['name' => 'HP',       'country_of_origin' => 'USA'],
            ['name' => 'Lenovo',   'country_of_origin' => 'China'],
            ['name' => 'Infinix',  'country_of_origin' => 'China'],
            ['name' => 'Tecno',    'country_of_origin' => 'China'],
            ['name' => 'Dell',     'country_of_origin' => 'USA'],
            ['name' => 'Sony',     'country_of_origin' => 'Japan'],
            ['name' => 'JBL',      'country_of_origin' => 'USA'],
            ['name' => 'Hisense',  'country_of_origin' => 'China'],
            ['name' => 'Oraimo',   'country_of_origin' => 'China'],
        ];
        foreach ($brandData as $b) {
            Brand::firstOrCreate(['name' => $b['name']], $b);
        }

        // Helper to get brand id
        $brand = fn(string $name) => Brand::where('name', $name)->first()?->id;

        // ─── 4. Categories ────────────────────────────────────────────────────────
        $categories = ['Smartphones', 'Laptops', 'Accessories', 'Televisions', 'Audio'];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        $cat = fn(string $name) => Category::where('name', $name)->first()?->id;

        // ─── 5. Products ──────────────────────────────────────────────────────────
        $products = [
            ['sku'=>'ELC-001','name'=>'Samsung Galaxy A55',     'category_id'=>$cat('Smartphones'), 'brand_id'=>$brand('Samsung'),  'model_number'=>'SM-A556E',       'price'=>62000,  'cost_price'=>52000, 'stock_quantity'=>15, 'reorder_level'=>5,  'warranty_months'=>12, 'serial_tracking'=>true],
            ['sku'=>'ELC-002','name'=>'Infinix Hot 40 Pro',      'category_id'=>$cat('Smartphones'), 'brand_id'=>$brand('Infinix'),  'model_number'=>'X6837',           'price'=>22000,  'cost_price'=>17500, 'stock_quantity'=>25, 'reorder_level'=>8,  'warranty_months'=>12, 'serial_tracking'=>true],
            ['sku'=>'ELC-003','name'=>'Tecno Spark 20 Pro',      'category_id'=>$cat('Smartphones'), 'brand_id'=>$brand('Tecno'),    'model_number'=>'KJ6',             'price'=>18500,  'cost_price'=>14800, 'stock_quantity'=>30, 'reorder_level'=>10, 'warranty_months'=>12, 'serial_tracking'=>true],
            ['sku'=>'ELC-004','name'=>'iPhone 14 128GB',         'category_id'=>$cat('Smartphones'), 'brand_id'=>$brand('Apple'),   'model_number'=>'MPWH3LLA',        'price'=>115000, 'cost_price'=>98000, 'stock_quantity'=>8,  'reorder_level'=>3,  'warranty_months'=>12, 'serial_tracking'=>true],
            ['sku'=>'ELC-005','name'=>'HP 250 G9 Laptop',        'category_id'=>$cat('Laptops'),     'brand_id'=>$brand('HP'),      'model_number'=>'6S6F5EA',         'price'=>58000,  'cost_price'=>48000, 'stock_quantity'=>10, 'reorder_level'=>3,  'warranty_months'=>24, 'serial_tracking'=>true],
            ['sku'=>'ELC-006','name'=>'Lenovo IdeaPad 3',        'category_id'=>$cat('Laptops'),     'brand_id'=>$brand('Lenovo'),  'model_number'=>'82KT00ADUS',      'price'=>52000,  'cost_price'=>43000, 'stock_quantity'=>12, 'reorder_level'=>3,  'warranty_months'=>24, 'serial_tracking'=>true],
            ['sku'=>'ELC-007','name'=>'Dell Inspiron 15',        'category_id'=>$cat('Laptops'),     'brand_id'=>$brand('Dell'),    'model_number'=>'3520-INS3520',    'price'=>72000,  'cost_price'=>61000, 'stock_quantity'=>7,  'reorder_level'=>2,  'warranty_months'=>24, 'serial_tracking'=>true],
            ['sku'=>'ELC-008','name'=>'Samsung 65W Charger',     'category_id'=>$cat('Accessories'), 'brand_id'=>$brand('Samsung'), 'model_number'=>'EP-TA865',        'price'=>2500,   'cost_price'=>1800,  'stock_quantity'=>50, 'reorder_level'=>15, 'warranty_months'=>6,  'serial_tracking'=>false],
            ['sku'=>'ELC-009','name'=>'Oraimo FreePods 3',       'category_id'=>$cat('Accessories'), 'brand_id'=>$brand('Oraimo'), 'model_number'=>'OEP-E75D',         'price'=>3200,   'cost_price'=>2400,  'stock_quantity'=>40, 'reorder_level'=>10, 'warranty_months'=>6,  'serial_tracking'=>false],
            ['sku'=>'ELC-010','name'=>'USB-C Cable 2m',          'category_id'=>$cat('Accessories'), 'brand_id'=>null,              'model_number'=>null,              'price'=>450,    'cost_price'=>280,   'stock_quantity'=>100,'reorder_level'=>30, 'warranty_months'=>0,  'serial_tracking'=>false],
            ['sku'=>'ELC-011','name'=>'Samsung 43" 4K Smart TV', 'category_id'=>$cat('Televisions'), 'brand_id'=>$brand('Samsung'), 'model_number'=>'UA43TU7000',      'price'=>48000,  'cost_price'=>39500, 'stock_quantity'=>6,  'reorder_level'=>2,  'warranty_months'=>24, 'serial_tracking'=>true],
            ['sku'=>'ELC-012','name'=>'Hisense 32" LED TV',      'category_id'=>$cat('Televisions'), 'brand_id'=>$brand('Hisense'), 'model_number'=>'32A4G',           'price'=>22500,  'cost_price'=>17800, 'stock_quantity'=>10, 'reorder_level'=>3,  'warranty_months'=>24, 'serial_tracking'=>true],
            ['sku'=>'ELC-013','name'=>'JBL Charge 5 Speaker',    'category_id'=>$cat('Audio'),       'brand_id'=>$brand('JBL'),     'model_number'=>'JBLCHARGE5BLK',   'price'=>12500,  'cost_price'=>9800,  'stock_quantity'=>15, 'reorder_level'=>4,  'warranty_months'=>12, 'serial_tracking'=>false],
            ['sku'=>'ELC-014','name'=>'Sony WH-1000XM5',         'category_id'=>$cat('Audio'),       'brand_id'=>$brand('Sony'),    'model_number'=>'WH1000XM5B',      'price'=>38000,  'cost_price'=>30000, 'stock_quantity'=>5,  'reorder_level'=>2,  'warranty_months'=>12, 'serial_tracking'=>true],
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate(['sku' => $prod['sku']], array_merge($prod, ['is_active' => true]));
        }

        // ─── 6. Serial Numbers (3-5 per serial-tracked product) ──────────────────
        $trackedProducts = Product::where('serial_tracking', true)->get();
        foreach ($trackedProducts as $product) {
            $count = rand(3, 5);
            for ($i = 0; $i < $count; $i++) {
                $sn = strtoupper($product->sku . '-SN-' . Str::random(8));
                SerialNumber::firstOrCreate(
                    ['serial_number' => $sn],
                    [
                        'product_id' => $product->id,
                        'status'     => 'available',
                    ]
                );
            }
        }

        // ─── 7. Customers with National ID ────────────────────────────────────────
        $customers = [
            ['name' => 'John Kamau',     'phone' => '0712345678', 'email' => 'john@email.com',  'id_number' => '12345678'],
            ['name' => 'Mary Wanjiku',   'phone' => '0723456789', 'email' => 'mary@email.com',  'id_number' => '23456789'],
            ['name' => 'Peter Odhiambo', 'phone' => '0734567890', 'email' => 'peter@email.com', 'id_number' => '34567890'],
            ['name' => 'Grace Akinyi',   'phone' => '0745678901', 'email' => 'grace@email.com', 'id_number' => '45678901'],
            ['name' => 'David Mwangi',   'phone' => '0756789012', 'email' => 'david@email.com', 'id_number' => '56789012'],
        ];
        foreach ($customers as $c) {
            Customer::firstOrCreate(['email' => $c['email']], $c);
        }
    }
}
