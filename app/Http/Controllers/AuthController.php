<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\UrlGenerator;

use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to log the user in
        if (auth()->attempt($validated)) {
            $products = Product::all();
            // Redirect to the intended page
            return response()->view('welcome', [
                'products' => $products,
                'query' => null,
                'categoryName' => null
            ])->header('HX-Redirect', route('home'));
        }

        $request->session()->flash('email', $validated['email']);

        return view('auth.login')->withErrors(['login' => 'Wrong email or password'])
            ->fragmentIf($request->hasHeader('HX-Request') ,'form');
    }

    public function register()
    {
        return view('auth.register')->fragmentIf(request()->hasHeader('HX-Request') && ! request()->hasHeader('HX-Boosted'), 'form');
    }

    public function registerPost(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('login')->with(
            'success',
            'Registration successful. You can now log in.'
        );
    }

    public function logout()
    {
        auth()->logout();
        session()->invalidate();

        return redirect()->route('home');
    }

    public function showRegisterSellerStep(Request $request)
    {
        return view('auth.register-seller-1');
    }

    public function postRegisterSellerStep()
    {
        $rules = [
            "business_name" => 'required|string',
            "business_description" => 'required|string',
            "business_address" => 'required|string',
            "business_phone" => 'required|string',
            "business_email" => 'required|string|email',
            "logo" => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            "opening_hour" => 'required|string',
            "closing_hour" => 'required|string',
            "facebook" => 'nullable|string',
            "instagram" => 'nullable|string',
            "ic_number" => 'required|string',
            "business_cert" => 'required|mimes:pdf|max:15360',
            "bank_name" => 'required|string',
            "bank_account_name" => 'required|string',
            "bank_account_number" => 'required|string',
        ];

        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'at' => 'seller',
                'errors' => $errors,
            ], 422);
        }

        $seller_data = $validator->validated();

        $rules_for_user = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ];

        $user_validator = Validator::make(request()->all(), $rules_for_user);
        if ($user_validator->fails()) {
            $errors = $user_validator->errors();
            return response()->json([
                'at' => 'user',
                'errors' => $errors,
            ], 422);
        }
        $user_data = $user_validator->validated();

        $new_user = User::create([
            'name' => $user_data['name'],
            'email' => $user_data['email'],
            'password' => $user_data['password'],
        ]);

        // Handle file uploads
        $logo_path = $seller_data['logo']->store('logos', 'public');
        $cert_path = $seller_data['business_cert']->store('business_certs', 'public');
        $seller_data['logo_url'] = $logo_path;
        $seller_data['business_cert_url'] = $cert_path;

        // Create the seller
        $new_user->seller()->create([
            "business_name" => $seller_data['business_name'],
            "business_description" => $seller_data['business_description'],
            "business_address" => $seller_data['business_address'],
            "business_phone" => $seller_data['business_phone'],
            "business_email" => $seller_data['business_email'],
            "logo_url" => $logo_path,
            "opening_hour" => $seller_data['opening_hour'],
            "closing_hour" => $seller_data['closing_hour'],
            "facebook" => $seller_data['facebook'],
            "instagram" => $seller_data['instagram'],
            "ic_number" => $seller_data['ic_number'],
            "business_cert_url" => $cert_path,
            "bank_name" => $seller_data['bank_name'],
            "bank_account_name" => $seller_data['bank_account_name'],
            "bank_account_number" => $seller_data['bank_account_number']
        ]);

        return redirect()->route('login')->with(
            'success',
            'Your Seller account has been successfully registered. You can now log in.'
        );
    }
}
