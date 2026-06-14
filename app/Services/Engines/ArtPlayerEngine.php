<?php

namespace App\Services\Engines;

class ArtPlayerEngine extends BaseEngine
{
    public function getName(): string
    {
        return 'ArtPlayer';
    }

    public function getCode(): string
    {
        return 'artplayer';
    }

    public function getCdnLinks(): array
    {
        return [
            'js' => 'https://cdn.jsdelivr.net/npm/artplayer@5.1.1/dist/artplayer.min.js',
            'css' => null,
            'hls' => 'https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js',
        ];
    }

    public function init(string $container, array $config): string
    {
        $theme = $config['theme_color'] ?? '#00a1d6';
        $autoplay = $config['autoplay'] ? 'true' : 'false';
        $muted = $config['muted'] ? 'true' : 'false';

        return <<<JS
var art = new Artplayer({
    container: '#{$container}',
    url: videoUrl,
    poster: videoPic,
    theme: '{$theme}',
    autoplay: {$autoplay},
    muted: {$muted},
    autoSize: true,
    autoMini: true,
    screenshot: true,
    flip: true,
    playbackRate: true,
    aspectRatio: true,
    fullscreen: true,
    fullscreenWeb: true,
    subtitleOffset: true,
    miniProgressBar: true,
    mutex: true,
    backdrop: true,
    playsInline: true,
    autoPlayback: true,
    airplay: true,
});
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

// HLS支持
if (isHlsUrl(videoUrl)) {
    art.on('ready', function() {
        var hls = new Hls();
        hls.loadSource(videoUrl);
        hls.attachMedia(art.video);
    });
}

window.playerInstance = art;
JS;
    }

    public function generateMaterialCode(array $ads): string
    {
        $adsJson = json_encode($ads);
        return <<<JS
// ArtPlayer广告适配
var materialAds = {$adsJson};
// 使用ArtPlayer的自定义层实现广告
JS;
    }

    public function generateWatermarkCode(array $config): string
    {
        $text = $config['text'] ?? '';
        if (empty($text)) return '';

        return <<<JS
art.layers.add({
    name: 'watermark',
    html: '<div style="position:fixed;top:10px;right:10px;color:rgba(255,255,255,0.3);font-size:14px;pointer-events:none;">{$text}</div>',
});
JS;
    }
}
