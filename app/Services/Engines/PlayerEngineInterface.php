<?php

namespace App\Services\Engines;

interface PlayerEngineInterface
{
    /**
     * 初始化播放器
     */
    public function init(string $container, array $config): string;

    /**
     * 生成播放器JavaScript代码
     */
    public function generateJs(array $config): string;

    /**
     * 生成广告注入代码
     */
    public function generateMaterialCode(array $ads): string;

    /**
     * 生成水印代码
     */
    public function generateWatermarkCode(array $config): string;

    /**
     * 获取引擎CDN链接
     */
    public function getCdnLinks(): array;

    /**
     * 获取引擎名称
     */
    public function getName(): string;

    /**
     * 获取引擎代码
     */
    public function getCode(): string;
}
