<?php

namespace App\Services\Engines;

class XgplayerEngine extends BaseEngine
{
    public function getName(): string { return 'xgplayer'; }
    public function getCode(): string { return 'xgplayer'; }
    
    public function getCdnLinks(): array
    {
        return [
            'js' => 'https://cdn.jsdelivr.net/npm/xgplayer@3.0.9/dist/index.min.js',
            'css' => 'https://cdn.jsdelivr.net/npm/xgplayer@3.0.9/dist/index.min.css',
            'hls' => 'https://cdn.jsdelivr.net/npm/xgplayer-hls.js@3.0.9/dist/index.min.js',
        ];
    }

    public function init(string $container, array $config): string
    {
        $theme = $config['theme_color'] ?? '#00a1d6';
        return <<<JS
var player = new Xgplayer({
    id: '{$container}',
    url: videoUrl,
    poster: videoPic,
    fluid: true,
    download: true,
    pip: true,
    miniplayer: true,
    autoplay: true,
});
window.playerInstance = player;
JS;
    }

    public function generateJs(array $config): string
    {
        $container = $config['container'] ?? 'player';
        $videoUrl = $config['video_url'] ?? '';
        $videoPic = $config['cover_url'] ?? '';
        return <<<JS
var videoUrl = '{$videoUrl}';
var videoPic = '{$videoPic}';
{$this->init($container, $config)}
JS;
    }

    public function generateMaterialCode(array $ads): string { return '// xgplayer广告'; }
    public function generateWatermarkCode(array $config): string { return ''; }
}
