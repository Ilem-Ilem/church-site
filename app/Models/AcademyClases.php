<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyClases extends Model
{
    public $fillable = ['name', 'description', 'date', 'time', 'study_material', 'tutor', 'academy_id'];

    public function academy()
    {
        return $this->belongsTo(BeliversAcademy::class);
    }
}
