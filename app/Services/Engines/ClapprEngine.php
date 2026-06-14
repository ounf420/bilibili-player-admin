<?php

namespace App\Services\Engines;

class ClapprEngine extends BaseEngine
{
    public function getName(): string { return 'Clappr'; }
    public function getCode(): string { return 'clappr'; }
    
    public function getCdnLinks(): array
    {
        return [
            'js' => 'https://cdn.jsdelivr.net/npm/clappr@0.4.3/dist/clappr.min.js',
            'css' => null,
            'hls' => 'https://cdn.jsdelivr.net/npm/@clappr/hls-plugin@0.6.0/dist/clappr-hls-plugin.min.js',
        ];
    }

    public function init(string $container, array $config): string
    {
        $theme = $config['theme_color'] ?? '#2e74b5';
        return <<<JS
var player = new Clappr.Player({
    source: videoUrl,
    parentId: '#{$container}',
    poster: videoPic,
    autoPlay: true,
    mediacontrol: { seekbar: '{$theme}', buttons: '{$theme}' },
    hlsjsConfig: {},
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

    public function generateMaterialCode(array $ads): string { return '// Clappr广告'; }
    public function generateWatermarkCode(array $config): string { return ''; }
}
