<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'supplier_id', 'brand_id', 'name', 'sku',
        'model_number', 'price', 'cost_price', 'warranty_months',
        'serial_tracking', 'stock_quantity', 'reorder_level', 'image', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active'       => 'boolean',
            'serial_tracking' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'reorder_level');
    }

    public function scopeSerialTracked($query)
    {
        return $query->where('serial_tracking', true);
    }
}
