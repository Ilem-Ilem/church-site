<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_date',
        'title',
        'description',
        'event_type',
        'level',
        'chapter_id',
        'team_id',
        'created_by',
        'report',
        'report_path'
    ];

    protected $casts = [
        'report_date' => 'date',
        'level' => 'string', // enum: team, chapter, hq
    ];

    // Relationships
    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id')->withDefault();
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }
}