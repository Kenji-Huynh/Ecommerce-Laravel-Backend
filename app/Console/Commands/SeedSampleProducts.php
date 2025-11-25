<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedSampleProducts extends Command
{
    protected $signature = 'demo:seed-products {--kids=5} {--men=5} {--women=5}';

    protected $description = 'Tạo nhanh sản phẩm demo: 5 Kids, 5 Men, 5 Women (mặc định)';

    public function handle(): int
    {
        $counts = [
            'Kids' => (int) $this->option('kids'),
            'Men'  => (int) $this->option('men'),
            'Women'=> (int) $this->option('women'),
        ];

        $this->info('🚀 Bắt đầu tạo sản phẩm demo...');

        foreach ($counts as $categoryName => $count) {
            if ($count <= 0) continue;

            $slug = Str::slug($categoryName);
            $category = Category::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $categoryName,
                    'image' => "categories/{$slug}.jpg",
                    'is_active' => true,
                    'sort_order' => 1,
                ]
            );

            // Tránh tạo trùng nhiều lần: nếu đã có đủ sản phẩm trong category thì bỏ qua
            $existing = Product::where('category_id', $category->id)->count();
            if ($existing >= $count) {
                $this->warn("Bỏ qua {$categoryName}: đã có {$existing}/{$count} sản phẩm.");
                continue;
            }

            for ($i = $existing + 1; $i <= $count; $i++) {
                $name = sprintf('%s Product %02d', $categoryName, $i);
                $price = $this->randomPriceForCategory($categoryName);
                $slugProduct = Str::slug($name) . '-' . Str::random(6);
                $mainImage = $this->placeholderImage($categoryName, $i);

                $product = Product::create([
                    'name' => $name,
                    'slug' => $slugProduct,
                    'description' => $this->fakeDescription($categoryName),
                    'price' => $price,
                    'compare_price' => $price + rand(5, 20),
                    'discount' => 0,
                    'sku' => strtoupper(substr($categoryName,0,1)) . '-' . strtoupper(Str::random(6)),
                    'is_new' => (bool)rand(0,1),
                    'is_featured' => (bool)rand(0,1),
                    'in_stock' => true,
                    'stock_quantity' => rand(10, 100),
                    'main_image' => $mainImage,
                    'category_id' => $category->id,
                    'sizes' => ['S','M','L'],
                    'colors' => ['black','white','blue'],
                    'tags' => [$slug,'demo'],
                    'rating' => rand(35, 50)/10, // 3.5 → 5.0
                    'reviews_count' => rand(0, 50),
                    'material' => 'Cotton',
                    'origin' => 'Vietnam',
                ]);

                // Thêm 2 ảnh phụ (placeholder)
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $this->placeholderImage($categoryName, $i, 2),
                    'sort_order' => 1,
                ]);
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $this->placeholderImage($categoryName, $i, 3),
                    'sort_order' => 2,
                ]);

                $this->line("✔️  Đã tạo: {$name} ({$categoryName})");
            }
        }

        $this->info('✅ Hoàn tất tạo sản phẩm demo.');
        return Command::SUCCESS;
    }

    private function randomPriceForCategory(string $category): float
    {
        switch (strtolower($category)) {
            case 'kids':
                return round(rand(999, 2999) / 100, 2); // 9.99 - 29.99
            case 'men':
            case 'women':
            default:
                return round(rand(1999, 9999) / 100, 2); // 19.99 - 99.99
        }
    }

    private function placeholderImage(string $category, int $i, int $variant = 1): string
    {
        // Demo ảnh thật cho từng loại
        $images = [
            'Kids' => [
                'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
                'https://images.unsplash.com/photo-1519125323398-675f0ddb6308',
                'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e',
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d',
                'https://images.unsplash.com/photo-1519864600265-abb23847ef2c',
            ],
            'Men' => [
                'https://images.unsplash.com/photo-1517841905240-472988babdf9',
                'https://images.unsplash.com/photo-1465101046530-73398c7f28ca',
                'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91',
                'https://images.unsplash.com/photo-1519125323398-675f0ddb6308',
                'https://images.unsplash.com/photo-1465101178521-c1a9136a3b99',
            ],
            'Women' => [
                'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2',
                'https://images.unsplash.com/photo-1518717758536-85ae29035b6d',
                'https://images.unsplash.com/photo-1519340333755-c6e2a6a1b49a',
                'https://images.unsplash.com/photo-1519864600265-abb23847ef2c',
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d',
            ],
        ];
        $arr = $images[$category] ?? $images['Kids'];
        // Lấy ảnh theo thứ tự, nếu vượt quá thì lặp lại
        $index = ($i + $variant - 2) % count($arr);
        return $arr[$index] . '?auto=format&fit=crop&w=800&q=80';
    }

    private function fakeDescription(string $category): string
    {
        return "High‑quality {$category} apparel. Soft fabric, modern fit, perfect for everyday wear.";
    }
}
