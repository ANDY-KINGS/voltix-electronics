<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerialNumber extends Model
{
    protected $fillable = ['product_id', 'serial_number', 'status', 'order_item_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }
}
