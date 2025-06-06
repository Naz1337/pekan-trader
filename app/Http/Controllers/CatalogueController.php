<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    public function home(Request $request)
    {
        $query = $request->input('query');
        $categoryName = $request->input('category'); // Get category name from request

        $productsQuery = Product::query(); // Start with a base Eloquent query

        if ($categoryName) {
            // Find the category model by name
            $category = \App\Models\ProductCategory::where('name', $categoryName)->first();
            if ($category) {
                // If category found, filter products by its ID
                $productsQuery->where('product_category_id', $category->id);
            }
            // Optional: Handle case where categoryName is provided but not found (e.g., log, ignore, or show error)
        }

        if ($query) {
            $productsQuery->where('name', 'LIKE', '%' . $query . '%');
        }

        // Conditionally apply ordering: random for homepage, latest for search/category results
        if ($query || $categoryName) {
            $products = $productsQuery->latest()->paginate(12); // Order by latest for search/category
        } else {
            $products = $productsQuery->inRandomOrder()->paginate(12); // Random order for homepage
        }

        // Pass all relevant data to the view
        // The view should be consistent, e.g., 'catalogue.search-result' for any filtering
        // or 'welcome' if no filters are applied.
        // Given the original logic, if $query or $categoryName is present, it's a search.
        if ($query || $categoryName) {
            return view('catalogue.search-result', compact('products', 'query', 'categoryName'));
        }

        // If no query and no category, show the default 'welcome' view with all products (or paginated)
        // The original code did Product::all(). We'll use the paginated query for consistency.
        return view('welcome', compact('products', 'query', 'categoryName'));
    }

    public function show(Product $product)
    {
        $product->load('attributes.productAttributeKey');
        return view('products.show', compact('product'));
    }

    public function add_to_cart(Request $request, Product $product)
    {
        $user = $request->user();
        $cart = $user->cart()->first();

        if (!$cart) {
            $cart = new Cart;
            $cart->user_id = $user->id; // Set the foreign key manually
            $cart->save();
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
        $defaultAddressId = $user->addresses()->where('is_default', true)->value('id'); // Get the ID of the default address

        return view('checkout.show', compact('groupedProducts', 'addresses', 'defaultAddressId'));
    }

    public function place_order(Request $request)
    {
        $user = $request->user();

        if (!$user->cart || $user->cart->products->isEmpty()) {
            return redirect()->route('cart.show')->with('toast', ['type' => 'info', 'message' => 'Your cart is empty.']);
        }

        // Validate form input
        $request->validate([
            'payment_method' => 'required|in:bank_transfer',
            'address_selector' => 'required|string',
            'recipient_name' => 'required_if:address_selector,new_address|string|max:255',
            'address_line_1' => 'required_if:address_selector,new_address|string|max:255',
            'city' => 'required_if:address_selector,new_address|string|max:255',
            'state' => 'required_if:address_selector,new_address|string|max:255',
            'postal_code' => 'required_if:address_selector,new_address|string|max:20',
            'country' => 'required_if:address_selector,new_address|string|max:255',
            'phone_number' => 'required_if:address_selector,new_address|string|max:20',
        ]);

        // Retrieve selected address
        $address = null;
        $addressSelector = $request->input('address_selector');

        if ($addressSelector === 'new_address') {
            $address = $user->addresses()->create([
                'recipient_name' => $request->input('recipient_name'),
                'address_line_1' => $request->input('address_line_1'),
                'address_line_2' => $request->input('address_line_2'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'postal_code' => $request->input('postal_code'),
                'country' => $request->input('country'),
                'phone_number' => $request->input('phone_number'),
                'is_default' => $request->has('remember_address'),
            ]);
        } else {
            // Ensure the selected address belongs to the user
            $address = $user->addresses()->find($addressSelector);

            if (!$address) {
                return redirect()->route('checkout.show')->with('toast', [
                    'type' => 'error',
                    'message' => 'Invalid address selected.',
                ]);
            }
        }

        // Check stock for all products
        foreach ($user->cart->products as $product) {
            if ($product->stock_quantity < $product->pivot->quantity) {
                return redirect()->route('cart.show')->with('toast', [
                    'type' => 'error',
                    'message' => "Not enough stock for {$product->name}. Available: {$product->stock_quantity}, Requested: {$product->pivot->quantity}"
                ]);
            }
        }

        // Group products by seller
        $groupedProducts = $user->cart->products->groupBy('seller_id');

        // Place orders and allocate stock
        $order = null;

        foreach ($groupedProducts as $sellerId => $products) {
            $order = $user->orders()->create([
                'seller_id' => $sellerId,
                'payment_method' => $request->input('payment_method'),
                'total_amount' => $products->sum(fn($product) => $product->price * $product->pivot->quantity),
                'address_id' => $address->id,
            ]);

            foreach ($products as $product) {
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
