<?php

test('checkout route renders a cart-backed order summary and only cash on delivery', function () {
    config(['services.store.delivery_charge' => 100]);
    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 2]);

    $response = $this->get(route('checkout.show'));

    $response->assertOk()
        ->assertSee('Daily Glow Serum')
        ->assertSee('2 × ৳1,190 each')
        ->assertSee('৳2,380')
        ->assertSee('Savings included')
        ->assertSee('৳520')
        ->assertSee('৳100')
        ->assertSee('৳2,480')
        ->assertSee('Cash on Delivery')
        ->assertSee('Place COD order')
        ->assertSee(route('cart.show'), false)
        ->assertSee('Decorative placeholder for Daily Glow Serum', false);

    expect(substr_count($response->getContent(), 'type="radio"'))->toBe(1);
});

test('checkout with an empty cart shows a continue-shopping state and no order form', function () {
    $this->get(route('checkout.show'))
        ->assertOk()
        ->assertSee('Your bag is empty.')
        ->assertSee(route('home').'#products', false)
        ->assertDontSee('Place COD order');

    $this->post(route('checkout.store'), [])->assertRedirect(route('cart.show'));
});

test('checkout summary uses the configured delivery fee and otherwise leaves it pending', function () {
    $this->post(route('cart.items.store', 'everyday-wireless-buds'), ['quantity' => 1]);

    $this->get(route('checkout.show'))
        ->assertOk()
        ->assertSee('To be confirmed')
        ->assertDontSee('Current total');
});

test('checkout validates required customer fields without persisting the submitted details', function () {
    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 1]);

    $response = $this->post(route('checkout.store'), []);

    $response->assertUnprocessable()
        ->assertSee('Please check these details')
        ->assertSee('The customer name field is required.')
        ->assertSee('The mobile field is required.')
        ->assertSee('The delivery address field is required.')
        ->assertSee('The area field is required.')
        ->assertSee('The district field is required.')
        ->assertSee('aria-invalid="true"', false);

    $this->assertFalse(session()->has('_old_input'));
});

test('valid checkout details show a clear unavailable state without creating an order', function () {
    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 1]);

    $response = $this->post(route('checkout.store'), [
        'customer_name' => 'Demo Customer',
        'mobile' => '+8801712345678',
        'email' => 'demo@example.test',
        'delivery_address' => 'Demo delivery address',
        'area' => 'Demo area',
        'district' => 'Demo district',
        'customer_note' => 'Please call on arrival.',
        'price' => 1,
        'discount' => 999999,
        'subtotal' => 1,
        'total' => 1,
    ]);

    $response->assertOk()
        ->assertSee('Order placement is not enabled in this development phase.')
        ->assertSee('Your details have not been saved and no order was placed.')
        ->assertSee('৳1,190')
        ->assertSee('৳260')
        ->assertSee('value="+8801712345678"', false)
        ->assertSee('value="demo@example.test"', false)
        ->assertDontSee('Your details passed validation.')
        ->assertDontSee('DEMO CHECKOUT COMPLETE');

    $this->assertFalse(session()->has('demo_checkout_validated'));
    $this->assertFalse(session()->has('_old_input'));
});

test('a product can proceed through the existing cart link to checkout', function () {
    $this->get(route('products.show', 'everyday-wireless-buds'))
        ->assertOk()
        ->assertSee(route('cart.items.store', 'everyday-wireless-buds'), false);

    $this->post(route('cart.items.store', 'everyday-wireless-buds'), ['quantity' => 2])
        ->assertRedirect(route('cart.show'));

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee(route('checkout.show'), false);

    $this->get(route('checkout.show'))
        ->assertOk()
        ->assertSee('Everyday Wireless Buds')
        ->assertSee('2 × ৳2,390 each');
});

test('the legacy confirmation route cannot imply an order was placed', function () {
    $this->get(route('order.confirmation'))
        ->assertOk()
        ->assertSee('Order placement isn’t enabled yet.')
        ->assertSee('No order was placed')
        ->assertDontSee('Your order is confirmed.');
});
