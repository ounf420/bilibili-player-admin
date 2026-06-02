<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerSetting extends Model
{
    protected $table = 'player_settings';
    
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'label',
        'description',
    ];
}
