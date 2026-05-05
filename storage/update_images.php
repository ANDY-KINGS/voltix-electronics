<?php

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

$products = Product::whereNull('image')->get();
foreach ($products as $p) {
    $filename = 'products/' . $p->name . '.png';
    if (Storage::disk('public')->exists($filename)) {
        $p->image = $filename;
        $p->save();
        echo 'Updated: ' . $p->name . PHP_EOL;
    } else {
        echo 'Missing: ' . $filename . PHP_EOL;
    }
}
