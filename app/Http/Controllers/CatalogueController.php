<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    function home(Request $request)
    {
        $products = Product::all();
        return view('welcome', compact('products'));
    }

    function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function add_to_cart(Request $request, Product $product)
    {
        $user = $request->user();
        if (!$user->has('cart')) {
            $cart = new Cart;

            $user->cart()->associate($cart);
            $user->save();
        }

        $existingProduct = $user->cart->products()->wherePivot('product_id', $product->id)->first();

        if ($existingProduct) {
            $user->cart->products()->updateExistingPivot($product->id, [
                'quantity' => $existingProduct->pivot->quantity + $request->input('quantity')
            ]);
        } else {
            $user->cart->products()->attach($product->id, ['quantity' => $request->input('quantity')]);
        }

        return redirect()->route('cart.show')
            ->with('toast', ['type'=>'success', 'message' => 'Successfully added product to your cart.']);
    }

    public function show_cart(Request $request)
    {
        $user = $request->user();

        if ($user->cart) {
            $products = $user->cart->products()->withPivot('quantity')->get();
        } else {
            $products = collect(); // Ensure $products is a collection
        }

        return view('cart.show', compact('products'));
    }

    public function remove_from_cart(Request $request, Product $product)
    {
        $user = $request->user();
        $user->cart->products()->detach($product->id);

        return redirect()->route('cart.show')->with('toast', ['type' => 'success', 'message' => 'Product removed from cart.']);
    }

    public function show_checkout(Request $request)
    {
        $user = $request->user();

        if (!$user->cart || $user->cart->products->isEmpty()) {
            return redirect()->route('cart.show')->with('toast', ['type' => 'info', 'message' => 'Your cart is empty.']);
        }

        $groupedProducts = $user->cart->products->groupBy('seller_id')->map(function ($products) {
            $totalAmount = $products->sum(fn($product) => $product->price * $product->pivot->quantity);
            return [
                'products' => $products,
                'total_amount' => $totalAmount,
            ];
        });

        $addresses = $user->addresses; // Retrieve the user's addresses

        return view('checkout.show', compact('groupedProducts', 'addresses'));
    }

    public function place_order(Request $request)
    {
        $user = $request->user();

        if (!$user->cart || $user->cart->products->isEmpty()) {
            return redirect()->route('cart.show')->with('toast', ['type' => 'info', 'message' => 'Your cart is empty.']);
        }

        $request->validate([
            'payment_method' => 'required|in:bank_transfer',
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        // Create a new address for every order and handle 'remember_address'
        $address = $user->addresses()->create([
            'address_line_1' => $request->input('address_line_1'),
            'address_line_2' => $request->input('address_line_2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'postal_code' => $request->input('postal_code'),
            'country' => $request->input('country'),
            'phone_number' => $request->input('phone_number'),
            'is_default' => $request->has('remember_address'), // Set to true if 'remember_address' is checked
        ]);

        $groupedProducts = $user->cart->products->groupBy('seller_id');

        // 1. Check stock for all products
        foreach ($user->cart->products as $product) {
            if ($product->stock_quantity < $product->pivot->quantity) {
                return redirect()->route('cart.show')->with('toast', [
                    'type' => 'error',
                    'message' => "Not enough stock for {$product->name}. Available: {$product->stock_quantity}, Requested: {$product->pivot->quantity}"
                ]);
            }
        }

        // 2. Place orders and allocate stock
        foreach ($groupedProducts as $sellerId => $products) {
            $order = $user->orders()->create([
                'seller_id' => $sellerId,
                'payment_method' => $request->input('payment_method'),
                'total_amount' => $products->sum(fn($product) => $product->price * $product->pivot->quantity),
                'address_id' => $address ? $address->id : null,
            ]);

            foreach ($products as $product) {
                // Decrement stock
                $product->decrement('stock_quantity', $product->pivot->quantity);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->price,
                ]);
            }
        }

        // Clear the cart
        $user->cart->products()->detach();

        return redirect()->route('orders.show', ['order' => $order->id])->with('toast', ['type' => 'success', 'message' => 'Order placed successfully!']);
    }
}
