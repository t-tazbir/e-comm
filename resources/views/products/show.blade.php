@extends('layouts.storefront', [
    'title' => $product['name'].' | Soumik',
    'metaDescription' => $product['description'],
    'canonicalUrl' => route('products.show', $product['slug']),
    'ogTitle' => $product['name'].' | Soumik',
])

@section('content')
<main id="main-content" class="page-shell product-detail-page">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span>
        <a href="{{ route('home') }}#products">{{ $product['category'] }}</a><span aria-hidden="true">/</span>
        <span aria-current="page">{{ $product['name'] }}</span>
    </nav>

    <div class="product-detail-layout">
        <section class="product-gallery" aria-label="{{ $product['name'] }} product gallery">
            <div class="product-gallery-main" data-gallery-main data-view="front">
                @include('partials.product-art', ['product' => $product, 'artClass' => ' product-art--detail'])
            </div>
            <div class="gallery-thumbnails" role="group" aria-label="Choose a product view">
                <button class="gallery-thumbnail is-selected" type="button" data-gallery-view="front" aria-label="Show front view" aria-pressed="true"><span class="thumbnail-art thumbnail-front product-art-{{ $product['art'] }}" aria-hidden="true"></span></button>
                <button class="gallery-thumbnail" type="button" data-gallery-view="detail" aria-label="Show detail view" aria-pressed="false"><span class="thumbnail-art thumbnail-detail product-art-{{ $product['art'] }}" aria-hidden="true"></span></button>
                <button class="gallery-thumbnail" type="button" data-gallery-view="close" aria-label="Show close-up view" aria-pressed="false"><span class="thumbnail-art thumbnail-close product-art-{{ $product['art'] }}" aria-hidden="true"></span></button>
            </div>
            <p class="demo-note">Illustrative demo artwork for storefront preview.</p>
        </section>

        <section class="product-detail-info" aria-labelledby="product-title">
            <p class="eyebrow">{{ $product['category'] }} · DEMO PICK</p>
            <h1 id="product-title">{{ $product['name'] }}</h1>
            <p class="product-tagline detail-tagline">{{ $product['label'] }}</p>
            <p class="product-reference">Reference: {{ $product['reference'] }}</p>
            <p class="product-availability">{{ $product['availability'] }}</p>
            <div class="detail-prices"><strong>৳{{ number_format($product['offer']) }}</strong><del>৳{{ number_format($product['price']) }}</del><span class="discount-label">{{ $product['discount'] }}</span></div>
            <p class="detail-description">{{ $product['description'] }}</p>

            <form class="add-to-cart-form" action="{{ route('cart.items.store', $product['slug']) }}" method="post">
                @csrf
                <label for="quantity">Quantity</label>
                <div class="quantity-control" data-quantity-control>
                    <button type="button" data-quantity-decrement aria-label="Decrease quantity">−</button>
                    <input id="quantity" name="quantity" type="number" value="1" min="1" max="10" required inputmode="numeric">
                    <button type="button" data-quantity-increment aria-label="Increase quantity">+</button>
                </div>
                <button class="button button-dark" type="submit" name="intent" value="cart">Add to cart</button>
                <button class="button button-outline" type="submit" name="intent" value="checkout">Order now</button>
            </form>

            @if ($whatsappNumber)
                <a class="button whatsapp-preview" href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode('Hello, I am interested in '.$product['name'].'.') }}" target="_blank" rel="noopener noreferrer">Order via WhatsApp</a>
            @else
                <p class="whatsapp-note" role="status">WhatsApp ordering is unavailable until the store number is configured.</p>
            @endif

            <div class="product-detail-lists">
                <section aria-labelledby="features-heading"><h2 id="features-heading">Key features</h2><ul>@foreach ($product['features'] as $feature)<li>{{ $feature }}</li>@endforeach</ul></section>
                <section aria-labelledby="box-heading"><h2 id="box-heading">What's in the box</h2><ul>@foreach ($product['contents'] as $content)<li>{{ $content }}</li>@endforeach</ul></section>
            </div>
        </section>
    </div>

    <section class="product-copy-section" aria-labelledby="description-heading">
        <h2 id="description-heading">Description</h2>
        <p>{{ $product['description'] }}</p>
    </section>
    <section class="product-copy-section replacement-policy" aria-labelledby="replacement-heading">
        <h2 id="replacement-heading">Replacement policy</h2>
        <p>{{ $replacementPolicy }}</p>
    </section>

    <section class="trust-section product-trust" aria-label="Shopping information">
        <div class="trust-items">
            <article class="trust-item"><span class="trust-icon" aria-hidden="true">✳</span><h3>100% Original Products</h3><p>Authenticity comes first. Every item is chosen with care.</p></article>
            <article class="trust-item"><span class="trust-icon" aria-hidden="true">⌕</span><h3>Product Checking</h3><p>We check the details, so you can shop with confidence.</p></article>
            <article class="trust-item"><span class="trust-icon" aria-hidden="true">↻</span><h3>Replacement information</h3><p>Review the configurable replacement information above.</p></article>
        </div>
    </section>

    @if ($similarProducts !== [])
        <section class="similar-products" aria-labelledby="similar-products-heading">
            <div class="section-heading"><div><p class="eyebrow">MORE TO DISCOVER</p><h2 id="similar-products-heading">Similar picks<span class="accent-dot">.</span></h2></div><a class="text-link" href="{{ route('home') }}#products">Shop all <span aria-hidden="true">→</span></a></div>
            <div class="product-grid similar-grid">@foreach ($similarProducts as $similarProduct)@include('partials.product-card', ['product' => $similarProduct])@endforeach</div>
            <p class="demo-note">Temporary demo products; details and prices are for preview only.</p>
        </section>
    @else
        <section class="similar-products" aria-labelledby="similar-products-heading"><h2 id="similar-products-heading">Similar picks</h2><p>No similar products are available in this category yet.</p></section>
    @endif
</main>
<script type="application/ld+json">{!! json_encode(['@'.'context' => 'https://schema.org', '@type' => 'Product', 'name' => $product['name'], 'description' => $product['description'], 'category' => $product['category'], 'sku' => $product['reference']], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endsection
