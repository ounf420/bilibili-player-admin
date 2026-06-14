<?php

namespace App\Services\Engines;

class EngineFactory
{
    /**
     * 生成完整的播放器部署代码（支持广告）
     */
    public static function generateDeployCode(string $engineCode, array $config, array $ads = []): array
    {
        $playerId = $config['player_id'] ?? '';
        $playerKey = $config['player_key'] ?? '';
        $videoUrl = $config['video_url'] ?? '';
        $coverUrl = $config['cover_url'] ?? '';
        $themeColor = $config['theme_color'] ?? '#ff6b00';
        $loopPlay = !empty($config['loop_play']) ? 'true' : 'false';
        $muted = !empty($config['muted']) ? 'true' : 'false';
        $watermarkText = addslashes($config['watermark_text'] ?? '');
        $watermarkPosition = $config['watermark_position'] ?? 'bottom-right';
        $width = $config['width'] ?? '100%';
        $height = $config['height'] ?? '500px';
        $apiUrl = $config['api_url'] ?? '';
        $playerName = addslashes($config['name'] ?? '播放器');

        $adsJson = json_encode($ads, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);

        $cdn = self::getCdnForEngine($engineCode);
        $initJs = self::getInitJs($engineCode, $config);

        $watermarkHtml = '';
        if ($watermarkText) {
            $watermarkHtml = '<div class="pwm wm-' . htmlspecialchars($watermarkPosition) . '">' . htmlspecialchars($watermarkText) . '</div>';
        }

        $coverSafe = addslashes($coverUrl);
        $videoSafe = addslashes($videoUrl);

        $html = self::buildHtml($playerName, $cdn, $width, $height, $watermarkHtml, $playerId, $playerKey, $apiUrl, $adsJson, $initJs, $themeColor, $videoSafe);

        $js = self::generateJs($playerId, $playerKey, $ads, $themeColor);

        return [
            'html' => $html,
            'js' => $js,
            'cdn' => $cdn,
        ];
    }

