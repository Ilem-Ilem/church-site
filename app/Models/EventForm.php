<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventForm extends Model
{
    use SoftDeletes;

    protected $table = 'event_forms';

    protected $fillable = [
        'event_id',
        'chapter_id',
        'name',
        'email',
        'phone',
        'guests',
        'answers',
        'notes',
        'status',
    ];

    protected $casts = [
        'answers' => 'array',
        'guests' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
