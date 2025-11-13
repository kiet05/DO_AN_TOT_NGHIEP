<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the site homepage (temporary fallback).
     */
    public function index(Request $request)
    {
        // If a frontend view exists, render it; otherwise return a simple placeholder.
        if (function_exists('view') && view()->exists('frontend.home')) {
            return view('frontend.home');
        }

        if (function_exists('view') && view()->exists('home')) {
            return view('home');
        }

        return response('Homepage placeholder', 200);
    }
}