    private static function buildHtml(string $playerName, array $cdn, string $width, string $height, string $watermarkHtml, string $playerId, string $playerKey, string $apiUrl, string $adsJson, string $initJs, string $themeColor, string $videoSafe): string
    {
        $css = $cdn['css'] ?? '';
        $js = $cdn['js'] ?? '';

        $html = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . $playerName . '</title>
' . $css . '
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#000}
.pw{width:' . $width . ';height:' . $height . ';max-width:1200px;margin:0 auto;position:relative}
.pc{width:100%;height:100%}
.pwm{position:absolute;z-index:50;color:rgba(255,255,255,.6);font-size:14px;pointer-events:none}
.pwm.wm-bottom-right{bottom:10px;right:10px}
.pwm.wm-bottom-left{bottom:10px;left:10px}
.pwm.wm-top-right{top:10px;right:10px}
.pwm.wm-top-left{top:10px;left:10px}
#ad-box{position:absolute;top:0;left:0;width:100%;height:100%;background:#000;z-index:100;display:flex;flex-direction:column;align-items:center;justify-content:center}
.ad-tag{position:absolute;top:10px;left:10px;background:' . $themeColor . ';color:#fff;padding:4px 12px;border-radius:4px;font-size:14px}
.ad-timer{position:absolute;top:10px;right:10px;background:rgba(0,0,0,.7);color:#fff;padding:4px 12px;border-radius:4px;font-size:14px}
.ad-skip{display:none;position:absolute;bottom:20px;right:20px;background:' . $themeColor . ';color:#fff;padding:8px 16px;border-radius:4px;cursor:pointer;font-size:14px}
.ad-more{position:absolute;bottom:20px;left:20px;color:#fff;text-decoration:underline;font-size:14px}
</style>
</head>
<body>
<div class="pw">
<div id="player" class="pc"></div>
' . $watermarkHtml . '
</div>

' . $js . '
<script>
(function(){
"use strict";

var PLAYER_ID="' . $playerId . '";
var PLAYER_KEY="' . $playerKey . '";
var API_URL="' . $apiUrl . '";
var ADS=' . $adsJson . ';

var adSt={cur:null,on:false,timer:null,played:{}};

function showAd(ad){
  adSt.cur=ad;adSt.on=true;
  var el=document.getElementById("player");
  var v=el.querySelector("video");
  if(v)v.pause();

  var box=document.createElement("div");
  box.id="ad-box";

  var inner=\'<div class="ad-tag">推广<\/div>\';
  inner+=\'<div class="ad-timer" id="ad-cd">\' + (ad.duration||5) + \'秒<\/div>\';
  if(ad.skip_seconds){
    inner+=\'<div id="ad-skip" class="ad-skip" onclick="window._skipAd()">跳过广告<\/div>\';
  }

  var content="";
  if(ad.type==="video"){
    content=\'<video id="ad-vid" src="\' + ad.media_url + \'" style="max-width:100%;max-height:80%" autoplay><\/video>\';
  }else if(ad.type==="image"){
    var href=ad.click_url||"#";
    content=\'<a href="\' + href + \'" target="_blank"><img src="\' + ad.media_url + \'" style="max-width:100%;max-height:80%"><\/a>\';
  }else{
    content=\'<div style="color:#fff;font-size:20px;padding:20px;text-align:center">\' + (ad.content||"") + \'<\/div>\';
  }
  inner+=\'<div style="flex:1;display:flex;align-items:center;justify-content:center">\' + content + \'<\/div>\';

  if(ad.click_url){
    inner+=\'<a href="\' + ad.click_url + \'" target="_blank" class="ad-more">了解更多<\/a>\';
  }

  box.innerHTML=inner;
  el.style.position="relative";
  el.appendChild(box);

  var rem=ad.duration||5;
  adSt.timer=setInterval(function(){
    rem--;
    var cd=document.getElementById("ad-cd");
    if(cd)cd.textContent=rem+"秒";
    var sk=document.getElementById("ad-skip");
    if(ad.skip_seconds && sk && rem<=ad.duration-ad.skip_seconds){
      sk.style.display="block";
    }
    if(rem<=0)hideAd();
  },1000);
}

function hideAd(){
  clearInterval(adSt.timer);
  var box=document.getElementById("ad-box");
  if(box)box.remove();
  adSt.on=false;adSt.cur=null;
}

window._skipAd=function(){hideAd();};

function initPlayer(url){
  ' . $initJs . '
  if(dp){
    dp.on("play",function(){
      if(!adSt.on && ADS.length>0){
        for(var i=0;i<ADS.length;i++){
          if(ADS[i].position==="preroll" && !adSt.played[ADS[i].id]){
            dp.pause();
            showAd(ADS[i]);
            adSt.played[ADS[i].id]=true;
            return;
          }
        }
      }
    });
  }
  return dp;
}

function load(){
  var p=new URLSearchParams(window.location.search);
  var url=p.get("url")||"' . $videoSafe . '";
  if(!url){
    document.getElementById("player").innerHTML="<div style=\\"color:#fff;text-align:center;padding:50px\\">请添加视频：?url=视频地址<\/div>";
    return;
  }
  initPlayer(url);
}

if(document.readyState==="loading"){
  document.addEventListener("DOMContentLoaded",load);
}else{
  load();
}

})();
</script>
</body>
</html>';

        return $html;
    }

    private static function getCdnForEngine(string $engineCode): array
    {
        if ($engineCode === 'artplayer') {
            return [
                'css' => '',
                'js' => '<script src="https://cdn.jsdelivr.net/npm/artplayer/dist/artplayer.js"></script>',
            ];
        } elseif ($engineCode === 'ckplayer') {
            return [
                'css' => '',
                'js' => '<script src="https://cdn.jsdelivr.net/npm/ckplayer/ckplayer.min.js"></script>',
            ];
        } elseif ($engineCode === 'xgplayer') {
            return [
                'css' => '',
                'js' => '<script src="https://cdn.jsdelivr.net/npm/xgplayer/dist/index.min.js"></script>',
            ];
        } else {
            return [
                'css' => '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer/dist/DPlayer.min.css">',
                'js' => '<script src="https://cdn.jsdelivr.net/npm/hls.js/dist/hls.min.js"></script><script src="https://cdn.jsdelivr.net/npm/dplayer/dist/DPlayer.min.js"></script>',
            ];
        }
    }

    private static function getInitJs(string $engineCode, array $config): string
    {
        $themeColor = $config['theme_color'] ?? '#ff6b00';
        $loopPlay = !empty($config['loop_play']) ? 'true' : 'false';
        $muted = !empty($config['muted']) ? 'true' : 'false';
        $coverUrl = addslashes($config['cover_url'] ?? '');

        if ($engineCode === 'artplayer') {
            return 'var dp=new Artplayer({container:"#player",url:url,theme:"' . $themeColor . '",autoplay:false,loop:' . $loopPlay . ',muted:' . $muted . ',autoSize:true,autoMini:true,fullscreen:true,fullscreenWeb:true,mutex:true});';
        } elseif ($engineCode === 'ckplayer') {
            return 'var dp;var vo={container:"#player",variable:"dp",autoplay:false,video:url};dp=new ckplayer(vo);';
        } elseif ($engineCode === 'xgplayer') {
            return 'var dp=new Xgplayer({id:"player",url:url,autoplay:false,loop:' . $loopPlay . ',download:true,fullscreen:true,pip:true});';
        } else {
            return 'var dp=new DPlayer({container:document.getElementById("player"),autoplay:false,theme:"' . $themeColor . '",' . ($loopPlay === 'true' ? 'loop:true' : 'loop:false') . ',lang:"zh-cn",screenshot:true,hotkey:true,preload:"auto",volume:.7,mutex:true,video:{url:url,type:"auto",pic:"' . $coverUrl . '"}});';
        }
    }

    private static function generateJs(string $playerId, string $playerKey, array $ads, string $themeColor): string
    {
        $adsJson = json_encode($ads, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
        $timestamp = date('Y-m-d H:i:s');

        return '/** 
 * 播放器广告引擎 
 * ID: ' . $playerId . '
 * 生成时间: ' . $timestamp . '
 * 包含广告: ' . count($ads) . ' 个
 */
(function(){
"use strict";

var PLAYER_ID="' . $playerId . '";
var PLAYER_KEY="' . $playerKey . '";
var ADS=' . $adsJson . ';
var THEME_COLOR="' . $themeColor . '";

var adState = {
    current: null,
    isPlaying: false,
    timer: null,
    played: {}
};

function showAd(ad, container) {
    adState.current = ad;
    adState.isPlaying = true;
    
    // 暂停视频
    var video = container.querySelector("video");
    if (video) video.pause();
    
    // 创建广告层
    var adBox = document.createElement("div");
    adBox.id = "ad-overlay-" + Date.now();
    adBox.style.cssText = "position:absolute;top:0;left:0;width:100%;height:100%;background:#000;z-index:1000;display:flex;flex-direction:column;align-items:center;justify-content:center;";
    
    var html = \'<div style="position:absolute;top:10px;left:10px;background:\' + THEME_COLOR + \';color:#fff;padding:4px 12px;border-radius:4px;font-size:14px;">推广</div>\';
    html += \'<div style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,0.7);color:#fff;padding:4px 12px;border-radius:4px;font-size:14px;" class="ad-countdown">\' + (ad.duration || 5) + \'秒</div>\';
    
    if (ad.type === "video") {
        html += \'<video src="\' + ad.media_url + \'" style="max-width:100%;max-height:80%" autoplay></video>\';
    } else if (ad.type === "image") {
        var href = ad.click_url || "#";
        html += \'<a href="\' + href + \'" target="_blank"><img src="\' + ad.media_url + \'" style="max-width:100%;max-height:80%"></a>\';
    } else {
        html += \'<div style="color:#fff;font-size:20px;padding:20px;text-align:center">\' + (ad.content || "") + \'</div>\';
    }
    
    if (ad.click_url) {
        html += \'<a href="\' + ad.click_url + \'" target="_blank" style="position:absolute;bottom:20px;left:20px;color:#fff;text-decoration:underline;font-size:14px;">了解更多</a>\';
    }
    
    adBox.innerHTML = html;
    container.style.position = "relative";
    container.appendChild(adBox);
    
    // 倒计时
    var remaining = ad.duration || 5;
    adState.timer = setInterval(function() {
        remaining--;
        var countdown = adBox.querySelector(".ad-countdown");
        if (countdown) countdown.textContent = remaining + "秒";
        if (remaining <= 0) hideAd(adBox);
    }, 1000);
}

function hideAd(adBox) {
    clearInterval(adState.timer);
    if (adBox) adBox.remove();
    adState.isPlaying = false;
    adState.current = null;
}

// 暴露API
window.PlayerAdEngine = {
    ads: function() { return ADS; },
    showAd: showAd,
    hideAd: hideAd,
    isPlaying: function() { return adState.isPlaying; }
};

// 自动初始化（如果页面有.player-container元素）
document.addEventListener("DOMContentLoaded", function() {
    var containers = document.querySelectorAll(".player-container, #player");
    containers.forEach(function(container) {
        // 前贴片广告
        var prerollAds = ADS.filter(function(ad) { return ad.position === "preroll"; });
        if (prerollAds.length > 0 && !adState.played[prerollAds[0].id]) {
            showAd(prerollAds[0], container);
            adState.played[prerollAds[0].id] = true;
        }
    });
});

})();';
    }
}
