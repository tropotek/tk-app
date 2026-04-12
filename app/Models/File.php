<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'fkey',
        'fid',
        'original_name',
        'filename',
        'path',
        'mime_type',
        'size',
    ];
}
