<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'contact_number' => ['required'],
            'subject' => 'required',
            'message' => 'required',
        ]);

        return response()->json(['message' => 'Your message has been sent'], 200);
    }
}
