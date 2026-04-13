<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Logika untuk subscribe newsletter
        // Misalnya, simpan email ke database atau kirim ke service

        return back()->with('success', 'Berhasil subscribe newsletter');
    }
}