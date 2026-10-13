<?php

test('cart page shows an empty state and a link back to shopping', function () {
    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee('Your bag is waiting.')
        ->assertSee(route('home').'#products', false)
        ->assertDontSee(route('checkout.show'));
});

test('adding the same product twice merges quantities and uses catalog pricing', function () {
    config(['services.store.delivery_charge' => null]);

    $this->get(route('products.show', 'daily-glow-serum'))
        ->assertOk()
        ->assertSee(route('cart.items.store', 'daily-glow-serum'), false);

    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 1, 'price' => 1])
        ->assertRedirect(route('cart.show'));
    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 1, 'price' => 999999])
        ->assertRedirect(route('cart.show'))
        ->assertSessionHas('cart', ['daily-glow-serum' => 2]);

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee('Daily Glow Serum')
        ->assertSee(route('products.show', 'daily-glow-serum'), false)
        ->assertSee('Decrease Daily Glow Serum quantity', false)
        ->assertSee('Increase Daily Glow Serum quantity', false)
        ->assertSee("\u{09F3}2,380")
        ->assertSee("\u{09F3}520")
        ->assertSee('To be confirmed')
        ->assertDontSee('Current total')
        ->assertSee('Proceed to checkout');
});

test('cart quantity controls submit bounded quantities and refresh the derived subtotal', function () {
    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 2]);

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee('data-cart-decrement', false)
        ->assertSee('data-cart-increment', false);

    $this->patch(route('cart.items.update', 'daily-glow-serum'), ['quantity' => 1])
        ->assertRedirect(route('cart.show'))
        ->assertSessionHas('cart', ['daily-glow-serum' => 1]);

    $this->get(route('cart.show'))->assertSee("\u{09F3}1,190");

    $this->patch(route('cart.items.update', 'daily-glow-serum'), ['quantity' => 0])
        ->assertRedirect(route('cart.show'))
        ->assertSessionHasErrors('quantity')
        ->assertSessionHas('cart', ['daily-glow-serum' => 1]);

    $this->patch(route('cart.items.update', 'daily-glow-serum'), ['quantity' => 11])
        ->assertRedirect(route('cart.show'))
        ->assertSessionHasErrors('quantity');
});

test('cart count stays in sync and removing the final product returns the empty state', function () {
    $this->get(route('products.show', 'everyday-wireless-buds'))->assertOk();
    $this->post(route('cart.items.store', 'everyday-wireless-buds'), ['quantity' => 3]);

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee('Shopping bag, 3 items', false)
        ->assertSee(route('checkout.show'), false);

    $this->delete(route('cart.items.destroy', 'everyday-wireless-buds'))
        ->assertRedirect(route('cart.show'));

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee('Your bag is waiting.')
        ->assertSee('Shopping bag, 0 items', false);
});

test('configured delivery charge is used for the total and savings remain informational', function () {
    config(['services.store.delivery_charge' => 150]);

    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 2]);

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee("\u{09F3}150")
        ->assertSee("\u{09F3}2,530")
        ->assertSee('Savings included')
        ->assertDontSee('To be confirmed');
});

test('unavailable products and invalid session lines are safely rejected or discarded', function () {
    $this->post(route('cart.items.store', 'retired-demo-product'), ['quantity' => 1])
        ->assertNotFound();

    $this->withSession(['cart' => [
        'daily-glow-serum' => 2,
        'retired-demo-product' => 4,
        'roasted-cashews' => 0,
    ]])->get(route('cart.show'))
        ->assertOk()
        ->assertSee('Shopping bag, 2 items', false)
        ->assertSessionHas('cart', ['daily-glow-serum' => 2])
        ->assertDontSee('Retired demo product');

    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 0])
        ->assertRedirect(route('cart.show'))
        ->assertSessionHasErrors('quantity');
});

test('cart checkout link opens the existing demo checkout without claiming an order', function () {
    $this->get(route('products.show', 'daily-glow-serum'))
        ->assertOk()
        ->assertSee(route('cart.items.store', 'daily-glow-serum'), false);

    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 1]);

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee(route('checkout.show'), false);

    $this->get(route('checkout.show'))
        ->assertOk()
        ->assertSee('Checkout preview only. Order placement is not enabled');
});

test('product detail and cart header show the current session item count', function () {
    $this->post(route('cart.items.store', 'daily-glow-serum'), ['quantity' => 2]);

    $this->get(route('products.show', 'daily-glow-serum'))
        ->assertOk()
        ->assertSee('Shopping bag, 2 items', false);
});
