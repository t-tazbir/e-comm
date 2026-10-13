@extends('layouts.storefront', ['title' => 'Soumik — thoughtful finds for everyday', 'metaDescription' => 'Shop a considered mix of health and beauty, perfume, electronics, and bags at Soumik. Browse temporary demo picks and discover your next everyday essential.'])

@section('content')
<nav class="category-bar" aria-label="Shop by category">
    <div class="category-bar-track category-bar-inner">
        <div class="category-bar-scroll" role="group" aria-label="Filter products by category">
            <button class="category-button is-selected" type="button" data-category-filter="all" aria-pressed="true">All Categories</button>
            @foreach ($categories as $category)
                <button class="category-button" type="button" data-category-filter="{{ $category['id'] }}" aria-pressed="false">{{ $category['name'] }}</button>
            @endforeach
        </div>
    </div>
</nav>
<main id="main-content">
    <section class="hero" aria-label="Featured promotions" data-carousel>
        <div class="hero-slides" aria-live="polite">
            <article class="hero-slide hero-slide-one is-active" data-slide="0" aria-hidden="false">
                <div class="hero-copy"><p class="eyebrow">THE EVERYDAY EDIT</p><h1>Good things,<br><em>beautifully</em> chosen.</h1><p class="hero-description">Thoughtful finds for the little rituals and big days that make life yours.</p><a class="button button-dark" href="#products">Explore the edit <span aria-hidden="true">↗</span></a></div>
                <div class="hero-art hero-art-one" aria-hidden="true"><span class="hero-orbit"></span><span class="hero-sun"></span><span class="hero-vase"></span><span class="hero-leaf leaf-one"></span><span class="hero-leaf leaf-two"></span><span class="hero-art-caption">A softer kind of everyday</span></div>
                <span class="slide-number">01 <span>/ 02</span></span>
            </article>
            <article class="hero-slide hero-slide-two" data-slide="1" aria-hidden="true" inert>
                <div class="hero-copy"><p class="eyebrow">LITTLE DISCOVERIES</p><h2>Find your new<br><em>favourite</em> thing.</h2><p class="hero-description">A considered collection of essentials, picked to bring a little more joy to your day.</p><a class="button button-dark" href="#flash-sales">See what’s on offer <span aria-hidden="true">↗</span></a></div>
                <div class="hero-art hero-art-two" aria-hidden="true"><span class="hero-orbit"></span><span class="hero-sun"></span><span class="hero-vase"></span><span class="hero-leaf leaf-one"></span><span class="hero-leaf leaf-two"></span><span class="hero-art-caption">Your next little discovery</span></div>
                <span class="slide-number">02 <span>/ 02</span></span>
            </article>
        </div>
        <div class="carousel-controls"><button class="carousel-arrow" type="button" data-previous aria-label="Previous promotion">←</button><div class="carousel-dots" role="group" aria-label="Choose promotion"><button type="button" data-indicator="0" aria-label="Show promotion 1" aria-current="true"></button><button type="button" data-indicator="1" aria-label="Show promotion 2" aria-current="false"></button></div><button class="carousel-arrow" type="button" data-next aria-label="Next promotion">→</button></div>
    </section>

    <section class="product-section flash-section" id="flash-sales" aria-labelledby="flash-heading">
        <div class="section-heading"><div><p class="eyebrow">GOOD FINDS, BETTER PRICES</p><h2 id="flash-heading">Flash sales<span class="accent-dot">.</span></h2></div><a class="text-link" href="#products">Shop all products <span aria-hidden="true">↗</span></a></div>
        <div class="product-grid flash-grid" data-product-grid="flash">
            @foreach ($flashProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <p class="flash-empty-state" data-flash-empty role="status" hidden>No flash-sale demo products match this category and search.</p>
        <p class="demo-note">Demo selection for preview only. Product details and offers are temporary.</p>
    </section>

    <section class="product-section all-section" id="products" aria-labelledby="products-heading">
        <div class="section-heading all-heading"><div><p class="eyebrow">A LITTLE OF EVERYTHING</p><h2 id="products-heading">The good stuff<span class="accent-dot">.</span></h2></div><p class="section-aside">Considered picks for wherever<br>your everyday takes you.</p></div>
        <p class="result-count" aria-live="polite"></p>
        <div class="product-grid all-product-grid" data-product-grid="all">
            @foreach ($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <p class="empty-state" data-empty-state hidden>No demo products match your filters. Try another search or category.</p>
        <p class="demo-note">These are temporary demo products created to preview the storefront. They are not available to purchase.</p>
    </section>

    <section class="trust-section" id="trust" aria-labelledby="trust-heading">
        <div class="trust-intro"><p class="eyebrow">SHOP WITH A LITTLE MORE EASE</p><h2 id="trust-heading">Good choices.<br><em>Good to know.</em></h2></div>
        <div class="trust-items">
            <article class="trust-item"><span class="trust-icon" aria-hidden="true">✳</span><h3>100% Original Products</h3><p>Authenticity comes first. Every item is chosen with care.</p></article>
            <article class="trust-item"><span class="trust-icon" aria-hidden="true">⌕</span><h3>Product Checking</h3><p>We check the details, so you can shop with confidence.</p></article>
            <article class="trust-item"><span class="trust-icon" aria-hidden="true">↻</span><h3>3 Days Replacement Guarantee</h3><p>Something not right? Get in touch within three days.</p></article>
        </div>
    </section>
</main>

@endsection
