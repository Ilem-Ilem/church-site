<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    protected $fillable = ['navbar', 'carousel', 'sections'];

    protected $casts = [
        'carousel' => 'array',
    ];
}
