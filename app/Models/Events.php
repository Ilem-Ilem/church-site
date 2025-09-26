<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Events extends Model
{
    use SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'chapter_id',
        'created_by',
        'title',
        'slug',
        'description',
        'start_at',
        'end_at',
        'timezone',
        'location',
        'is_online',
        'livestream_url',
        'banner',
        'status',
        'capacity',
        'registration_required',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_online' => 'boolean',
        'registration_required' => 'boolean',
    ];

    // Relationships
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function forms()
    {
        return $this->hasMany(EventForm::class, 'event_id');
    }

    public function galleries()
    {
        return $this->hasMany(EventGallery::class, 'event_id');
    }
}
