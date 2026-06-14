<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserPlayer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'slug', 'player_code', 'theme_color', 'logo_url', 'logo_position', 'logo_size', 'logo_opacity',
        'progress_icon_url', 'parse_url',
        'watermark_text', 'watermark_position', 'watermark_font_size',
        'watermark_color', 'watermark_opacity', 'watermark_x', 'watermark_y',
        'show_title', 'show_controls', 'template', 'background_image', 'background_image_mobile',
        'autoplay', 'loop_play', 'muted', 'show_danmaku', 'allow_danmaku',
        'show_quality', 'show_speed', 'show_fullscreen', 'show_pip',
        'show_download', 'show_share', 'width', 'height', 'aspect_ratio',
        'border_radius', 'show_ads', 'ad_decoration_id', 'ad_mode',
        'engine_code', 'player_key', 'plan_id',
        'version', 'version_expire_at', 'custom_domain', 'has_super_ad', 'has_ad_module', 'has_ad_free',
        'ad_free_expires_at', 'ad_module_expires_at',
        'custom_domain_enabled',
        'super_material_enabled', 'engine_config', 'ad_mode',
        'preroll_duration', 'midroll_duration', 'postroll_duration',
        'show_marquee', 'marquee_text', 'marquee_speed', 'marquee_color',
        'is_active', 'view_count', 'video_count',
    ];

    protected $casts = [
        'show_title' => 'boolean',
        'show_controls' => 'boolean',
        'autoplay' => 'boolean',
        'loop_play' => 'boolean',
        'muted' => 'boolean',
        'show_danmaku' => 'boolean',
        'allow_danmaku' => 'boolean',
        'show_quality' => 'boolean',
        'show_speed' => 'boolean',
        'show_fullscreen' => 'boolean',
        'show_pip' => 'boolean',
        'show_download' => 'boolean',
        'show_share' => 'boolean',
        'show_ads' => 'boolean',
        'show_marquee' => 'boolean',
        'custom_domain_enabled' => 'boolean',
        'super_material_enabled' => 'boolean',
        'is_active' => 'boolean',
        'engine_config' => 'array',
        'ad_free_expires_at' => 'datetime',
        'ad_module_expires_at' => 'datetime',
        'version_expire_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($player) {
            if (empty($player->slug)) {
                $player->slug = Str::random(16);
            }
            if (empty($player->player_code)) {
                $player->player_code = self::generatePlayerCode();
            }
            if (empty($player->player_key)) {
                $player->player_key = md5(Str::random(32));
            }
        });
    }

    /**
     * 生成10位随机数字播放器ID
     */
    private static function generatePlayerCode(): string
    {
        $code = '';
        for ($i = 0; $i < 10; $i++) {
            $code .= mt_rand(0, 9);
        }
        if (self::where('player_code', $code)->exists()) {
            return self::generatePlayerCode();
        }
        return $code;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(PlayerPlan::class);
    }

    public function engine()
    {
        return $this->belongsTo(PlayerEngine::class, 'engine_code', 'code');
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'user_player_videos', 'player_id', 'video_id')
            ->withPivot(['sort_order', 'is_featured'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function decoration()
    {
        return $this->belongsTo(Decoration::class, 'ad_decoration_id');
    }

    public function ads()
    {
        return $this->hasMany(UserPlayerAd::class, 'player_id');
    }

    public function enabledAds()
    {
        return $this->hasMany(UserPlayerAd::class, 'player_id')
            ->where('enabled', true)
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 检查用户版本是否在有效期内
     */
    public function isVersionActive(): bool
    {
        // 免费版永不过期
        if (($this->version ?? 'free') === 'free') return true;
        // 付费版检查到期时间
        if ($this->version_expire_at && now()->gt($this->version_expire_at)) {
            return false;
        }
        return true;
    }

    /**
     * 获取生效中的版本（过期则降为free）
     */
    public function getEffectiveVersion(): string
    {
        if (!$this->isVersionActive()) {
            return 'free';
        }
        return $this->version ?? 'free';
    }

    /**
     * 检查是否有某个功能权限
     */
    public function hasFeature(string $feature): bool
    {
        // 版本功能权限矩阵
        $versionFeatures = [
            'free' => [],  // 免费版：无自定义功能
            'basic' => ['custom_appearance', 'custom_logo'],  // 基础版：可自定义外观
            'advanced' => ['custom_appearance', 'custom_logo', 'custom_domain'],  // 高级版：+自定义域名
            'flagship' => ['custom_appearance', 'custom_logo', 'custom_domain', 'ad_module', 'ad_free'],  // 旗舰版：+广告模块+去广告
        ];
        
        // 版本过期则降为免费版
        $version = $this->getEffectiveVersion();
        $features = $versionFeatures[$version] ?? [];
        
        // 检查是否通过购买开通了去广告功能（含到期判断）
        if ($feature === 'ad_free' && $this->has_ad_free) {
            if ($this->ad_free_expires_at && now()->gt($this->ad_free_expires_at)) {
                return false; // 已过期
            }
            return true;
        }
        
        // 检查是否通过购买开通了广告模块（含到期判断）
        if ($feature === 'ad_module' && $this->has_ad_module) {
            if ($this->ad_module_expires_at && now()->gt($this->ad_module_expires_at)) {
                return false; // 已过期
            }
            return true;
        }
        
        // 检查是否通过购买开通了超级广告
        if ($feature === 'super_ad' && ($this->has_super_ad || $this->super_material_enabled)) {
            return true;
        }
        
        return in_array($feature, $features);
    }
    
    /**
     * 检查是否可以自定义外观
     */
    public function canCustomizeAppearance(): bool
    {
        return $this->hasFeature('custom_appearance');
    }
    
    /**
     * 检查是否支持自定义域名
     */
    public function canUseCustomDomain(): bool
    {
        return $this->hasFeature('custom_domain');
    }
    
    /**
     * 检查是否有广告模块
     */
    public function hasAdModule(): bool
    {
        return $this->hasFeature('ad_module') || $this->hasFeature('super_ad');
    }

    /**
     * 获取嵌入URL（带验证参数）
     */
    public function getEmbedUrlAttribute(): string
    {
        $pathPrefix = $this->template === 'youku' ? '/youku/player/' : '/embed/player/';
        return url("{$pathPrefix}{$this->slug}?pid={$this->player_code}&pkey={$this->player_key}");
    }

    /**
     * 获取嵌入代码（iframe方式，简单）
     */
    public function getEmbedCodeAttribute(): string
    {
        return '<iframe src="' . $this->embed_url . '" width="' . $this->width . '" height="' . $this->height . '" frameborder="0" allowfullscreen></iframe>';
    }

    /**
     * 获取完整部署HTML代码（支持广告）
     */
    public function getDeployCodeAttribute(): string
    {
        // 通过EngineFactory生成
        $result = \App\Services\Engines\EngineFactory::generateDeployCode(
            $this->engine_code,
            [
                'name' => $this->name,
                'player_id' => $this->player_code ?: $this->id,
                'player_key' => $this->player_key,
                'api_url' => url('/api/player'),
                'video_url' => $this->videos->first()->url ?? '',
                'cover_url' => $this->videos->first()->cover_url ?? '',
                'theme_color' => $this->theme_color ?? '#ff6b00',
                'autoplay' => $this->autoplay,
                'loop_play' => $this->loop_play,
                'muted' => $this->muted,
                'watermark_text' => $this->watermark_text,
                'watermark_position' => $this->watermark_position,
                'width' => $this->width ?: '100%',
                'height' => $this->height ?: '500px',
            ],
            $this->enabledAds()->get()->toArray()
        );
        
        return $result['html'];
    }

    /**
     * 获取部署用的完整URL（不带验证参数，用于自定义域名）
     */
    public function getDeployUrlAttribute(): string
    {
        if ($this->custom_domain_enabled && $this->custom_domain) {
            return "https://{$this->custom_domain}/player/{$this->slug}";
        }
        return url("/embed/player/{$this->slug}");
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * 验证播放器密钥
     */
    public function verifyKey(string $key): bool
    {
        return $this->player_key === $key;
    }
}
