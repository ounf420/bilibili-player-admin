# 播放器小项目

## 使用方法

### 1. 修改配置（config.js）

只需填写3个参数：

```javascript
const PLAYER_CONFIG = {
    site_url: 'http://dem.viesta.cn',        // 播放器站点
    player_id: '2319110234',                 // 播放器ID（10位数字）
    player_key: '3cfca66dd469cb11e0bee62dc6ac7fb2',  // 密钥（32位）
};
```

### 2. 上传到你的网站

上传 `index.html` 和 `config.js` 到任意目录即可：
- 二级目录：`http://你的网站.com/player/`
- 子目录：`http://你的网站.com/tools/player/`

### 3. 输入视频地址，开始播放

## 关于广告

**广告由平台自动管理，无需任何配置。**

- 平台广告（开屏/前贴片/中贴片/暂停等）自动展示
- 用户在后台创建的自定义广告自动生效
- 广告规则由播放器版本和功能决定

## 获取播放器参数

1. 登录播放器后台 → 我的播放器 → 播放器详情
2. 复制**播放器ID**（10位数字）和**密钥**（32位字符串）
3. 填入 `config.js` 即可

## 文件结构

```
player-widget/
├── index.html   # 主页面
├── config.js    # 配置文件（改这里）
└── README.md    # 说明
```
