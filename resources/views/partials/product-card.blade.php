<a class="product-card" href="{{ route('products.show', $product['slug']) }}" data-product data-category-id="{{ $product['category_id'] }}" data-search-value="{{ strtolower($product['name'].' '.$product['category'].' '.$product['label']) }}">
    @include('partials.product-art', ['product' => $product, 'showDiscount' => true])
    <div class="product-info"><p class="product-category">{{ $product['category'] }}</p><h3>{{ $product['name'] }}</h3><p class="product-tagline">{{ $product['label'] }}</p><div class="product-prices"><span class="offer-price">৳{{ number_format($product['offer']) }}</span><del>৳{{ number_format($product['price']) }}</del></div></div>
</a>
