<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public const TYPE_RESIZE = 'resize';
    public const UPDATED_AT = false;

    protected $fillable = ['name', 'path', 'type', 'output_path', 'user_id', 'album_id'];
}
