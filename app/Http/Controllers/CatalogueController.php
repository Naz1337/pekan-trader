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

        return redirect()->route('catalogue.show', compact('product'))
            ->with('toast', ['type'=>'success', 'message' => 'saw some snow rock hard']);
    }

    public function show_cart(Request $request)
    {
        $user = $request->user();

        if (!$user->cart) {
            return redirect()->route('home')->with('toast', ['type' => 'info', 'message' => 'Your cart is empty.']);
        }

        $products = $user->cart->products()->withPivot('quantity')->get();

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

        return view('checkout.show', compact('groupedProducts'));
    }

    public function place_order(Request $request)
    {
        $user = $request->user();

        if (!$user->cart || $user->cart->products->isEmpty()) {
            return redirect()->route('cart.show')->with('toast', ['type' => 'info', 'message' => 'Your cart is empty.']);
        }

        $request->validate([
            'payment_method' => 'required|in:bank_transfer',
        ]);

        $groupedProducts = $user->cart->products->groupBy('seller_id');

        foreach ($groupedProducts as $sellerId => $products) {
            $order = $user->orders()->create([
                'seller_id' => $sellerId,
                'payment_method' => $request->input('payment_method'),
                'total_amount' => $products->sum(fn($product) => $product->price * $product->pivot->quantity),
            ]);

            foreach ($products as $product) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->price,
                ]);
            }
        }

        // Clear the cart
        $user->cart->products()->detach();

        return redirect()->route('home')->with('toast', ['type' => 'success', 'message' => 'Order placed successfully!']);
    }
}
