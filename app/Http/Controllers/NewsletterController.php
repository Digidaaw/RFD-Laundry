<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterSubscribeRequest;

class NewsletterController extends Controller
{
    public function subscribe(NewsletterSubscribeRequest $request)
    {
        // Logika untuk subscribe newsletter
        // Misalnya, simpan email ke database atau kirim ke service

        return back()->with('success', 'Berhasil subscribe newsletter');
    }
}