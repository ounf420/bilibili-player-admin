# 苹果CMS播放器接口配置说明

## 接口地址

### 1. 标准接口（推荐）
```
http://你的域名/api/player/vod?id=视频ID
```

### 2. URL接口
```
http://你的域名/api/player/url?vid=视频ID
```

### 3. 苹果CMS标准接口
```
http://你的域名/api/player/maccms?id=视频ID
http://你的域名/api/player/maccms?url=视频地址
```

### 4. 代理接口
```
http://你的域名/api/player/proxy?url=视频地址
```

## 返回格式

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "url": "https://example.com/video.mp4",
        "type": "auto",
        "pic": "https://example.com/cover.jpg",
        "title": "视频标题"
    }
}
```

## 苹果CMS后台配置

### 方法1：自定义播放器接口

1. 登录苹果CMS后台
2. 进入 **视频** → **播放器** → **添加播放器**
3. 填写以下信息：
   - **播放器名称**: 自定义播放器
   - **播放器代码**:
   ```
   <iframe src="http://你的域名/embed/player/播放器slug?pid=播放器ID&pkey=播放器密钥" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
   ```
   - **解析接口**: `http://你的域名/api/player/maccms`

### 方法2：使用解析接口

1. 在苹果CMS后台添加解析接口
2. 接口地址: `http://你的域名/api/player/maccms?id=`

### 方法3：直接调用播放页面

```
http://你的域名/player/vod-视频ID.html
http://你的域名/player/play-视频ID.html
```

## 播放器嵌入代码

### iframe方式（推荐）
```html
<iframe src="http://你的域名/embed/player/播放器slug?pid=播放器ID&pkey=播放器密钥" width="100%" height="500" frameborder="0" allowfullscreen></iframe>
```

### JS脚本方式
```html
<script src="http://你的域名/player-播放器ID.js"></script>
<div class="player-container" id="player"></div>
```

## 参数说明

| 参数 | 说明 | 示例 |
|------|------|------|
| id | 视频ID | 123 |
| vid | 视频ID（另一种格式） | 123 |
| url | 视频地址 | https://example.com/video.mp4 |
| pid | 播放器ID | 2319110234 |
| pkey | 播放器密钥 | 3cfca66dd469cb11e0bee62dc6ac7fb2 |

## 注意事项

1. **视频ID**: 使用数据库中的视频ID（数字）
2. **播放器ID**: 使用10位随机数字（player_code）
3. **播放器密钥**: 使用32位MD5密钥（player_key）
4. **域名**: 替换为你的实际域名

## 测试接口

```bash
# 测试标准接口
curl "http://dem.viesta.cn/api/player/vod?id=1"

# 测试苹果CMS接口
curl "http://dem.viesta.cn/api/player/maccms?id=1"

# 测试URL接口
curl "http://dem.viesta.cn/api/player/url?vid=1"
```

## 常见问题

### Q: 为什么视频无法播放？
A: 检查视频URL是否有效，确保视频格式支持（MP4、M3U8等）

### Q: 如何获取播放器ID和密钥？
A: 在用户中心 → 我的播放器 → 播放器详情页查看

### Q: 支持哪些视频格式？
A: 支持 MP4、M3U8、FLV 等常见格式，使用HLS.js自动识别

### Q: 如何在苹果CMS中使用？
A: 推荐使用iframe嵌入方式，将播放器代码粘贴到苹果CMS的播放器代码框中
