<?php

namespace App\Http\Controllers;

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
}
