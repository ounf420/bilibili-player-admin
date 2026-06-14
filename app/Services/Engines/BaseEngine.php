<?php

namespace App\Services\Engines;

abstract class BaseEngine implements PlayerEngineInterface
{
    protected array $config;
    protected array $capabilities;

    public function __construct(array $config = [], array $capabilities = [])
    {
        $this->config = $config;
        $this->capabilities = $capabilities;
    }

    /**
     * 生成通用的HLS检测和加载代码
     */
    protected function generateHlsLoader(): string
    {
        return <<<'JS'
function isHlsUrl(url) {
    return url && (url.includes('.m3u8') || url.includes('hls'));
}

function loadHlsSource(video, url, hlsUrl) {
    if (isHlsUrl(url) && Hls.isSupported()) {
        var hls = new Hls();
        hls.loadSource(url);
        hls.attachMedia(video);
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = url;
    } else {
        video.src = url;
    }
}
JS;
    }

    /**
     * 生成广告系统代码（通用）
     */
    protected function generateMaterialSystem(): string
    {
        return <<<'JS'
class MaterialManager {
    constructor(player, options) {
        this.player = player;
        this.materials = options.materials || [];
        this.currentMaterial = null;
        this.isPlaying = false;
        this.overlay = null;
        this.init();
    }

    init() {
        this.createOverlay();
        this.bindEvents();
    }

    createOverlay() {
        this.overlay = document.createElement('div');
        this.overlay.className = 'material-overlay';
        this.overlay.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;z-index:200;display:none;background:#000;';
        this.player.container.appendChild(this.overlay);
    }

    bindEvents() {
        // 前贴片
        this.player.on('play', () => {
            if (!this.hasPlayed) {
                this.hasPlayed = true;
                this.playMaterial('preroll');
            }
        });
    }

    playMaterial(position) {
        const material = this.materials.find(m => m.position === position);
        if (!material) return;
        // 播放广告逻辑...
    }
}
JS;
    }
}
