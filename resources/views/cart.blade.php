@extends('layouts.storefront', ['title' => 'Your bag | Soumik', 'metaDescription' => 'Review the temporary demo items in your Soumik bag.'])

@section('content')
<main id="main-content" class="page-shell cart-page">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">Your bag</span>
    </nav>

    <div class="page-title-row">
        <div><p class="eyebrow">YOUR EVERYDAY FINDS</p><h1>Your bag<span class="accent-dot">.</span></h1></div>
        <a class="text-link" href="{{ route('home') }}#products">Continue shopping <span aria-hidden="true">→</span></a>
    </div>

    @if (session('status'))
        <p class="flash-message" role="status">{{ session('status') }}</p>
    @endif
    @if ($errors->has('quantity'))
        <p class="flash-message" role="alert">{{ $errors->first('quantity') }}</p>
    @endif

    @if ($items === [])
        <section class="empty-cart" aria-labelledby="empty-cart-heading">
            <span class="empty-cart-icon" aria-hidden="true">♡</span>
            <h2 id="empty-cart-heading">Your bag is waiting.</h2>
            <p>There’s nothing in your demo bag just yet. Take a look around and find something you like.</p>
            <a class="button button-dark" href="{{ route('home') }}#products">Continue shopping <span aria-hidden="true">→</span></a>
        </section>
    @else
        <div class="cart-layout">
            <section class="cart-items" aria-label="Items in your bag">
                @foreach ($items as $item)
                    @php($product = $item['product'])
                    <article class="cart-item">
                        <a class="cart-item-art" href="{{ route('products.show', $product['slug']) }}" aria-label="View {{ $product['name'] }} details">
                            @include('partials.product-art', ['product' => $product])
                        </a>
                        <div class="cart-item-details">
                            <p class="product-category">{{ $product['category'] }}</p>
                            <h2><a href="{{ route('products.show', $product['slug']) }}">{{ $product['name'] }}</a></h2>
                            <p class="cart-unit-price">৳{{ number_format($product['offer']) }} <span>each</span>
                                @if ($product['price'] > $product['offer'])
                                    <del>৳{{ number_format($product['price']) }}</del>
                                    <span class="cart-discount">{{ $product['discount'] }}</span>
                                @endif
                            </p>
                            <form class="quantity-form" action="{{ route('cart.items.update', $product['slug']) }}" method="post" data-cart-quantity-form>
                                @csrf
                                @method('PATCH')
                                <label for="quantity-{{ $product['slug'] }}">Quantity</label>
                                <div class="cart-quantity-control" role="group" aria-label="Quantity for {{ $product['name'] }}">
                                    <button class="cart-quantity-button" type="button" data-cart-decrement aria-label="Decrease {{ $product['name'] }} quantity" @disabled($item['quantity'] <= 1)>−</button>
                                    <input id="quantity-{{ $product['slug'] }}" name="quantity" type="number" value="{{ $item['quantity'] }}" min="1" max="10" required inputmode="numeric" data-cart-quantity>
                                    <button class="cart-quantity-button" type="button" data-cart-increment aria-label="Increase {{ $product['name'] }} quantity" @disabled($item['quantity'] >= 10)>+</button>
                                </div>
                                <button class="button-small" type="submit">Update</button>
                            </form>
                            <form action="{{ route('cart.items.destroy', $product['slug']) }}" method="post" class="remove-form">
                                @csrf
                                @method('DELETE')
                                <button class="text-button" type="submit" aria-label="Remove {{ $product['name'] }} from your bag">Remove</button>
                            </form>
                        </div>
                        <strong class="cart-line-total" aria-label="Line subtotal">৳{{ number_format($item['lineTotal']) }}</strong>
                    </article>
                @endforeach
            </section>

            <aside class="order-summary" aria-labelledby="cart-summary-heading">
                <h2 id="cart-summary-heading">Order summary</h2>
                <dl>
                    <div><dt>Items subtotal</dt><dd>৳{{ number_format($subtotal) }}</dd></div>
                    @if ($discountTotal > 0)
                        <div class="summary-savings"><dt>Savings included</dt><dd>−৳{{ number_format($discountTotal) }}</dd></div>
                    @endif
                    <div><dt>Delivery</dt><dd>{{ $deliveryCharge === null ? 'To be confirmed' : '৳'.number_format($deliveryCharge) }}</dd></div>
                    @if ($total !== null)
                        <div class="summary-total"><dt>Current total</dt><dd>৳{{ number_format($total) }}</dd></div>
                    @endif
                </dl>
                <p class="summary-note">Demo prices only. Delivery will be confirmed before any order is placed.</p>
                <a class="button button-dark checkout-button" href="{{ route('checkout.show') }}">Proceed to checkout <span aria-hidden="true">→</span></a>
                <a class="continue-link" href="{{ route('home') }}#products">Continue shopping</a>
            </aside>
        </div>
    @endif

    <p class="demo-note">This bag is stored temporarily in your session. It is not a real order.</p>
</main>
@endsection
