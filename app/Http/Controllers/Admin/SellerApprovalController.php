<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\User;

class SellerApprovalController extends Controller
{
    /**
     * Display a listing of all sellers.
     */
    public function index()
    {
        $sellers = Seller::with('user')
            ->select('sellers.*')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($seller) {
                return [
                    'id' => $seller->id,
                    'business_name' => $seller->business_name,
                    'user_name' => $seller->user->name,
                    'email' => $seller->user->email,
                    'status' => $seller->approved ? 'Approved' : 'Pending',
                    'created_at' => $seller->created_at,
                ];
            });

        return view('admin.sellers.index', compact('sellers'));
    }

    /**
     * Display a listing of pending sellers.
     */
    public function pending()
    {
        $pendingSellers = Seller::with('user')
            ->where('approved', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($seller) {
                return [
                    'id' => $seller->id,
                    'business_name' => $seller->business_name,
                    'user_name' => $seller->user->name,
                    'email' => $seller->user->email,
                    'created_at' => $seller->created_at,
                ];
            });

        return view('admin.sellers.pending', compact('pendingSellers'));
    }

    /**
     * Display the specified seller details.
     */
    public function show($id)
    {
        $seller = Seller::with('user')->findOrFail($id);

        $sellerData = [
            'id' => $seller->id,
            'business_name' => $seller->business_name,
            'business_description' => $seller->business_description,
            'business_address' => $seller->business_address,
            'business_phone' => $seller->business_phone,
            'business_email' => $seller->business_email,
            'logo_url' => $seller->profile_image_url,
            'opening_hour' => $seller->opening_hour,
            'closing_hour' => $seller->closing_hour,
            'facebook' => $seller->facebook,
            'instagram' => $seller->instagram,
            'ic_number' => $seller->ic_number,
            'business_cert_url' => $seller->business_cert_url ? asset('storage/' . $seller->business_cert_url) : null,
            'bank_name' => $seller->bank_name,
            'bank_account_name' => $seller->bank_account_name,
            'bank_account_number' => $seller->bank_account_number,
            'user_name' => $seller->user->name,
            'user_email' => $seller->user->email,
            'status' => $seller->approved ? 'Approved' : 'Pending',
        ];

        return view('admin.sellers.show', ['seller' => $sellerData]);
    }

    /**
     * Approve a seller.
     */
    public function approve($id)
    {
        $seller = Seller::findOrFail($id);

        if (!$seller->approved) {
            $seller->update([
                'approved' => true,
                'approved_at' => now()
            ]);
        }

        return back()->with('success', 'Seller has been approved successfully.');
    }
}
