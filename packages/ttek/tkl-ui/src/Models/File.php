<?php

namespace Tk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

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
