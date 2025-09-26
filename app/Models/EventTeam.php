<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTeam extends Model
{
    public $fillable = [
        'team_id', 'chapter_id'
    ];

    public function team(){
        return $this->belongsTo(Team::class);
    }

    public function chapter(){}
}
