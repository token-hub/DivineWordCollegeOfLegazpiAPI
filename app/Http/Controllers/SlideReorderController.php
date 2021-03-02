<?php

namespace App\Http\Controllers;

use App\Http\Requests\SlideUpdateRequest;
use App\Models\Slide;

class SlideReorderController extends Controller
{
    public function __invoke(SlideUpdateRequest $request)
    {
        $slidesOrder = array_map(function ($slide) {
            return json_decode($slide);
        }, $request->all());

        $slideValues = array_map(function ($slide) {
            return $slide->value;
        }, $slidesOrder);

        $uniqueOrderNumbersCount = count(array_unique(array_values($slideValues)));
        $OrderNumbersCount = count(array_values($slideValues));

        if ($uniqueOrderNumbersCount < $OrderNumbersCount) {
            return response()->json(['message' => 'Order number must not duplicated']);
        }

        Slide::all()->each(function ($slide) use ($slidesOrder) {
            foreach ($slidesOrder as $value) {
                if ($slide->id === $value->id) {
                    $slide->update(['order' => $value->value]);
                }
            }
        });

        return response()->json(['message' => 'Slide was successfully updated']);
    }
}
