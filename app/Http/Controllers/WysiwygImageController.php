<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WysiwygImageController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $filename = $image->getClientOriginalName();
            $link = $image->storeAs('images/updates', $filename);

            return response()->json(['link' => Storage::url($link)]);
        }
    }
}
