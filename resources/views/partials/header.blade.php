<a class="skip-link" href="#main-content">Skip to main content</a>
<div class="announcement">A little something good for your everyday <span aria-hidden="true">✳</span></div>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="Soumik home">soumik<span>.</span></a>
        <form class="header-search" role="search" action="{{ route('home') }}#products" method="get">
            <label class="sr-only" for="site-search">Search products</label>
            <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg>
            <input id="site-search" name="q" type="search" placeholder="What are you looking for?" value="{{ request('q') }}" autocomplete="off">
            <button type="submit" aria-label="Search">Search</button>
        </form>
        <a class="cart-link" href="{{ route('cart.show') }}" aria-label="Shopping bag, {{ $cartCount }} {{ $cartCount === 1 ? 'item' : 'items' }}">
            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 8h14l1 12H4L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg>
            <span class="cart-count" aria-hidden="true">{{ $cartCount }}</span><span class="cart-label">Bag</span>
        </a>
        {{-- <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-nav" aria-label="Open navigation menu"><span aria-hidden="true"></span><span aria-hidden="true"></span></button> --}}
    </div>
    {{-- Temporarily paused homepage navigation options. --}}
    {{-- <nav class="main-nav" aria-label="Main navigation">
        <a href="{{ route('home') }}#flash-sales">Flash sales</a><a href="{{ route('home') }}#products">Shop all</a><a href="{{ route('home') }}#trust">Why Soumik</a>
    </nav> --}}
    {{-- Mobile navigation menu temporarily disabled. --}}
    {{-- <nav class="mobile-nav" id="mobile-nav" aria-label="Mobile navigation" aria-hidden="true" inert hidden>
        <a href="{{ route('home') }}#flash-sales">Flash sales</a><a href="{{ route('home') }}#products">Shop all</a><a href="{{ route('cart.show') }}">Bag</a><a href="{{ route('home') }}#trust">Why Soumik</a>
    </nav> --}}
</header>
