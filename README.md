# 🎬 视频播放器管理系统

一套完整的视频播放器广告管理平台，支持多格式视频播放、智能广告投放、用户管理与数据分析。

## ✨ 功能特点

### 🎥 播放器功能
- 支持 HLS (m3u8)、MP4、FLV 等多种视频格式
- 标准版 + 优酷风格两套播放器模板
- 弹幕系统、倍速播放、音量控制
- 选集面板、记忆播放、自动播放
- 全屏模式自动隐藏控制栏
- Logo水印、文字水印自定义

### 📺 广告系统
- 17种广告类型：开屏、前贴片、中贴片、后贴片、暂停、角标、跑马灯等
- 平台广告 + 用户广告双层控制
- 广告素材管理、投放时间控制
- 去广告功能（需购买）

### 👤 用户系统
- 用户注册/登录
- 用户中心（播放器管理、广告管理）
- 卡密充值系统
- 播放器额度管理

### 🔧 后台管理
- Filament 后台管理系统
- 用户管理、视频管理、广告管理
- 卡密管理、订单管理
- 系统设置、数据统计

## 🚀 快速安装

### 环境要求
- PHP >= 8.1
- MySQL >= 5.7
- PHP扩展：openssl, pdo, mbstring, tokenizer, xml, ctype, json, bcmath, fileinfo, curl

### 安装步骤

1. **下载项目**
```bash
git clone https://github.com/ounf420/bilibili-player-admin.git
cd bilibili-player-admin
```

2. **安装依赖**
```bash
composer install --no-dev --optimize-autoloader
```

3. **设置目录权限**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

4. **访问安装向导**

在浏览器中访问你的域名，系统会自动跳转到安装向导：
```
http://你的域名/install
```

按照向导完成：
- 环境检测
- 数据库配置
- 管理员账号设置
- 一键安装

## 📦 开箱即用安装包

下载 `bilibili-player-admin-v1.0.zip`，无需运行 composer install，上传解压后直接访问安装向导。

## 🎮 使用说明

### 播放器访问
- 标准播放器：`/player`
- 优酷风格播放器：`/youku/player/{slug}`
- 嵌入播放器：`/embed/player/{slug}?pid=xxx&pkey=xxx`

### 后台管理
- 访问：`/admin`
- 默认管理员：安装时设置的账号

### 用户中心
- 访问：`/user`
- 用户注册：`/register`

## 📁 项目结构

```
bilibili-player-admin/
├── app/
│   ├── Http/Controllers/    # 控制器
│   ├── Models/              # 数据模型
│   ├── Services/            # 服务层
│   └── Filament/            # 后台管理
├── resources/
│   └── views/               # 视图模板
├── public/                  # 公共资源
├── database/                # 数据库迁移
├── routes/                  # 路由定义
└── storage/                 # 存储目录
```

## 🔧 技术栈

- **后端**：Laravel 12 + PHP 8.1+
- **后台**：Filament v5
- **前端**：原生JS + DPlayer + HLS.js
- **数据库**：MySQL 5.7+
- **UI框架**：Tailwind CSS + Remix Icon

## 📝 更新日志

### v1.0.0 (2026-06-14)
- ✅ 完整的视频播放器系统
- ✅ 17种广告类型支持
- ✅ 用户注册/登录系统
- ✅ 卡密充值系统
- ✅ 后台管理系统
- ✅ 在线安装向导
- ✅ 优酷风格播放器模板
- ✅ 全屏模式自动隐藏控制栏

## 🤝 贡献

欢迎提交 Issue 和 Pull Request！

## 📄 许可证

MIT License

## 🔗 相关链接

- [GitHub仓库](https://github.com/ounf420/bilibili-player-admin)
- [问题反馈](https://github.com/ounf420/bilibili-player-admin/issues)
