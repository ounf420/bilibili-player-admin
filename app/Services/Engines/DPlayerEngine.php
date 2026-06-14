<?php

namespace App\Services\Engines;

class DPlayerEngine extends BaseEngine
{
    public function getName(): string
    {
        return 'DPlayer';
    }

    public function getCode(): string
    {
        return 'dplayer';
    }

    public function getCdnLinks(): array
    {
        return [
            'js' => 'https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js',
            'css' => 'https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css',
            'hls' => 'https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js',
        ];
    }

    public function init(string $container, array $config): string
    {
        $theme = $config['theme_color'] ?? '#b7daff';
        $autoplay = $config['autoplay'] ? 'true' : 'false';
        $loop = $config['loop_play'] ? 'true' : 'false';
        $muted = $config['muted'] ? 'true' : 'false';

        return <<<JS
var dp = new DPlayer({
    container: document.getElementById('{$container}'),
    theme: '{$theme}',
    autoplay: {$autoplay},
    loop: {$loop},
    screenshot: true,
    hotkey: true,
    preload: 'auto',
    volume: 0.7,
    mutex: true,
    video: {
        url: videoUrl,
        pic: videoPic,
        type: 'auto'
    }
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

// HLS支持
{$this->generateHlsLoader()}

// 播放器实例
window.playerInstance = dp;
JS;
    }

    public function generateMaterialCode(array $ads): string
    {
        $adsJson = json_encode($ads);
        return <<<JS
var materialAds = {$adsJson};
var materialMgr = new MaterialManager(window.playerInstance, { materials: materialAds });
JS;
    }

    public function generateWatermarkCode(array $config): string
    {
        $text = $config['text'] ?? '';
        $position = $config['position'] ?? 'top-right';
        
        if (empty($text)) return '';

        return <<<JS
var watermark = document.createElement('div');
watermark.style.cssText = 'position:fixed;z-index:999;color:rgba(255,255,255,0.3);font-size:14px;pointer-events:none;';
watermark.className = '{$position}';
watermark.textContent = '{$text}';
document.body.appendChild(watermark);
JS;
    }
}
