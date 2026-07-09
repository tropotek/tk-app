<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $casts = [
        'enable_user_reg' => 'boolean',
    ];

    protected $attributes = [
        'site_title' => 'Untitled',
        'enable_user_reg' => true,
        'site_email' => 'sales@example.com',
    ];

    /**
     * Settings are a single row. Return it, or an unsaved default instance
     * if the app hasn't been configured yet.
     */
    public static function current(): self
    {
        return static::first() ?? new static;
    }
}
