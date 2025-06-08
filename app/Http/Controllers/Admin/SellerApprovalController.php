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
        // Hardcoded data for demonstration
        $sellers = [
            [
                'id' => 1,
                'business_name' => 'Siti\'s Delightful Bakes',
                'user_name' => 'Siti Aisyah',
                'email' => 'siti.aisyah@example.com',
                'status' => 'Pending',
                'created_at' => '2024-01-15 10:00:00',
            ],
            [
                'id' => 2,
                'business_name' => 'Kedai Buku ABC',
                'user_name' => 'Ahmad Bin Ali',
                'email' => 'ahmad.ali@example.com',
                'status' => 'Approved',
                'created_at' => '2023-11-01 09:30:00',
            ],
            [
                'id' => 3,
                'business_name' => 'Warung Makan Sedap',
                'user_name' => 'Fatimah Binti Abdullah',
                'email' => 'fatimah.abdullah@example.com',
                'status' => 'Approved',
                'created_at' => '2024-02-20 14:00:00',
            ],
        ];

        return view('admin.sellers.index', compact('sellers'));
    }

    /**
     * Display a listing of pending sellers.
     */
    public function pending()
    {
        // Hardcoded data for demonstration
        $pendingSellers = [
            [
                'id' => 1,
                'business_name' => 'Siti\'s Delightful Bakes',
                'user_name' => 'Siti Aisyah',
                'email' => 'siti.aisyah@example.com',
                'created_at' => '2024-01-15 10:00:00',
            ],
        ];

        return view('admin.sellers.pending', compact('pendingSellers'));
    }

    /**
     * Display the specified seller details.
     */
    public function show($id)
    {
        // Hardcoded data for demonstration based on the persona
        $seller = [
            'id' => $id,
            'business_name' => 'Siti\'s Delightful Bakes',
            'business_description' => 'Homemade cakes, cookies, and pastries made with premium ingredients. Perfect for celebrations or a daily treat.',
            'business_address' => 'No. 42, Lorong Cempaka, 26600 Pekan, Pahang',
            'business_phone' => '019-8765432',
            'business_email' => 'orders@sitisdelightfulbakes.com',
            'logo_url' => '/imgs/logo.png', // Placeholder image
            'opening_hour' => '09:00 AM',
            'closing_hour' => '07:00 PM',
            'facebook' => 'https://facebook.com/sitisdelightfulbakes',
            'instagram' => 'https://instagram.com/sitisdelightfulbakes',
            'ic_number' => '900120-06-5432',
            'business_cert_url' => 'https://www.africau.edu/images/default/sample.pdf', // Placeholder PDF
            'bank_name' => 'CIMB Bank',
            'bank_account_name' => 'Siti\'s Delightful Bakes Enterprise',
            'bank_account_number' => '987654321098',
            'user_name' => 'Siti Aisyah',
            'user_email' => 'siti.aisyah@example.com',
            'status' => 'Pending', // For demonstration
        ];

        return view('admin.sellers.show', compact('seller'));
    }
}
