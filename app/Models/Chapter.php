<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = [
        'name',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function members(){
        return $this->hasMany(User::class);
    }

    public function admin()
    {
        return $this->hasOne(User::class)->whereHas('roles', function($q){
            $q->where('name', 'admin');
        });
    }


}
