<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Decoration extends Model
{
    protected $table = 'decorations';
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = 'dec' . Str::random(8);
            }
        });
    }
    
    protected $fillable = [
        'id',
        'name',
        'badge_text',
        'badge_color',
        'badge_text_color',
        'progress_color',
        'progress_bg',
        'overlay_opacity',
        'overlay_gradient',
        'animation',
        'text_stroke',
        'text_shadow_color',
        'cta_style',
        'cta_color',
        'cta_text_color',
        'close_btn_style',
        'countdown_style',
        'show_brand_area',
        'show_progress_bar',
        'custom_css',
        'enabled',
        'sort_order',
    ];
    
    protected $casts = [
        'show_brand_area' => 'boolean',
        'show_progress_bar' => 'boolean',
        'enabled' => 'boolean',
    ];

    public function ads()
    {
        return $this->hasMany(Ad::class, 'decoration_id');
    }
}
