<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function store(Seller $seller)
    {
        $user = auth()->user();

        // Check if user is trying to follow their own seller account
        if ($user->seller && $user->seller->id === $seller->id) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'You cannot follow your own seller account.'], 400);
            }
            return redirect()->back()->with('error', 'You cannot follow your own seller account.');
        }

        // Check if already following
        if ($user->followedSellers()->where('seller_id', $seller->id)->exists()) {
            if (request()->expectsJson()) {
                return response()->json(['info' => 'You are already following this seller.'], 200);
            }
            return redirect()->back()->with('info', 'You are already following this seller.');
        }

        // Create the follow relationship
        $user->followedSellers()->attach($seller->id);

        if (request()->expectsJson()) {
            return response()->json(['success' => 'You are now following ' . $seller->business_name . '!'], 200);
        }
        return redirect()->back()->with('success', 'You are now following ' . $seller->business_name . '!');
    }

    public function destroy(Seller $seller)
    {
        $user = auth()->user();

        // Remove the follow relationship
        $user->followedSellers()->detach($seller->id);

        if (request()->expectsJson()) {
            return response()->json(['success' => 'You have unfollowed ' . $seller->business_name . '.'], 200);
        }
        return redirect()->back()->with('success', 'You have unfollowed ' . $seller->business_name . '.');
    }
}
