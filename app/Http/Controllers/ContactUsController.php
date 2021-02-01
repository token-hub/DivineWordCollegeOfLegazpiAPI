<?php

namespace App\Http\Controllers;

use App\Mail\ContactUsMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        Mail::send(new ContactUsMail($validated));

        return response()->json(['message' => 'Your message has been sent'], 200);
    }
}
