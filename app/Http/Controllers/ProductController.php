<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search_product = $request->input('search_product');

        $products = $request->user()->seller->products()
            ->where('name', 'like', '%' . $search_product . '%')
            ->orderBy('id', 'desc')
            ->get();

        return view('seller.products.index', compact('search_product', 'products'));
    }

    public function create()
    {
        return view('seller.products.create');
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('create', Product::class)) {
            return redirect()->route('home');
        }

        $rules = [
            'product_name' => 'required|string|max:90',
            'product_description' => 'nullable|string|max:1000',
            'product_price' => 'required|decimal:2|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'product_image' => 'required|file|image|max:10240',
            'delivery_fee' => 'required|decimal:2|min:0',
        ];

//        $validated = $request->validate($rules);
        $validator = Validator::make($request->all(), $rules);

        $validator->sometimes('is_published', 'accepted', function ($input) {
            return $input->has('is_published');
        });

        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_data = $validator->getData();
            return response()->json([
                'at' => 'products',
                'errors' => $errors,
                'data' => $error_data
            ], 422);
        }

        $validated = $validator->validated();

        if (!array_key_exists('is_published', $validated))
            $validated['is_published'] = false;

        //store the image
        $product_image_path = $validated['product_image']->store('product_images', 'public');

        $new_product = new Product;
        $new_product->name = $validated['product_name'];
        $new_product->description = $validated['product_description'] ?? "";
        $new_product->price = $validated['product_price'];
        $new_product->stock_quantity = $validated['stock_quantity'];
        $new_product->image_path = $product_image_path;
        $new_product->delivery_fee = $validated['delivery_fee'];
        $new_product->is_published = $validated['is_published'] === 'on';

        $request->user()->seller->products()->save($new_product);

        return redirect()->route('seller.products.index')->with('success', 'Successfully created a new product!');

    }

    public function show(Request $request, Product $product)
    {
        if ($request->user()->can('view', $product)) {
            return view('seller.products.show', compact('product'));
        }
        return redirect()->route('home');
    }

    public function destroy(Request $request, Product $product)
    {
        if ($request->user()->cannot('delete', $product)) {
            return redirect()->route('home');
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('toast', ['type' => 'success', 'message' => 'Successfully deleted '. $productName . '.']);
    }

    public function edit(Request $request, Product $product)
    {
        if ($request->user()->cannot('update', $product)) {
            return redirect()->route('home');
        }

        return view('seller.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if ($request->user()->cannot('update', $product)) {
            return redirect()->route('home');
        }

        $rules = [
            'product_name' => 'string|max:90',
            'product_description' => 'nullable|string|max:1000',
            'product_price' => 'decimal:2|min:0',
            'stock_quantity' => 'integer|min:0',
            'product_image' => 'nullable|file|image|max:10240',
            'delivery_fee' => 'decimal:2|min:0'
        ];

        $validation = makeDevFormValidator($request->all(), $rules);

        $validation['validator']->sometimes('is_published', 'accepted', function ($input) {
            return $input->has('is_published');
        });

        if ($validation['validator']->fails()) {
            return $validation['response']();
        }

        $validated = $validation['validator']->validated();

//        return response()->json(['data' => $request->input(), 'files' => $request->hasFile('product_image')]);

        $product->name = $validated['product_name'] ?? $product->name;

        if (!is_null($validated['product_description'])) {
            $product->description = $validated['product_description'];
        }

        $product->price = $validated['product_price'] ?? $product->price;

        if ($request->hasFile('product_image')) {
            $image_path = $validated['product_image']->store('product_images', 'public');
            $product->image_path = $image_path;
        }

        $product->stock_quantity = $validated['stock_quantity'] ?? $product->stock_quantity;

        $product->delivery_fee = $validated['delivery_fee'] ?? $product->delivery_fee;

        $product->is_published = $validated['is_published'] ?? false;

        $product->save();

        return redirect()->route('seller.products.show', compact('product'))
            ->with('toast', ['type' => 'success', 'message' => 'Successfully updated ' . $product->name . '.']);
    }
}
