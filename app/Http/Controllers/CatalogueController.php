<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    function home(Request $request)
    {
        return view('welcome');
    }
}
