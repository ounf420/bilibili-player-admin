<?php

namespace App\Services\Engines;

class MUIPlayerEngine extends BaseEngine
{
    public function getName(): string { return 'MUIPlayer'; }
    public function getCode(): string { return 'muiplayer'; }
    
    public function getCdnLinks(): array
    {
        return [
            'js' => 'https://cdn.jsdelivr.net/npm/mui-player@2.6.3/dist/mui-player.min.js',
            'css' => 'https://cdn.jsdelivr.net/npm/mui-player@2.6.3/dist/mui-player.min.css',
            'hls' => 'https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js',
        ];
    }

    public function init(string $container, array $config): string
    {
        $theme = $config['theme_color'] ?? '#00a1d6';
        return <<<JS
var player = new MUIPlayer({
    container: '#{$container}',
    src: videoUrl,
    poster: videoPic,
    autoplay: true,
    themeColor: '{$theme}',
    config: {
        logo: '',
        title: '',
    }
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

{$this->generateHlsLoader()}
JS;
    }

    public function generateMaterialCode(array $ads): string { return '// MUIPlayer广告'; }
    public function generateWatermarkCode(array $config): string { return ''; }
}
