<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerEngine extends Model
{
    protected $fillable = [
        'name', 'code', 'icon', 'description', 'cdn_js',
        'cdn_css', 'hls_js', 'default_config', 'capabilities',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'default_config' => 'array',
        'capabilities' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function supportsFeature(string $feature): bool
    {
        return in_array($feature, $this->capabilities ?? []);
    }

    public static function getAvailableEngines(): array
    {
        return static::active()->orderBy('sort_order')->pluck('name', 'code')->toArray();
    }
}
