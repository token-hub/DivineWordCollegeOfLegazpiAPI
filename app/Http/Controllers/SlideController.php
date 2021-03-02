<?php

namespace App\Http\Controllers;

use App\Http\Requests\SlideDeleteRequest;
use App\Http\Requests\SlideStoreRequest;
use App\Http\Requests\SlideViewRequest;
use App\Http\Resources\SlideCollection;
use App\Models\Slide;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function index(SlideViewRequest $request)
    {
        return new SlideCollection(Slide::latest()->get());
    }

    public function store(SlideStoreRequest $request)
    {
        $latestOrderNumber = Slide::latest('order')->get()->first()
        ? Slide::latest('order')->get()->first()->order
        : 0;

        if ($request->file('slides')) {
            $slides = $request->file('slides');

            $fileNames = array_map(function ($slide) {
                return "images/slides/{$slide->getClientOriginalName()}";
            }, $slides);

            // get the file if it is already in the database
            $existingFiles = Slide::whereIn('slide', $fileNames)->get()->pluck('slide')->toArray();

            foreach ($slides as $slide) {
                if (!in_array($slide, $existingFiles)) {
                    $filename = $slide->getClientOriginalName();
                    $slide->storeAs('images/slides/', $filename);

                    Slide::create([
                        'slide' => $slide->storeAs('images/slides', $filename),
                        'order' => ++$latestOrderNumber,
                    ]);
                }
            }

            return response()->json(['message' => 'Slide/s was successfully added'], 200);
        }

        return response()->json(['message' => 'Please provide an image'], 422);
    }

    public function destroy($slideIds, SlideDeleteRequest $request)
    {
        // $slideIds is coming from an axios http request so it will be
        // string when it got here, so we need to turn it back to array,
        // before we use it

        $ids = array_map('intval', explode(',', $slideIds));

        $slides = Slide::find($ids)->pluck('slide')->toArray();

        //remove all the selected images from the storage
        Storage::disk('public')->delete($slides);

        Slide::destroy($ids);

        return response()->json(['message' => 'Slide/s was successfully delete']);
    }
}
