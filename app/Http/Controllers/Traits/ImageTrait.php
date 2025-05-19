<?php

namespace App\Http\Controllers\Traits;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

trait ImageTrait
{
    protected function getImageWidthAndHeight($width, $height, $imagePath): array
    {
        // in intervention images are manipulated thru ImageManager
        // used gd image processing driver here, since manipulations are simple and gd is more lightweight
        // if needed more advanced image manipulation imagick driver must be used BUT ImageMagick must be installed on the server
        $manager = new ImageManager(new Driver());
        $image = $manager->read($imagePath);
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // if width ends with % then gotta resize it based on that
        if (str_ends_with($width, '%')) {
            $ratioW = (float) str_replace('%', '', $width);
            $ratioH = $height ? (float) str_replace('%', '', $width) : $ratioW;

            // Since $ratioW is percent value this is formula
            $newWidth = $originalWidth * $ratioW / 100;
            $newHeight = $originalHeight * $ratioH / 100;
        } else {
            // if width is not percent (meaning it is already a value to resize)
            $newWidth = (float) $width;  // casting it to float because it may be string
            $newHeight = $height ? (float) $height : $originalHeight * $newWidth / $originalWidth;
        }

        return [$newWidth, $newHeight];
    }
}
