<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class ResizeImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;  // fixme
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'image' => ['required'],
            'w' => ['required', 'regex:/^\d+(\.\d+)?%?$/'],
            'h' => ['regex:/^\d+(\.\d+)?%?$/'],
            // syntax: exists:table,column
            'album_id' => ['exists:\App\Models\Album,id']
        ];

        // getting the image field from request file in the current request
        // NOTE: $this->post('image') does not work files it only works for text fields
        $image = $this->file('image');
        if ($image && $image instanceof UploadedFile) {
            // if file was passed as form data
            array_push($rules['image'], 'image');  // The file under image validation must be an image (jpg, jpeg, png, bmp, gif, or webp).
        } else {
            // If it was not passed as form data we assume that it was passed as query string base64 encoded
            array_push($rules['image'], 'url');
        }
        return $rules;
    }
}
