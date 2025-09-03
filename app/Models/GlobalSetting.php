<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $table = 'global_settings';

    protected $fillable = [
        'church_name',
        'denomination',
        'tagline',
        'logo',
        'favicon',
        'banner_image',
        'livestream_url',
        'podcast_url',
        'giving_url',
        'social_links',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'extras',
    ];

    protected $casts = [
        'social_links' => 'array',
        'meta_keywords' => 'array',
        'extras' => 'array',
    ];
}
