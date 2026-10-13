<?php

namespace App\Support;

class DemoCatalog
{
    /**
     * The temporary category source and demo product assignments for the storefront.
     * Replace these definitions with database-backed categories and products in a later phase.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public static function categories(): array
    {
        return [
            ['id' => 'beauty-health', 'name' => 'Beauty & Health'],
            ['id' => 'food', 'name' => 'Food'],
            ['id' => 'electronics', 'name' => 'Electronics'],
            ['id' => 'watch', 'name' => 'Watch'],
            ['id' => 'suitcase-trolley', 'name' => 'Suitcase/Trolley'],
            ['id' => 'perfume', 'name' => 'Perfume'],
        ];
    }

    /**
     * Return all temporary demo products with their display category resolved from the source above.
     *
     * @return array<int, array{slug: string, name: string, category_id: string, category: string, price: int, offer: int, discount: string, art: string, label: string, description: string, features: array<int, string>, contents: array<int, string>, flash_sale: bool, reference: string, availability: string}>
     */
    public static function all(): array
    {
        $categoryNames = array_column(self::categories(), 'name', 'id');

        return array_map(function (array $product) use ($categoryNames): array {
            $product['category'] = $categoryNames[$product['category_id']];
            $product['reference'] = 'DEMO-'.strtoupper($product['slug']);
            $product['availability'] = 'Demo availability not tracked';

            return $product;
        }, self::demoProducts());
    }

    /**
     * Return the temporary demo products selected for Flash Sales.
     *
     * @return array<int, array{slug: string, name: string, category_id: string, category: string, price: int, offer: int, discount: string, art: string, label: string, description: string, features: array<int, string>, contents: array<int, string>, flash_sale: bool, reference: string, availability: string}>
     */
    public static function flashSales(): array
    {
        return array_values(array_filter(self::all(), fn (array $product): bool => $product['flash_sale']));
    }

