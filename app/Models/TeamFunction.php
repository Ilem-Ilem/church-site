<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamFunction extends Model
{
    protected $table = 'team_functions';
    
    protected $fillable = ['team_id', 'function', 'function_id'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
