<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;

class SellerProfileController extends Controller
{
    public function show(Seller $seller)
    {
        $seller->load(['user', 'products']);
        return view('seller.profile.show', compact('seller'));
    }
}
