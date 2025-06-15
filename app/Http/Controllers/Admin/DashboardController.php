<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingCount = Seller::where('approved', false)->count();
        $allSellersCount = Seller::count();

        return view('admin.dashboard', compact('pendingCount', 'allSellersCount'));
    }
}
