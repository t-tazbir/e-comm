@extends('layouts.storefront', ['title' => 'Order placement unavailable | Soumik', 'metaDescription' => 'Order placement is not enabled in this Soumik development phase.'])

@section('content')
<main id="main-content" class="page-shell confirmation-page">
    <section class="confirmation-card" aria-labelledby="confirmation-heading">
        <span class="confirmation-mark" aria-hidden="true">!</span>
        <p class="eyebrow">CHECKOUT STATUS</p>
        <h1 id="confirmation-heading">Order placement isn’t enabled yet.</h1>
        <p>No order was placed, and no confirmation number was created.</p>
        <a class="button button-dark" href="{{ route('checkout.show') }}">Return to checkout <span aria-hidden="true">→</span></a>
        <a class="continue-link" href="{{ route('cart.show') }}">Return to your bag</a>
    </section>
</main>
@endsection
