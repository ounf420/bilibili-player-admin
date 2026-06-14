<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $video->title ?? '播放器' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer/dist/DPlayer.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #000; }
        .player-wrapper { width: 100%; height: 100vh; position: relative; }
        #player { width: 100%; height: 100%; }
    </style>
</head>
<body>
    <div class="player-wrapper">
        <div id="player"></div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/hls.js/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dplayer/dist/DPlayer.min.js"></script>
    <script>
        var url = '{{ $video->url }}';
        var pic = '{{ $video->cover_url ?? "" }}';
        var title = '{{ addslashes($video->title ?? "") }}';
        
        var dp = new DPlayer({
            container: document.getElementById('player'),
            autoplay: true,
            theme: '#ff6b00',
            lang: 'zh-cn',
            screenshot: true,
            hotkey: true,
            preload: 'auto',
            volume: 0.7,
            mutex: true,
            video: {
                url: url,
                type: 'auto',
                pic: pic,
                title: title
            }
        });
        
        // 苹果CMS接口兼容
        window.addEventListener('message', function(e) {
            if (e.data && e.data.type === 'maccms') {
                if (e.data.url) {
                    dp.switchVideo({ url: e.data.url, type: 'auto' });
                }
            }
        });
    </script>
</body>
</html>
