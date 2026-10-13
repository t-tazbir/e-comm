<?php

test('homepage renders the category bar, two-slide hero, product sections and trust section in order', function () {
    $response = $this->get(route('home'));
    $html = $response->getContent();

    $response->assertOk()
        ->assertSee('All Categories')
        ->assertSee('Beauty &amp; Health', false)
        ->assertSee('Food')
        ->assertSee('Electronics')
        ->assertSee('Watch')
        ->assertSee('Suitcase/Trolley')
        ->assertSee('Perfume')
        ->assertSee('Flash sales')
        ->assertSee('The good stuff')
        ->assertSee('100% Original Products')
        ->assertSee('Product Checking')
        ->assertSee('3 Days Replacement Guarantee')
        ->assertSee('data-slide="0"', false)
        ->assertSee('data-slide="1"', false);

    expect(strpos($html, 'category-bar'))->toBeLessThan(strpos($html, 'data-carousel'))
        ->and(strpos($html, 'data-carousel'))->toBeLessThan(strpos($html, 'id="flash-sales"'))
        ->and(strpos($html, 'id="flash-sales"'))->toBeLessThan(strpos($html, 'id="products"'))
        ->and(strpos($html, 'id="products"'))->toBeLessThan(strpos($html, 'id="trust"'));
});

test('both homepage product sections include products from every demo category', function () {
    $html = $this->get(route('home'))->getContent();
    $flashStart = strpos($html, 'data-product-grid="flash"');
    $allStart = strpos($html, 'data-product-grid="all"');
    $flashGrid = substr($html, $flashStart, $allStart - $flashStart);
    $allGrid = substr($html, $allStart);

    foreach (['beauty-health', 'food', 'electronics', 'watch', 'suitcase-trolley', 'perfume'] as $categoryId) {
        expect($flashGrid)->toContain('data-category-id="'.$categoryId.'"');
        expect($allGrid)->toContain('data-category-id="'.$categoryId.'"');
    }
});

test('paused homepage navigation leaves the brand search and cart controls available', function () {
    $html = $this->get(route('home'))->getContent();
    $headerStart = strpos($html, '<header class="site-header">');
    $headerEnd = strpos($html, '</header>', $headerStart);
    $header = substr($html, $headerStart, $headerEnd - $headerStart);

    expect($header)->toContain('class="brand"')
        ->and($header)->toContain('class="header-search"')
        ->and($header)->toContain('class="cart-link"')
        ->and($header)->not->toContain('class="menu-toggle"')
        ->and($header)->not->toContain('class="main-nav"')
        ->and($header)->not->toContain('class="mobile-nav"');
});

test('the product section has no search input while the header search and category filters remain', function () {
    $html = $this->get(route('home'))->getContent();

    expect($html)->toContain('id="site-search"')
        ->and($html)->not->toContain('id="product-search"')
        ->and($html)->toContain('data-category-filter="all"')
        ->and($html)->toContain('data-category-filter="beauty-health"');
});
