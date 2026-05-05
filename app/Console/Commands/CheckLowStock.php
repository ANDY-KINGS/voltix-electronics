<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Mail\LowStockAlertEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckLowStock extends Command
{
    protected $signature = 'app:check-low-stock';
    protected $description = 'Check for low stock products and notify admin';

    public function handle()
    {
        $lowStockProducts = Product::lowStock()->get();

        if ($lowStockProducts->count() > 0) {
            // Find all admins
            $admins = User::role('admin')->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new LowStockAlertEmail($lowStockProducts));
            }
            $this->info('Low stock alerts dispatched.');
        } else {
            $this->info('No low stock products found.');
        }
    }
}
