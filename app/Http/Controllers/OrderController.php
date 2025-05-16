<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
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

    public function pay(Request $request, Order $order)
    {
        // Check if the user is authorized to pay for this order
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        // Validate the uploaded file and payment method
        $rules = [
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'method' => 'required|string', // e.g., 'bank_transfer'
        ];

        $kek = makeDevFormValidator($request->all(), $rules);
        if ($kek['validator']->fails()) {
            return response()->json($kek['response']());
        }

        $validated = $kek['validator']->validated();

        // Store the receipt file
        $path = $validated['receipt']->store('receipts', 'public');

        // Create a new OrderPayment record
        $order_payment = OrderPayment::create([
            'order_id' => $order->id,
            'method' => $request->input('method'),
            'receipt_path' => $path,
            'status' => 'pending', // or 'paid' if auto-approve
            // 'meta' => json_encode([...]), // add extra data if needed
        ]);

        $order_payment->order->payment_status = 'in_payment';
        $order_payment->order->save();

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Payment receipt uploaded successfully. Your payment is being processed.');
    }
}
