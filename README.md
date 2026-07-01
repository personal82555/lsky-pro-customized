# Lsky Pro 定制版

基于 [Lsky Pro](https://www.lsky.pro/) 图床的定制版本，支持 API 上传时指定相册、独立 Token 管理页面、OCR 图片文字搜索等功能。

## ✨ 主要修改功能

### 1. API 上传支持指定相册 ID

在上传图片时可以通过 `album_id` 参数直接指定目标相册：

```bash
curl -X POST https://your-domain/api/v1/upload \
  -F "file=@image.jpg" \
  -F "album_id=3" \
  -H "Authorization: Bearer ***"
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

### 4. 🔍 OCR 图片文字搜索

基于 [Tesseract OCR](https://github.com/tesseract-ocr/tesseract) 引擎，支持图片内文字识别和搜索：

- **上传时自动识别**：图片上传后自动提取文字并存入数据库
- **搜索图片内容**：在搜索框输入关键词，同时匹配文件名和图片内文字
- **多语言支持**：中文简体 + 英文
- **支持格式**：JPG、PNG、GIF、BMP、TIFF、WebP

```bash
# 搜索示例：在「我的图片」搜索框输入关键词即可
# 例如输入 "软件" 会返回文件名包含"软件"的图片，以及图片内容中包含"软件"文字的图片
```

**安装 OCR 依赖**（Docker 容器内）：

```bash
docker exec <container> apt-get update && apt-get install -y \
  tesseract-ocr tesseract-ocr-chi-sim tesseract-ocr-chi-tra
```

**批量识别已有图片**：

```bash
docker exec <container> php artisan tinker --execute="
\$service = new App\Services\OcrService();
\$images = App\Models\Image::whereNull('ocr_text')->get();
foreach (\$images as \$img) { \$service->processImage(\$img); }
echo 'Done';
"
```

### 5. 我的图片页面优化

- **默认显示全部图片**：进入「我的图片」默认展示所有图片，不再只显示未归类图片
- **相册数量准确**：使用 `withCount` 动态计算相册图片数量
- **切换相册正常**：切换相册后图片列表正确显示，无白屏/空白问题
- **点击查看大图**：点击图片在页内 Viewer 查看器中打开
- **选择框始终可见**：右上角选择按钮始终显示，方便操作
- **拖拽框选**：支持鼠标拖拽多选图片

### 6. 数据库扩展

| 表 | 新增字段 | 类型 | 说明 |
|---|---------|------|------|
| `personal_access_tokens` | `ip` | varchar(45) | Token 创建时的 IP 地址 |
| `personal_access_tokens` | `plain_token` | varchar(500) | Token 明文（用于页面显示） |
| `images` | `ocr_text` | text | OCR 识别的图片文字内容 |

## 📁 修改文件清单

```
app/Http/Controllers/
├── Common/ApiController.php          # API文档页面 - 增加Token和相册显示
├── User/TokenController.php          # 新增 - Token管理控制器

app/Models/
├── Image.php                         # 修改 - ocr_text填充+搜索+动态计数

app/Services/
├── ImageService.php                  # 修改 - 支持album_id参数 + 上传时OCR识别
├── OcrService.php                    # 新增 - Tesseract OCR文字识别服务

database/migrations/
├── 2026_07_01_144919_add_ip_to_personal_access_tokens_table.php
├── 2026_07_01_145836_add_plain_token_to_personal_access_tokens_table.php
└── 2026_07_01_191052_add_ocr_text_to_images_table.php

resources/views/
├── common/api.blade.php              # 修改 - API文档增强
├── layouts/sidebar.blade.php         # 修改 - 添加Token菜单
├── user/images.blade.php             # 修改 - 搜索框优化+Viewer+DragSelect修复
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
