<div class="product-art product-art-{{ $product['art'] }}{{ $artClass ?? '' }}" role="img" aria-label="Decorative placeholder for {{ $product['name'] }}">
    <span class="art-shape"></span><span class="art-detail"></span><span class="art-shadow"></span>
    @if ($showDiscount ?? false)
        <span class="discount-label">{{ $product['discount'] }}</span>
    @endif
    <span class="placeholder-label">DEMO</span>
</div>
