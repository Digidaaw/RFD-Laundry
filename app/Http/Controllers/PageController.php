<?php

namespace App\Http\Controllers;

use App\Models\Layanan;

class PageController extends Controller
{
    public function home()
    {
        $layanans = Layanan::latest()->get();

        return view('welcome', compact('layanans'));
    }

    public function quoteRequest()
    {
        return view('quote.request');
    }
}
