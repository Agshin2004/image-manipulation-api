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

        // Removing image key from the $allData array because $allData will be saved in db and the actual image file doesn't need to be stored in the JSON
        unset($allData['image']);

        // prepare data to be saved in database
        $data = [
            'type' => Image::TYPE_RESIZE,  // marks the image operation
            'data' => json_encode($allData),
            'user_id' => null  // TODO: fix
        ];

        // if album_id was passed then attach that image to the album
        if (isset($allData['album_id'])) {
            // TODO:
            $data['album_id'] = $allData['album_id'];
        }

        // generate a random subdirectory under public/images/ so each image gets its own foldre
        $dir = 'images/' . \Illuminate\Support\Str::random() . '/';
        $absolutePath = public_path($dir);  // get the path to the public folder and then append $dir
        // Make directory with directory path we just creatd
        \Illuminate\Support\Facades\File::makeDirectory($absolutePath);

        if ($image instanceof UploadedFile) {
            // Handle image that was uploaded
            $data['name'] = $image->getClientOriginalName();
            $filename = pathinfo($data['name'], PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            // move image to newly created folder with the getClientOriginalName() that we assigned to $data['name']
            $image->move($absolutePath, $data['name']);
        } else {
            // Handle image passed as url
            $data['name'] = pathinfo($image, PATHINFO_BASENAME);  // Using pathinfo here because image was passed as url and we need to access image from different url
            $filename = pathinfo($image, PATHINFO_FILENAME);
            $extension = pathinfo($image, PATHINFO_EXTENSION);

            copy($image, $absolutePath . $data['name']); // copy image from different server
        }
        // Save the relative path of the image willl be stored in DB later, allowing access via URL
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
