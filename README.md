# Lsky Pro 定制版

基于 [Lsky Pro](https://www.lsky.pro/) 图床的定制版本，支持 API 上传时指定相册、独立 Token 管理页面等功能。

## ✨ 主要修改功能

### 1. API 上传支持指定相册 ID

在上传图片时可以通过 `album_id` 参数直接指定目标相册：

```bash
curl -X POST https://your-domain/api/v1/upload \
  -F "file=@image.jpg" \
  -F "album_id=3" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

- `album_id` 为可选参数，不传则保存到默认相册
- 验证相册归属，防止上传到他人相册

### 2. 独立 Token 管理页面

在侧边栏「我的」分类下新增「🔑 我的 Token」页面：

- 查看所有 Token 列表（分页显示，每页 10 条）
- 快速创建新 Token
- 显示 Token 来源 IP、创建时间、最后使用时间
- 显示 Token 明文（点击复制）
- 一键删除 Token

### 3. API 文档页面增强

`/api` 页面新增功能：

- 动态显示所有相册列表（从数据库读取）
- Token 管理区域（显示最新 10 个 Token）
- 来源 IP 追踪
- 创建来源自动识别（Hermes Agent / Python脚本 / 值得买 / 邮箱登录）

### 4. 数据库扩展

`personal_access_tokens` 表新增字段：

| 字段 | 类型 | 说明 |
|------|------|------|
| `ip` | varchar(45) | Token 创建时的 IP 地址 |
| `plain_token` | varchar(500) | Token 明文（用于页面显示） |

## 📁 修改文件清单

```
app/Http/Controllers/
├── Common/ApiController.php          # API文档页面 - 增加Token和相册显示
├── User/TokenController.php          # 新增 - Token管理控制器

app/Services/
├── ImageService.php                  # 修改 - 支持album_id参数

database/migrations/
├── 2026_07_01_144919_add_ip_to_personal_access_tokens_table.php
└── 2026_07_01_145836_add_plain_token_to_personal_access_tokens_table.php

resources/views/
├── common/api.blade.php              # 修改 - API文档增强
├── layouts/sidebar.blade.php         # 修改 - 添加Token菜单
└── user/tokens.blade.php             # 新增 - Token管理页面

routes/
└── web.php                           # 修改 - 添加Token路由
```

## 🚀 安装

```bash
git clone https://github.com/personal82555/lsky-pro-customized.git
cd lsky-pro-customized
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## 📝 License

MIT License - 基于 [Lsky Pro](https://github.com/XP-Creator/laravel-picture-bed) 修改
