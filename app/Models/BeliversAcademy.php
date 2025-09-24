<?php

//TODO: create the class_table with columns('id', 'name', 'date', 'time')
//TODO: create the student class table with column('id', 'user_id', 'class_completed', 'status'. 'cert')
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeliversAcademy extends Model
{
    public $fillable = [
        'status', 'chapter_id', 'start_at'
    ];

    public function classes()
    {
        return $this->hasMany(AcademyClases::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
