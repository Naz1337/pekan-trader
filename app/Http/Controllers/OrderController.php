<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Request $request, Order $order)
    {

        // todo check policy for user and order
        return view('orders.show', compact('order'));
    }

    public function index(Request $request)
    {
        $orders = $request->user()->orders()->latest()->get();
        return view('orders.index', compact('orders'));
    }
}
