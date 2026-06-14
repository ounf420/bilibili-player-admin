<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ad extends Model
{
    protected $table = 'ads';
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = 'ad' . Str::random(8);
            }
        });
    }
    
    protected $fillable = [
        'id',
        'name',
        'type',
        'media_url',
        'media_type',
        'brand_name',
        'brand_logo',
        'click_url',
        'cta_text',
        'duration',
        'skippable',
        'fullscreen',
        'closable',
        'trigger_time',
        'skip_after',
        'text_content',
        'text_color',
        'description',
        'interactive_type',
        'options',
        'results',
        'qrcode_url',
        'enabled',
        'priority',
        'frequency_cap',
        'target_videos',
        'target_category',
        'start_date',
        'end_date',
        'impressions',
        'clicks',
        'skips',
        'badge_text',
        'badge_color',
        'progress_color',
        'overlay_opacity',
        'animation',
        'text_stroke',
        'decoration_id',
    ];
    
    protected $casts = [
        'skippable' => 'boolean',
        'fullscreen' => 'boolean',
        'closable' => 'boolean',
        'enabled' => 'boolean',
        'options' => 'array',
        'results' => 'array',
    ];

    public function decoration()
    {
        return $this->belongsTo(Decoration::class, 'decoration_id');
    }
}
