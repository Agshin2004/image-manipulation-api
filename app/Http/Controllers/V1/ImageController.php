<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ResizeImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\UploadedFile;

class ImageController extends Controller
{
    public function index()
    {
        //
    }

    public function resize(ResizeImageRequest $request)
    {
        $allData = $request->all();

        /** @var UploadedFile|string $image */
        $image = $allData['image'];

        // Removing image key from the $allData array because $allData will be saved in db and we do not need image data there
        unset($allData['image']);

        // data to be saved in database
        $data = [
            'type' => Image::TYPE_RESIZE,
            'data' => json_encode($allData),
            'user_id' => null  // TODO: fix
        ];

        if (isset($allData['album_id'])) {
            // TODO:
            $data['album_id'] = $allData['album_id'];
        }

        $dir = 'images/' . \Illuminate\Support\Str::random() . '/';
        $absolutePath = public_path($dir);  // get the path to the public folder and then append $dir
        \Illuminate\Support\Facades\File::makeDirectory($absolutePath);

        if ($image instanceof UploadedFile) {
            // Handle image that was uploaded
            $data['name'] = $image->getClientOriginalName();
            $filename = pathinfo($data['name'], PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            // move image to newly created folder
            $image->move($absolutePath, $data['name']);
        } else {
            // Handle image passed as url
            $data['name'] = pathinfo($image, PATHINFO_BASENAME);  // PATHINFO_BASENAME - give folder name or full file name (with extension)
            $filename = pathinfo($image, PATHINFO_FILENAME);
            $extension = pathinfo($image, PATHINFO_EXTENSION);

            copy($image, $absolutePath . $data['name']);
        }
        $data['path'] = $dir . $data['name'];
    }

    public function byAlbum(Album $album) {}

    public function show(Image $image)
    {
        //
    }

    public function destroy(Image $image)
    {
        //
    }
}
