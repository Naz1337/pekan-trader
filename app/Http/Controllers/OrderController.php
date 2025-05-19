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

        $order->order_histories()->create([
            'message' => 'The customer uploaded a transaction receipt.',
            'created_at' => now(),
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('toast', [
                'type' => 'success',
                'message' => 'Payment receipt uploaded successfully. Your payment is being processed.'
            ]);
    }

    public function seller_index(Request $request)
    {
        // Get the logged-in user's seller relationship
        $seller = $request->user()->seller;

        // Query orders related to the seller
        $orders = $seller->orders()->latest()->get();

        // Return a view with the orders
        return view('seller.orders.index', compact('orders'));
    }

    public function seller_show(Request $request, Order $order)
    {
        // Get the logged-in user's seller relationship
        $seller = $request->user()->seller;

        // Check if the order belongs to the seller
        if ($order->seller_id !== $seller->id) {
            return redirect()->route('seller.dashboard')->with('toast', [
                'type' => 'error',
                'message' => 'Unauthorized action.'
            ]);
        }

        // Return the view with the order details
        return view('seller.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        // Get the authenticated user's seller
        $seller = $request->user()->seller;

        // Check if the order belongs to the seller
        if ($order->seller_id !== $seller->id) {
            return redirect()->route('seller.dashboard')->with('toast', [
                'type' => 'error',
                'message' => 'Unauthorized action.'
            ]);
        }

        // Check if the order can be canceled
        if ($order->status === 'canceled') {
            return redirect()->route('seller.orders.show', $order->id)->with('toast', [
                'type' => 'error',
                'message' => 'Order is already canceled.'
            ]);
        }

        // Update the order status to 'canceled'
        $order->status = 'canceled';
        $order->save();

        // Return the stock to the products
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->stock_quantity += $item->quantity; // Use the correct column name
            $product->save();
        }

        // Add an order history entry
        $order->order_histories()->create([
            'message' => 'The order was canceled by the seller.',
            'created_at' => now(),
        ]);

        // Notify the customer (optional)
        // Notification::send($order->user, new OrderCanceledNotification($order));

        return redirect()->route('seller.orders.show', $order->id)->with('toast', [
            'type' => 'success',
            'message' => 'Order has been successfully canceled.'
        ]);
    }

    public function setDelivering(Request $request, Order $order)
    {
        // Get the authenticated user's seller
        $seller = $request->user()->seller;

        // Check if the order belongs to the seller
        if ($order->seller_id !== $seller->id) {
            return redirect()->route('seller.dashboard')->with('toast', [
                'type' => 'error',
                'message' => 'Unauthorized action.'
            ]);
        }

        // Validate the tracking ID
        $request->validate([
            'tracking_id' => 'required|string|max:255',
        ]);

        // Check if the order status is 'packing'
        if ($order->status !== 'packing') {
            return redirect()->route('seller.orders.show', $order->id)->with('toast', [
                'type' => 'error',
                'message' => 'Order cannot be set to delivering at this stage.'
            ]);
        }

        // Update the order status and tracking ID
        $order->status = 'delivering';
        $order->tracking_id = $request->input('tracking_id');
        $order->save();

        // Add an order history entry
        $order->order_histories()->create([
            'message' => 'The order was set to delivering by the seller.',
            'created_at' => now(),
        ]);

        // Notify the customer (optional)
        // Notification::send($order->user, new OrderDeliveringNotification($order));

        return redirect()->route('seller.orders.show', $order->id)->with('toast', [
            'type' => 'success',
            'message' => 'Order has been successfully set to delivering.'
        ]);
    }

    public function setReceived(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== $request->user()->id) {
            return redirect()->route('orders.index')->with('toast', [
                'type' => 'error',
                'message' => 'Unauthorized action.'
            ]);
        }

        // Update the order status to 'completed'
        $order->status = 'completed';
        $order->save();

        // Add an order history entry
        $order->order_histories()->create([
            'message' => 'The order was marked as received by the customer.',
            'created_at' => now(),
        ]);

        return redirect()->route('orders.show', $order->id)->with('toast', [
            'type' => 'success',
            'message' => 'Order marked as received.'
        ]);
    }
}
