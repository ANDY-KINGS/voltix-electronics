<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'country_of_origin', 'logo'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
