@extends('layouts.storefront', ['title' => 'Checkout | Soumik', 'metaDescription' => 'Review your Soumik bag and delivery details for a Cash on Delivery checkout preview.'])

@section('content')
<main id="main-content" class="page-shell checkout-page">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span>
        <a href="{{ route('cart.show') }}">Your bag</a><span aria-hidden="true">/</span>
        <span aria-current="page">Checkout</span>
    </nav>

    <div class="page-title-row">
        <div><p class="eyebrow">A FEW DETAILS TO GO</p><h1>Delivery details<span class="accent-dot">.</span></h1></div>
    </div>

    @if ($items === [])
        <section class="empty-cart checkout-empty" aria-labelledby="empty-checkout-heading">
            <span class="empty-cart-icon" aria-hidden="true">♡</span>
            <h2 id="empty-checkout-heading">Your bag is empty.</h2>
            <p>Add an item to your bag before continuing to checkout.</p>
            <a class="button button-dark" href="{{ route('home') }}#products">Continue shopping <span aria-hidden="true">→</span></a>
            <a class="continue-link" href="{{ route('cart.show') }}">Return to your bag</a>
        </section>
    @else
        <p class="demo-banner" role="note">Checkout preview only. Order placement is not enabled, and no customer details or order will be saved.</p>
        @if ($checkoutNotice)
            <p class="checkout-notice" role="status">{{ $checkoutNotice }}</p>
        @endif

        <div class="checkout-layout">
            <form class="checkout-form" action="{{ route('checkout.store') }}" method="post">
                @csrf
                @if ($fieldErrors->any())
                    <div class="form-error-summary" role="alert" tabindex="-1">
                        <h2>Please check these details</h2>
                        <ul>@foreach ($fieldErrors->keys() as $field)<li><a href="#{{ $field }}">{{ $fieldErrors->first($field) }}</a></li>@endforeach</ul>
                    </div>
                @endif

                <fieldset class="form-section">
                    <legend>Who’s receiving the order?</legend>
                    <div class="form-field">
                        <label for="customer_name">Full name <span aria-hidden="true">*</span></label>
                        <input id="customer_name" name="customer_name" type="text" autocomplete="name" maxlength="120" value="{{ $checkoutValues['customer_name'] ?? '' }}" required @if ($fieldErrors->has('customer_name')) aria-invalid="true" aria-describedby="customer_name-error" @endif>
                        @if ($fieldErrors->has('customer_name'))<p class="field-error" id="customer_name-error">{{ $fieldErrors->first('customer_name') }}</p>@endif
                    </div>
                    <div class="form-field">
                        <label for="mobile">Mobile number <span aria-hidden="true">*</span></label>
                        <input id="mobile" name="mobile" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" placeholder="e.g. 01XXXXXXXXX or +880..." value="{{ $checkoutValues['mobile'] ?? '' }}" required @if ($fieldErrors->has('mobile')) aria-invalid="true" aria-describedby="mobile-error" @endif>
                        @if ($fieldErrors->has('mobile'))<p class="field-error" id="mobile-error">{{ $fieldErrors->first('mobile') }}</p>@endif
                    </div>
                    <div class="form-field">
                        <label for="email">Email address <span>(optional)</span></label>
                        <input id="email" name="email" type="email" autocomplete="email" maxlength="254" value="{{ $checkoutValues['email'] ?? '' }}" @if ($fieldErrors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
                        @if ($fieldErrors->has('email'))<p class="field-error" id="email-error">{{ $fieldErrors->first('email') }}</p>@endif
                    </div>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Where should we deliver?</legend>
                    <div class="form-field">
                        <label for="delivery_address">Full delivery address <span aria-hidden="true">*</span></label>
                        <textarea id="delivery_address" name="delivery_address" rows="3" maxlength="500" autocomplete="street-address" required @if ($fieldErrors->has('delivery_address')) aria-invalid="true" aria-describedby="delivery_address-error" @endif>{{ $checkoutValues['delivery_address'] ?? '' }}</textarea>
                        @if ($fieldErrors->has('delivery_address'))<p class="field-error" id="delivery_address-error">{{ $fieldErrors->first('delivery_address') }}</p>@endif
                    </div>
                    <div class="form-two-columns">
                        <div class="form-field">
                            <label for="area">Area or locality <span aria-hidden="true">*</span></label>
                            <input id="area" name="area" type="text" autocomplete="address-level3" maxlength="120" value="{{ $checkoutValues['area'] ?? '' }}" required @if ($fieldErrors->has('area')) aria-invalid="true" aria-describedby="area-error" @endif>
                            @if ($fieldErrors->has('area'))<p class="field-error" id="area-error">{{ $fieldErrors->first('area') }}</p>@endif
                        </div>
                        <div class="form-field">
                            <label for="district">District or city <span aria-hidden="true">*</span></label>
                            <input id="district" name="district" type="text" autocomplete="address-level2" maxlength="120" value="{{ $checkoutValues['district'] ?? '' }}" required @if ($fieldErrors->has('district')) aria-invalid="true" aria-describedby="district-error" @endif>
                            @if ($fieldErrors->has('district'))<p class="field-error" id="district-error">{{ $fieldErrors->first('district') }}</p>@endif
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="customer_note">Delivery instructions <span>(optional)</span></label>
                        <textarea id="customer_note" name="customer_note" rows="3" maxlength="1000" @if ($fieldErrors->has('customer_note')) aria-invalid="true" aria-describedby="customer_note-error" @endif>{{ $checkoutValues['customer_note'] ?? '' }}</textarea>
                        @if ($fieldErrors->has('customer_note'))<p class="field-error" id="customer_note-error">{{ $fieldErrors->first('customer_note') }}</p>@endif
                    </div>
                </fieldset>

                <fieldset class="payment-choice">
                    <legend>Payment method</legend>
                    <label class="payment-choice-content" for="payment-cod">
                        <input id="payment-cod" type="radio" checked disabled>
                        <span><strong>Cash on Delivery</strong><span>Only payment method for this checkout preview.</span></span>
                    </label>
                </fieldset>

                <button class="button button-dark place-demo-order" type="submit">Place COD order <span aria-hidden="true">→</span></button>
                <p class="form-footnote">This button validates the form and displays the current order-placement status. It does not create an order.</p>
                <a class="continue-link checkout-back-link" href="{{ route('cart.show') }}">Return to your bag</a>
            </form>

            <aside class="order-summary checkout-summary" aria-labelledby="checkout-summary-heading">
                <h2 id="checkout-summary-heading">In your bag</h2>
                <ul class="checkout-items">
                    @foreach ($items as $item)
                        <li class="checkout-item">
                            <a class="checkout-item-art" href="{{ route('products.show', $item['product']['slug']) }}" aria-label="View {{ $item['product']['name'] }} details">
                                @include('partials.product-art', ['product' => $item['product']])
                            </a>
                            <div class="checkout-item-info">
                                <a class="checkout-item-name" href="{{ route('products.show', $item['product']['slug']) }}">{{ $item['product']['name'] }}</a>
                                <span>{{ $item['quantity'] }} × ৳{{ number_format($item['product']['offer']) }} each</span>
                            </div>
                            <strong>৳{{ number_format($item['lineTotal']) }}</strong>
                        </li>
                    @endforeach
                </ul>
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
                <a class="continue-link" href="{{ route('cart.show') }}">Edit your bag</a>
            </aside>
        </div>
    @endif
</main>
@endsection
