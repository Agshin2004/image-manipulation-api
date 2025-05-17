<?php

namespace App\Http\Controllers\V1;

use App\Models\Album;
use App\Models\Image;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateImageRequest;
use App\Http\Requests\V1\ResizeImageRequest;

class ImageController extends Controller
{
    public function index()
    {
        //
    }

    public function resize(ResizeImageRequest $request)
    {
        //
    }

    public function byAlbum(Album $album)
    {
        
    }

    public function show(Image $image)
    {
        //
    }

    public function destroy(Image $image)
    {
        //
    }
}
