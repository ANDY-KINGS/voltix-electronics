<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false; // Only uses created_at

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'unit_price', 'subtotal', 'created_at',
        'serial_number_id', 'warranty_months', 'warranty_expiry'
    ];

    protected function casts(): array
    {
        return [
            'created_at'      => 'datetime',
            'warranty_expiry' => 'date',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function serialNumber()
    {
        return $this->belongsTo(SerialNumber::class);
    }

    public function warrantyClaim()
    {
        return $this->hasOne(WarrantyClaim::class);
    }
}
