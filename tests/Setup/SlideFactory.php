<?php

namespace Tests\Setup;

use App\Models\Slide;
use Illuminate\Http\UploadedFile;

class SlideFactory
{
    private $count = 1;

    public function count($count)
    {
        $this->count = $count;

        return $this;
    }

    public function create()
    {
        $count = $this->count;

        return Slide::factory()->count($count)->create()
            ->each(function ($slide) {
                // upload a sample image for each slide
                $pathWithFileNameArray = explode('/', $slide->slide);
                $fileName = array_pop($pathWithFileNameArray);
                UploadedFile::fake()->image($fileName)
                    ->storeAs('images/slides', $fileName);
            });
    }
}
