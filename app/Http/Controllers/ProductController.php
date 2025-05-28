<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeKey;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $productCategories = ProductCategory::all();
        return view('seller.products.create', compact('productCategories'));
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
            'product_images' => 'required|array|min:1',
            'product_images.*' => 'file|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'delivery_fee' => 'required|decimal:2|min:0',
            'product_category_id' => 'required|exists:product_categories,id',
        ];

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

        $new_product = new Product;
        $new_product->name = $validated['product_name'];
        $new_product->description = $validated['product_description'] ?? "";
        $new_product->price = $validated['product_price'];
        $new_product->stock_quantity = $validated['stock_quantity'];
        $new_product->delivery_fee = $validated['delivery_fee'];
        $new_product->is_published = $validated['is_published'] === 'on';
        $new_product->product_category_id = $validated['product_category_id'];

        $request->user()->seller->products()->save($new_product);

        // Store multiple images
        $is_first_image = true;
        $order = 1;
        foreach ($validated['product_images'] as $imageFile) {
            $imagePath = $imageFile->store('product_images/' . $new_product->id, 'public');

            $new_product->images()->create([
                'image_path' => $imagePath,
                'is_thumbnail' => $is_first_image,
                'order' => $order,
            ]);

            if ($is_first_image) {
                $new_product->image_path = $imagePath; // For backward compatibility
                $is_first_image = false;
            }
            $order++;
        }
        $new_product->save(); // Save product again to update image_path

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

        $product->load(['attributes.productAttributeKey', 'category']);
        $productCategories = ProductCategory::all();

        return view('seller.products.edit', compact('product', 'productCategories'));
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
            'product_images' => 'nullable|array',
            'product_images.*' => 'file|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'delivery_fee' => 'decimal:2|min:0',
            'product_category_id' => 'required|exists:product_categories,id',
            'attributes' => 'nullable|array',
            'attributes.*.id' => 'required|exists:product_attributes,id',
            'attributes.*.value' => 'nullable|string|max:255',
            'attributes.*.order_column' => 'required|integer|min:0',
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
        $product->stock_quantity = $validated['stock_quantity'] ?? $product->stock_quantity;

        // Handle multiple images update
        if ($request->hasFile('product_images')) {
            // Delete existing product images and their files
            foreach ($product->images as $image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Store new images
            $is_first_image = true;
            $order = 1;
            foreach ($validated['product_images'] as $imageFile) {
                $imagePath = $imageFile->store('product_images/' . $product->id, 'public');

                $product->images()->create([
                    'image_path' => $imagePath,
                    'is_thumbnail' => $is_first_image,
                    'order' => $order,
                ]);

                if ($is_first_image) {
                    $product->image_path = $imagePath; // For backward compatibility
                    $is_first_image = false;
                }
                $order++;
            }
        }

        $product->delivery_fee = $validated['delivery_fee'] ?? $product->delivery_fee;
        $product->is_published = $validated['is_published'] ?? false;
        $product->product_category_id = $validated['product_category_id'];

        $product->save();

        // Update product attributes
        if (isset($validated['attributes'])) {
            foreach ($validated['attributes'] as $attributeData) {
                $productAttribute = $product->attributes->find($attributeData['id']);
                if ($productAttribute) {
                    $productAttribute->value = $attributeData['value'];
                    $productAttribute->order_column = $attributeData['order_column'];
                    $productAttribute->save();
                }
            }
        }

        return redirect()->route('seller.products.show', compact('product'))
            ->with('toast', ['type' => 'success', 'message' => 'Successfully updated ' . $product->name . '.']);
    }

    public function storeAttribute(Request $request, Product $product)
    {
        if ($request->user()->cannot('update', $product)) {
            return redirect()->route('home');
        }

        $rules = [
            'attribute_type_name' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $display_name = $validated['attribute_type_name'];
        $key_name = Str::lower(Str::replace(' ', '_', $display_name));

        $productAttributeKey = ProductAttributeKey::firstOrCreate(
            ['name' => $key_name],
            ['display_name' => $display_name]
        );

        // Determine the next order_column
        $maxOrder = $product->attributes()->max('order_column');
        $nextOrder = $maxOrder !== null ? $maxOrder + 1 : 0;

        $product->attributes()->create([
            'attribute_key_id' => $productAttributeKey->id,
            'value' => null, // Or an empty string, as per requirement
            'order_column' => $nextOrder,
        ]);

        return back()->with('success', 'Attribute type added successfully!');
    }

    public function destroyAttribute(Request $request, Product $product, ProductAttribute $attribute)
    {
        if ($request->user()->cannot('update', $product)) {
            return response()->json(['message' => 'Unauthorized to update this product.'], 403);
        }

        // Ensure the attribute belongs to the product
        if ($attribute->product_id !== $product->id) {
            return response()->json(['message' => 'Attribute does not belong to this product.'], 403);
        }

        try {
            $attribute->delete();
            return response()->json(['message' => 'Attribute deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete attribute: ' . $e->getMessage()], 500);
        }
    }
}
