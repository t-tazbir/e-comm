<?php

namespace App\Http\Controllers;

use App\Support\DemoCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

class StorefrontController extends Controller
{
    public function home(Request $request): View
    {
        return view('home', [
            'products' => DemoCatalog::all(),
            'flashProducts' => DemoCatalog::flashSales(),
            'categories' => DemoCatalog::categories(),
            'cartCount' => array_sum($this->cartQuantities($request)),
        ]);
    }

    public function showProduct(Request $request, string $slug): View
    {
        $product = DemoCatalog::find($slug);

        abort_if($product === null, 404);

        $similarProducts = array_values(array_filter(
            DemoCatalog::all(),
            fn (array $candidate): bool => $candidate['category_id'] === $product['category_id'] && $candidate['slug'] !== $product['slug'],
        ));

        return view('products.show', [
            'product' => $product,
            'similarProducts' => array_slice($similarProducts, 0, 4),
            'whatsappNumber' => preg_match('/^\+?[1-9]\d{7,14}$/', (string) config('services.store.whatsapp_number')) === 1
                ? preg_replace('/\D/', '', (string) config('services.store.whatsapp_number'))
                : null,
            'replacementPolicy' => config('services.store.replacement_policy'),
            'cartCount' => array_sum($this->cartQuantities($request)),
        ]);
    }

    public function cart(Request $request): View
    {
        return view('cart', $this->cartViewData($request));
    }

    public function addToCart(Request $request, string $slug): RedirectResponse
    {
        abort_if(DemoCatalog::find($slug) === null, 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'intent' => ['nullable', Rule::in(['cart', 'checkout'])],
        ]);

        $cart = $this->cartQuantities($request);
        $cart[$slug] = min(10, ($cart[$slug] ?? 0) + (int) $validated['quantity']);
        $request->session()->put('cart', $cart);

        if (($validated['intent'] ?? 'cart') === 'checkout') {
            return redirect()->route('checkout.show');
        }

        return redirect()->route('cart.show')->with('status', 'Demo item added to your bag.');
    }

    public function updateCart(Request $request, string $slug): RedirectResponse
    {
        abort_if(DemoCatalog::find($slug) === null, 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $cart = $this->cartQuantities($request);
        $cart[$slug] = (int) $validated['quantity'];
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.show')->with('status', 'Your demo bag was updated.');
    }

    public function removeFromCart(Request $request, string $slug): RedirectResponse
    {
        $cart = $this->cartQuantities($request);
        unset($cart[$slug]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.show')->with('status', 'Item removed from your demo bag.');
    }

    public function checkout(Request $request): View
    {
        $cartData = $this->cartViewData($request);

        return view('checkout', array_merge($cartData, [
            'checkoutValues' => [],
            'fieldErrors' => new MessageBag,
            'checkoutNotice' => null,
        ]));
    }

    public function submitCheckout(Request $request): View|RedirectResponse|Response
    {
        $cartData = $this->cartViewData($request);

        if ($cartData['items'] === []) {
            return redirect()->route('cart.show')->with('status', 'Your demo bag is empty.');
        }

        $rules = [
            'customer_name' => ['required', 'string', 'max:120'],
            'mobile' => ['required', 'string', 'min:7', 'max:20', 'regex:/^[0-9+() -]+$/'],
            'email' => ['nullable', 'email', 'max:254'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'area' => ['required', 'string', 'max:120'],
            'district' => ['required', 'string', 'max:120'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
        ];
        $submittedValues = $request->only(array_keys($rules));
        $checkoutValues = array_map(
            fn (mixed $value): string => is_scalar($value) ? (string) $value : '',
            $submittedValues,
        );
        $validator = Validator::make($submittedValues, $rules, [
            'mobile.regex' => 'Enter a valid mobile number using digits, spaces, +, parentheses, or hyphens.',
        ]);

        if ($validator->fails()) {
            return response()->view('checkout', array_merge($cartData, [
                'checkoutValues' => $checkoutValues,
                'fieldErrors' => $validator->errors(),
                'checkoutNotice' => null,
            ]), 422);
        }

        return view('checkout', array_merge($cartData, [
            'checkoutValues' => $checkoutValues,
            'fieldErrors' => $validator->errors(),
            'checkoutNotice' => 'Order placement is not enabled in this development phase. Your details have not been saved and no order was placed.',
        ]));
    }

    public function orderConfirmation(Request $request): View
    {
        return view('order-confirmation', [
            'cartCount' => array_sum($this->cartQuantities($request)),
        ]);
    }

    /**
     * @return array{items: array<int, array{product: array<string, mixed>, quantity: int, lineTotal: int, discountTotal: int}>, subtotal: int, discountTotal: int, deliveryCharge: int|null, total: int|null, cartCount: int}
     */
    private function cartViewData(Request $request): array
    {
        $items = [];
        $subtotal = 0;
        $discountTotal = 0;

        $cart = $this->cartQuantities($request);

        foreach ($cart as $slug => $quantity) {
            $product = DemoCatalog::find($slug);
            $lineTotal = $product['offer'] * $quantity;
            $lineDiscount = max(0, $product['price'] - $product['offer']) * $quantity;
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'lineTotal' => $lineTotal,
                'discountTotal' => $lineDiscount,
            ];
            $subtotal += $lineTotal;
            $discountTotal += $lineDiscount;
        }

        $configuredDeliveryCharge = filter_var(config('services.store.delivery_charge'), FILTER_VALIDATE_INT);
        $deliveryCharge = $configuredDeliveryCharge !== false && $configuredDeliveryCharge >= 0
            ? $configuredDeliveryCharge
            : null;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'discountTotal' => $discountTotal,
            'deliveryCharge' => $deliveryCharge,
            'total' => $deliveryCharge === null ? null : $subtotal + $deliveryCharge,
            'cartCount' => array_sum($cart),
        ];
    }

    /**
     * Keep only valid session cart lines for currently available demo products.
     *
     * @return array<string, int>
     */
    private function cartQuantities(Request $request): array
    {
        $sessionCart = $request->session()->get('cart', []);
        $sessionCart = is_array($sessionCart) ? $sessionCart : [];
        $cart = [];

        foreach ($sessionCart as $slug => $quantity) {
            if (! is_string($slug) || DemoCatalog::find($slug) === null || ! is_int($quantity) || $quantity < 1 || $quantity > 10) {
                continue;
            }

            $cart[$slug] = $quantity;
        }

        if ($cart !== $sessionCart) {
            $request->session()->put('cart', $cart);
        }

        return $cart;
    }
}
