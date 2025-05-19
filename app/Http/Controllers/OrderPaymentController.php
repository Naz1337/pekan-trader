<?php

namespace App\Http\Controllers;

use App\Models\OrderPayment;
use Illuminate\Http\Request;

class OrderPaymentController extends Controller
{
    public function accept(Request $request, OrderPayment $orderPayment)
    {
        // Update the status of the order payment to 'confirmed'
        $orderPayment->update(['status' => 'confirmed']);

        // Update the payment status of the related order to 'paid'
        $order = $orderPayment->order;
        $order->payment_status = 'paid';
        $order->status = 'packing';
        $order->save();

        // Create an order history message
        $order->order_histories()->create([
            'message' => 'Payment confirmed. Order status updated to packing.',
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('toast', [
            'type' => 'success',
            'message' => 'Payment accepted and order updated successfully.'
        ]);
    }

    public function reject(Request $request, OrderPayment $orderPayment)
    {
        // Update the status of the order payment to 'rejected'
        $orderPayment->update(['status' => 'rejected']);

        // Update the payment status of the related order to 'reupload_required'
        $order = $orderPayment->order;
        $order->payment_status = 'reupload_required';
        $order->save();

        // Create an order history message
        $order->order_histories()->create([
            'message' => 'Payment receipt rejected by the seller. A new receipt is required.',
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('toast', [
            'type' => 'error',
            'message' => 'Payment receipt rejected successfully. User needs to upload a new receipt.'
        ]);
    }
}
