<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClasses extends Model
{
    public $fillable = ['user_id', 'class_completed', 'status', 'cert', 'interest', 'how_did_you_know_about_us', 'phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
