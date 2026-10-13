<?php

use App\Support\DemoCatalog;

test('customer storefront pages render and homepage product cards link to details', function () {
    $productResponse = $this->get(route('products.show', 'daily-glow-serum'));
    $cartResponse = $this->get(route('cart.show'));
    $confirmationResponse = $this->get(route('order.confirmation'));
    $homeResponse = $this->get(route('home'));

    $productResponse->assertOk()
        ->assertSee('Daily Glow Serum')
        ->assertSee('Key features')
        ->assertSee("What's in the box", false)
        ->assertSee('Replacement policy')
        ->assertSee('DEMO-DAILY-GLOW-SERUM')
        ->assertSee('Demo availability not tracked')
        ->assertSee('Add to cart')
        ->assertSee('Order now')
        ->assertSee('WhatsApp ordering is unavailable until the store number is configured.')
        ->assertSee('application/ld+json', false)
        ->assertSee(route('products.show', 'daily-glow-serum'), false);
    $cartResponse->assertOk()->assertSee('Your bag is waiting.');
    $confirmationResponse->assertOk()->assertSee('No order was placed');
    $homeResponse->assertOk();

    foreach (DemoCatalog::all() as $product) {
        $homeResponse->assertSee(route('products.show', $product['slug']), false);
    }
});

test('product detail uses the configured WhatsApp number and replacement wording', function () {
    config([
        'services.store.whatsapp_number' => '+8801700000000',
        'services.store.replacement_policy' => 'Demo replacement wording for this test.',
    ]);

    $this->get(route('products.show', 'everyday-wireless-buds'))
        ->assertOk()
        ->assertSee('https://wa.me/8801700000000?', false)
        ->assertSee('Demo replacement wording for this test.');
});

test('demo cart calculates totals from catalog prices and supports quantity updates', function () {
    config(['services.store.delivery_charge' => 120]);

    $this->post(route('cart.items.store', 'daily-glow-serum'), [
        'quantity' => 2,
        'intent' => 'cart',
        'price' => 1,
    ])->assertRedirect(route('cart.show'));

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee('৳2,380')
        ->assertSee('৳120')
        ->assertSee('৳2,500');

    $this->patch(route('cart.items.update', 'daily-glow-serum'), ['quantity' => 3])
        ->assertRedirect(route('cart.show'));

    $this->get(route('cart.show'))->assertSee('৳3,570')->assertSee('৳3,690');

    $this->delete(route('cart.items.destroy', 'daily-glow-serum'))->assertRedirect(route('cart.show'));

    $this->get(route('cart.show'))->assertSee('Your bag is waiting.');

    $this->post(route('cart.items.store', 'daily-glow-serum'), [
        'quantity' => 1,
        'intent' => 'checkout',
    ])->assertRedirect(route('checkout.show'));
});

test('checkout validation does not claim to confirm a demo order', function () {
    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 1])
        ->assertRedirect(route('cart.show'));

    $this->get(route('checkout.show'))->assertOk()->assertSee('Cash on Delivery');

    $response = $this->post(route('checkout.store'), []);
    $response->assertUnprocessable()
        ->assertSee('The customer name field is required.')
        ->assertSee('The mobile field is required.')
        ->assertSee('The delivery address field is required.')
        ->assertSee('The area field is required.')
        ->assertSee('The district field is required.');

    $this->post(route('checkout.store'), [
        'customer_name' => 'Demo Customer',
        'mobile' => '01700000000',
        'delivery_address' => 'Demo delivery address',
        'area' => 'Demo area',
        'district' => 'Demo district',
        'customer_note' => 'Please call on arrival.',
    ])->assertOk()
        ->assertSee('Order placement is not enabled in this development phase.')
        ->assertSee('Your details have not been saved and no order was placed.')
        ->assertDontSee('Your details passed validation.');

    $this->get(route('order.confirmation'))
        ->assertOk()
        ->assertSee('Order placement isn’t enabled yet.')
        ->assertSee('No order was placed');
});

test('unknown demo products return not found and invalid cart quantities are rejected', function () {
    $this->get(route('products.show', 'not-a-product'))->assertNotFound();

    $response = $this->from(route('products.show', 'daily-glow-serum'))
        ->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 99]);

    $response->assertRedirect(route('products.show', 'daily-glow-serum'))
        ->assertSessionHasErrors('quantity');
});

test('product page quantity submission respects cart quantity boundaries', function () {
    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 1])
        ->assertRedirect(route('cart.show'));

    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 10])
        ->assertRedirect(route('cart.show'));

    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 0])
        ->assertSessionHasErrors('quantity');

    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 11])
        ->assertSessionHasErrors('quantity');
});
