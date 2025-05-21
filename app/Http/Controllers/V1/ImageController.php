<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ResizeImageRequest;
use App\Http\Resources\V1\ImageResource;
use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ImageController extends Controller
{
    use \App\Http\Controllers\Traits\ImageTrait;

    public function index(Request $request)
    {
        // Collection - Used for: A collection of model instances (e.g., from all(), get(), or paginate())
        // Resource - Used for: A single model instance
        return ImageResource::collection(Image::where('user_id' ,$request->user()->id)->paginate());
    }

    public function byAlbum(Album $album)
    {
        $where = [
            'album_id' => $album->id,
        ];
        return ImageResource::collection(Image::where($where)->paginate());
    }

    public function show(Request $request, Image $image)
    {
        if ($request->user()->id !== $image->id)
            return response()->json(['message' => 'unauthorized'], 401);

        // Since Resource is used for a single model instance
        // and we return single image we must use ImageResource
        // But above in index we used collection because we were returning all of the images
        return new ImageResource($image);
    }

    public function destroy(Request $request, Image $image)
    {
        if ($request->user()->id !== $image->id)
            return response()->json(['message' => 'unauthorized'], 401);

        $image->delete();
        return response(status: 204);
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
            'user_id' => $request->user()->id
        ];

        // if album_id was passed then attach that image to the album
        if (isset($allData['album_id'])) {
            $album = Album::find($allData['album_id']); // find a model by its primary key
            if ($request->user()->id !== $album->user_id)
                return response()->json(['message' => 'unauthorized'], 401);

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
            $fullImagePath = $absolutePath . $data['name'];
        } else {
            // Handle image passed as url
            $data['name'] = pathinfo($image, PATHINFO_BASENAME);  // Using pathinfo here because image was passed as url and we need to access image from different url
            $filename = pathinfo($image, PATHINFO_FILENAME);
            // pathinfo() is only useful when url contains filename+extension but there is not always
            // extension in url to tackle that logic was replaced below
            // $extension = pathinfo($image, PATHINFO_EXTENSION);

            // get_headers under the hood makes HEAD request to get headers
            // actual image is downloaded below in using copy() method
            $headers = get_headers($image, 1);
            if (isset($headers['Content-Type'])) {
                $mime = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];
                $extension = match ($mime) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                    default => 'jpg'  // fallback to jpg
                };
            } else {
                $extension = 'jpg';  // falback if content type missing
            }

            // condition to check if filename already contains extension, if not then concatinate extension
            $hasExtension = (pathinfo($data['name'], PATHINFO_EXTENSION) === '' ? $extension : '');
            $fullImagePath = $absolutePath . $data['name'] . '.' . $hasExtension;

            copy($image, $fullImagePath);  // copy image from different server
        }
        // Save the relative path of the image willl be stored in DB later, allowing access via URL
        $data['path'] = $dir . $data['name'];

        $w = $allData['w'];
        $h = $allData['h'] ?? false;

        // unpack the return valies to the $width and $height (array like [1, 2] must be returned to succesffuly unpack it)
        // list($width, $height) -> method could be used but array destructuring in shorter
        [$width, $height, $image] = $this->getImageWidthAndHeight($w, $h, $fullImagePath);

        $resizedFilename = "{$filename}-resized.{$extension}";
        $image->resize($width, $height)->save($absolutePath . $resizedFilename);
        $data['output_path'] = $dir . $resizedFilename;  // path to the modified image (modified image and not modified stored in same folder)

        $savedImagedData = Image::create($data);

        return new ImageResource($savedImagedData);
    }
}