    /**
     * Find one temporary demo product by its URL slug.
     *
     * @return array{slug: string, name: string, category_id: string, category: string, price: int, offer: int, discount: string, art: string, label: string, description: string, features: array<int, string>, contents: array<int, string>, flash_sale: bool, reference: string, availability: string}|null
     */
    public static function find(string $slug): ?array
    {
        foreach (self::all() as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Temporary demo products; all product-to-category assignments use stable category IDs.
     *
     * @return array<int, array{slug: string, name: string, category_id: string, price: int, offer: int, discount: string, art: string, label: string, description: string, features: array<int, string>, contents: array<int, string>, flash_sale: bool}>
     */
    private static function demoProducts(): array
    {
        return [
            ['slug' => 'daily-glow-serum', 'name' => 'Daily Glow Serum', 'category_id' => 'beauty-health', 'price' => 1450, 'offer' => 1190, 'discount' => '18% OFF', 'art' => 'serum', 'label' => 'A little daily ritual', 'description' => 'A lightweight demo skincare pick for a simple, feel-good daily routine.', 'features' => ['Lightweight everyday formula', 'Suitable for a simple morning routine', 'Demo product details for preview'], 'contents' => ['1 × Daily Glow Serum (demo)'], 'flash_sale' => true],
            ['slug' => 'soft-touch-hand-cream', 'name' => 'Soft Touch Hand Cream', 'category_id' => 'beauty-health', 'price' => 780, 'offer' => 650, 'discount' => '17% OFF', 'art' => 'cream', 'label' => 'Comfort in your pocket', 'description' => 'A pocket-sized self-care concept from the temporary Soumik demo collection.', 'features' => ['Easy-to-carry format concept', 'A small everyday care ritual', 'Demo product details for preview'], 'contents' => ['1 × Soft Touch Hand Cream (demo)'], 'flash_sale' => false],
            ['slug' => 'daily-sun-shield', 'name' => 'Daily Sun Shield', 'category_id' => 'beauty-health', 'price' => 1250, 'offer' => 990, 'discount' => '21% OFF', 'art' => 'cream', 'label' => 'A brighter day ahead', 'description' => 'A daily sun-care concept included as temporary demo storefront content.', 'features' => ['Everyday sun-care concept', 'Easy routine-friendly size', 'Demo product details for preview'], 'contents' => ['1 × Daily Sun Shield (demo)'], 'flash_sale' => true],
            ['slug' => 'wildflower-honey', 'name' => 'Wildflower Honey', 'category_id' => 'food', 'price' => 850, 'offer' => 720, 'discount' => '15% OFF', 'art' => 'food', 'label' => 'A spoonful of sunshine', 'description' => 'A small-batch style pantry pick concept from the Soumik demo collection.', 'features' => ['Wildflower-inspired pantry pick', 'Lovely with tea or breakfast', 'Demo product details for preview'], 'contents' => ['1 × Wildflower Honey (demo jar)'], 'flash_sale' => true],
            ['slug' => 'roasted-cashews', 'name' => 'Roasted Cashews', 'category_id' => 'food', 'price' => 620, 'offer' => 540, 'discount' => '13% OFF', 'art' => 'food', 'label' => 'A little crunch break', 'description' => 'A snack-time favourite concept, shown as temporary demo content.', 'features' => ['Roasted snack concept', 'Easy to share or enjoy on the go', 'Demo product details for preview'], 'contents' => ['1 × Roasted Cashews (demo pack)'], 'flash_sale' => false],
            ['slug' => 'everyday-wireless-buds', 'name' => 'Everyday Wireless Buds', 'category_id' => 'electronics', 'price' => 2850, 'offer' => 2390, 'discount' => '16% OFF', 'art' => 'buds', 'label' => 'Sound, simplified', 'description' => 'A compact audio essential concept shown here as temporary storefront demo content.', 'features' => ['Compact wireless design concept', 'Everyday listening companion', 'Demo product details for preview'], 'contents' => ['1 × Pair of wireless buds (demo)', '1 × Charging case (demo)'], 'flash_sale' => true],
            ['slug' => 'pocket-power-bank', 'name' => 'Pocket Power Bank', 'category_id' => 'electronics', 'price' => 1950, 'offer' => 1590, 'discount' => '18% OFF', 'art' => 'power', 'label' => 'Keep going, powered', 'description' => 'A pocket-sized power accessory concept from the Soumik demo assortment.', 'features' => ['Compact portable design concept', 'Useful everyday carry size', 'Demo product details for preview'], 'contents' => ['1 × Pocket Power Bank (demo)'], 'flash_sale' => false],
            ['slug' => 'heritage-face-watch', 'name' => 'Heritage Face Watch', 'category_id' => 'watch', 'price' => 4200, 'offer' => 3590, 'discount' => '15% OFF', 'art' => 'watch', 'label' => 'Time, well spent', 'description' => 'A timeless everyday watch concept from our temporary demo edit.', 'features' => ['Classic dial silhouette', 'Easy-to-style neutral finish', 'Demo product details for preview'], 'contents' => ['1 × Heritage Face Watch (demo)', '1 × Presentation box (demo)'], 'flash_sale' => true],
            ['slug' => 'minimal-day-watch', 'name' => 'Minimal Day Watch', 'category_id' => 'watch', 'price' => 3650, 'offer' => 3190, 'discount' => '13% OFF', 'art' => 'watch', 'label' => 'A quiet classic', 'description' => 'A clean, minimal watch concept included for storefront preview only.', 'features' => ['Simple dial concept', 'Understated everyday style', 'Demo product details for preview'], 'contents' => ['1 × Minimal Day Watch (demo)'], 'flash_sale' => false],
            ['slug' => 'weekend-cabin-trolley', 'name' => 'Weekend Cabin Trolley', 'category_id' => 'suitcase-trolley', 'price' => 6800, 'offer' => 5890, 'discount' => '13% OFF', 'art' => 'trolley', 'label' => 'Ready when you are', 'description' => 'A cabin-size travel case concept for short escapes and weekend plans.', 'features' => ['Compact cabin luggage concept', 'Smooth-rolling wheel design', 'Demo product details for preview'], 'contents' => ['1 × Weekend Cabin Trolley (demo)'], 'flash_sale' => true],
            ['slug' => 'softside-travel-case', 'name' => 'Softside Travel Case', 'category_id' => 'suitcase-trolley', 'price' => 5400, 'offer' => 4790, 'discount' => '11% OFF', 'art' => 'trolley', 'label' => 'Pack a little lighter', 'description' => 'A flexible travel bag concept from the temporary Soumik demo collection.', 'features' => ['Softside travel design concept', 'Practical packing shape', 'Demo product details for preview'], 'contents' => ['1 × Softside Travel Case (demo)'], 'flash_sale' => false],
            ['slug' => 'amber-eau-de-parfum', 'name' => 'Amber Eau de Parfum', 'category_id' => 'perfume', 'price' => 3200, 'offer' => 2690, 'discount' => '16% OFF', 'art' => 'perfume', 'label' => 'Warm, lasting notes', 'description' => 'A warm, softly layered fragrance concept from our temporary demo collection.', 'features' => ['Warm amber-inspired profile', 'Made for everyday wear', 'Demo product details for preview'], 'contents' => ['1 × Amber Eau de Parfum (demo)'], 'flash_sale' => true],
            ['slug' => 'citrus-bloom-mist', 'name' => 'Citrus Bloom Mist', 'category_id' => 'perfume', 'price' => 1750, 'offer' => 1490, 'discount' => '15% OFF', 'art' => 'mist', 'label' => 'A bright little lift', 'description' => 'A bright citrus-inspired fragrance mist concept, included as demo content only.', 'features' => ['Fresh citrus-inspired profile', 'Light mist format concept', 'Demo product details for preview'], 'contents' => ['1 × Citrus Bloom Mist (demo)'], 'flash_sale' => false],
        ];
    }
}
