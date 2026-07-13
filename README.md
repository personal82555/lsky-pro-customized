# Lsky Pro 定制版

基于 [Lsky Pro](https://github.com/lsky-org/lsky-pro) 的定制版本，新增了目录导入图片功能。

## 新增功能

### 目录导入（仅管理员）

管理员可以从服务器目录批量导入图片到图床。

**使用方法：**

1. 登录管理员账号
2. 在左侧导航栏点击「目录导入」
3. 输入服务器上的目录路径（支持中文路径）
4. 选择储存策略、目标相册、图片权限
5. 点击「扫描目录」，系统会列出目录下所有支持的图片文件
6. 勾选需要导入的图片（默认全选）
7. 点击「导入选中图片」完成导入

**注意事项：**

- Docker 部署时，宿主机目录需挂载到容器内才能访问
- 例如：宿主机 `/vol2/1000/HD2/壁纸` 挂载到容器内 `/var/www/html/storage/app/import/wallpaper`
- 导入时自动为图片生成缩略图
- 支持递归扫描子目录
- 自动跳过重复图片（相同 md5+sha1）
- 支持的图片格式：jpeg, jpg, png, gif, tif, bmp, ico, psd, webp

## Docker 部署

```bash
git clone https://github.com/personal82555/lsky-pro-customized.git
cd lsky-pro-customized
docker compose up -d
```

如果需要导入宿主机目录的图片，在 `docker-compose.yml` 中添加挂载：

```yaml
services:
  lsky-pro:
    volumes:
      - ./data:/var/www/html
      - /vol2/1000/HD2/lsky:/var/www/html/storage/app/uploads
      - /vol2/1000/HD2/壁纸:/var/www/html/storage/app/import/wallpaper  # 添加需要导入的目录
```

## 原版功能

- 图片上传与管理
- 多种储存策略（本地、S3、OSS、COS、七牛、又拍云等）
- 图片审核（腾讯云、阿里云、NSFW.js）
- 水印功能
- 缩略图生成
- 画廊展示
- API 接口
- 用户管理
- 相册管理
- AI 图片处理
