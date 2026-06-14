<?php

namespace App\Services\Engines;

class CKPlayerEngine extends BaseEngine
{
    public function getName(): string { return 'CKPlayer'; }
    public function getCode(): string { return 'ckplayer'; }
    
    public function getCdnLinks(): array
    {
        return [
            'js' => 'https://cdn.jsdelivr.net/npm/ckplayer@2.1.0/ckplayer.min.js',
            'css' => null,
            'hls' => null,
        ];
    }

    public function init(string $container, array $config): string
    {
        $theme = $config['theme_color'] ?? '#00a1d6';
        return <<<JS
var videoObject = {
    container: '#{$container}',
    variable: 'player',
    autoplay: true,
    html5Mse: true,
    video: videoUrl,
    loaded: 'loadedHandler',
};
var player = new ckplayer(videoObject);
window.playerInstance = { video: document.querySelector('#{$container} video') };
JS;
    }

    public function generateJs(array $config): string
    {
        $container = $config['container'] ?? 'player';
        $videoUrl = $config['video_url'] ?? '';
        return <<<JS
var videoUrl = '{$videoUrl}';
{$this->init($container, $config)}
JS;
    }

    public function generateMaterialCode(array $ads): string { return '// CKPlayer广告'; }
    public function generateWatermarkCode(array $config): string { return ''; }
}
