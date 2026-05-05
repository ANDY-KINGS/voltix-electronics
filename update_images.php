<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Str;

$localImages = [
    'ELC-001' => 'products/samsung_a55.png',     // Samsung A55
    'ELC-002' => 'products/infinix_hot40.png',   // Infinix Hot 40
    'ELC-003' => 'products/tecno_spark20.png',   // Tecno Spark 20
    'ELC-004' => 'products/iphone14.png',        // iPhone 14
    'ELC-005' => 'products/hp_250_g9.png',       // HP 250 G9
    'ELC-006' => 'products/lenovo_ideapad3.png', // Lenovo IdeaPad 3
];

$products = Product::all();

foreach ($products as $product) {
    if (isset($localImages[$product->sku])) {
        // We already have these local images copied
        $product->image = $localImages[$product->sku];
        $product->save();
        echo "Updated generated image for: " . $product->name . "\n";
    } else {
        // Generate a local image using PHP GD to serve as an authentic placeholder
        $slug = Str::slug($product->name);
        $filename = "products/{$slug}.png";
        $filepath = storage_path("app/public/{$filename}");
        
        $width = 600;
        $height = 600;
        $image = imagecreatetruecolor($width, $height);
        
        // Background color
        $bg = imagecolorallocate($image, 248, 249, 250); // Light gray #f8f9fa
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);
        
        // Text color
        $textColor = imagecolorallocate($image, 31, 58, 110); // Duka Electronics Blue #1F3A6E
        
        // Wrap text to fit
        $text = $product->name;
        $fontPath = 'C:\Windows\Fonts\arialbd.ttf'; // Using a standard Windows font
        
        // Draw text using imagettftext if font exists, else fallback to standard string
        if(file_exists($fontPath)) {
            // Calculate bounding box and center
            $fontSize = 32;
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
            $textWidth = $bbox[2] - $bbox[0];
            $x = ($width - $textWidth) / 2;
            $y = ($height / 2);
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $text);
        } else {
            // Fallback font
            $textWidth = imagefontwidth(5) * strlen($text);
            $x = ($width - $textWidth) / 2;
            $y = ($height / 2) - (imagefontheight(5) / 2);
            imagestring($image, 5, $x, $y, $text, $textColor);
        }

        imagepng($image, $filepath);
        imagedestroy($image);
        
        $product->image = $filename;
        $product->save();
        echo "Created local dummy image for: " . $product->name . "\n";
    }
}

echo "All 14 product images assigned automatically!\n";
