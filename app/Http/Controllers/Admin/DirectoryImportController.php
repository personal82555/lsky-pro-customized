<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GroupConfigKey;
use App\Enums\ImagePermission;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Strategy;
use App\Models\User;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Illuminate\View\View;
use Intervention\Image\ImageManager;

class DirectoryImportController extends Controller
{
    private array $blockedDirs = [
        '/', '/etc', '/proc', '/sys', '/dev', '/boot', '/root',
    ];

    public function index(): View
    {
        $strategies = Strategy::all();
        $albums = Auth::user()->albums()->get();
        return view('admin.directory-import.index', compact('strategies', 'albums'));
    }

    public function scan(Request $request): Response
    {
        $request->validate([
            'directory' => 'required|string|max:500',
            'recursive' => 'nullable|boolean',
        ]);

        $directory = trim($request->input('directory'));
        $directory = rtrim($directory, '/');
        $recursive = $request->boolean('recursive', false);

        foreach ($this->blockedDirs as $blocked) {
            if ($directory === $blocked || str_starts_with($directory, $blocked . '/')) {
                return $this->fail('禁止访问系统敏感目录');
            }
        }

        // 如果目录不存在，尝试将宿主机路径映射到容器路径
        if (!is_dir($directory) || !is_readable($directory)) {
            $pathMap = [
                '/vol2/1000/HD2/壁纸' => '/var/www/html/storage/app/import/wallpaper',
            ];
            foreach ($pathMap as $hostPath => $containerPath) {
                if (str_starts_with($directory, $hostPath)) {
                    $mapped = $containerPath . substr($directory, strlen($hostPath));
                    if (is_dir($mapped) && is_readable($mapped)) {
                        $directory = $mapped;
                        break;
                    }
                }
            }
        }

        if (!is_dir($directory) || !is_readable($directory)) {
            return $this->fail('目录不存在或不可读');
        }

        $acceptedExtensions = config('convention.group.accepted_file_suffixes', []);
        $acceptedExtensions = array_map('strtolower', $acceptedExtensions);

        $files = [];

        if ($recursive) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
        } else {
            $iterator = new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS);
        }

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, $acceptedExtensions)) {
                    $sizeKB = $file->getSize() / 1024;
                    $files[] = [
                        'path' => $file->getPathname(),
                        'name' => $file->getFilename(),
                        'size' => $sizeKB,
                        'size_human' => Utils::formatSize($file->getSize()),
                        'extension' => $ext,
                        'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                    ];
                }
            }
        }

        usort($files, fn($a, $b) => $a['name'] <=> $b['name']);

        return $this->success('扫描完成', [
            'directory' => $directory,
            'total' => count($files),
            'files' => $files,
        ]);
    }

    public function import(Request $request): Response
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|string',
            'strategy_id' => 'required|integer|exists:strategies,id',
            'permission' => 'nullable|string|in:public,private',
            'album_id' => 'nullable|integer',
        ]);

        $filePaths = $request->input('files');
        $strategyId = $request->input('strategy_id');
        $permission = $request->input('permission', 'private');
        $albumId = $request->input('album_id');
        $quality = max(1, min(100, (int) $request->input('quality', 100)));

        /** @var User $user */
        $user = Auth::user();

        /** @var Strategy $strategy */
        $strategy = Strategy::find($strategyId);

        /** @var \App\Models\Group $group */
        $group = $user->group;
        $configs = $group->configs;

        $album = null;
        if ($albumId) {
            $album = $user->albums()->find($albumId);
        }

        $filesystem = new Filesystem((new \App\Services\ImageService())->getAdapter($strategy));

        $permissionEnum = $permission === 'public' ? ImagePermission::Public : ImagePermission::Private;

        $results = [
            'success' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($filePaths as $filePath) {
            try {
                $realPath = realpath($filePath);

                if (!$realPath || !is_file($realPath)) {
                    $results['failed']++;
                    $results['errors'][] = ['file' => $filePath, 'reason' => '文件不存在'];
                    continue;
                }

                $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
                $acceptedExtensions = $configs->get(GroupConfigKey::AcceptedFileSuffixes, []);
                if (!in_array($extension, $acceptedExtensions)) {
                    $results['skipped']++;
                    $results['errors'][] = ['file' => $filePath, 'reason' => '不支持的文件类型: ' . $extension];
                    continue;
                }

                $fileSizeKB = filesize($realPath) / 1024;
                $maxSize = $configs->get(GroupConfigKey::MaximumFileSize, 5120);
                if ($fileSizeKB > $maxSize) {
                    $results['skipped']++;
                    $results['errors'][] = ['file' => $filePath, 'reason' => '文件大小超出限制'];
                    continue;
                }

                $md5 = md5_file($realPath);
                $sha1 = sha1_file($realPath);

                $existing = Image::query()
                    ->where('strategy_id', $strategy->id)
                    ->where('md5', $md5)
                    ->where('sha1', $sha1)
                    ->first();

                $filename = $this->replacePathname(
                    $configs->get(GroupConfigKey::PathNamingRule) . '/' . $configs->get(GroupConfigKey::FileNamingRule),
                    $realPath
                );
                $pathname = $filename . ".{$extension}";

                [$width, $height] = @getimagesize($realPath) ?: [400, 400];

                $mimetype = mime_content_type($realPath) ?: 'application/octet-stream';

                $image = new Image();
                $image->user_id = $user->id;
                $image->group_id = $group->id;
                $image->strategy_id = $strategy->id;
                $image->album_id = $album ? $album->id : null;
                $image->fill([
                    'md5' => $md5,
                    'sha1' => $sha1,
                    'path' => $configs->get(GroupConfigKey::PathNamingRule) ? dirname($pathname) : '',
                    'name' => basename($pathname),
                    'origin_name' => pathinfo($realPath, PATHINFO_FILENAME) . '.' . $extension,
                    'size' => $fileSizeKB,
                    'mimetype' => $mimetype,
                    'extension' => $extension,
                    'width' => $width,
                    'height' => $height,
                    'is_unhealthy' => false,
                    'permission' => $permissionEnum,
                    'uploaded_ip' => $request->ip(),
                ]);

                if (is_null($existing)) {
                    // 压缩图片（quality < 100 且非 ico/gif）
                    if ($quality < 100 && !in_array($extension, ['ico', 'gif'])) {
                        $manager = new ImageManager(['driver' => 'imagick']);
                        $handleImage = $manager->make($realPath)->save($extension, $quality);
                        $compressedPath = $handleImage->basePath();
                        $handle = fopen($compressedPath, 'r');
                        try {
                            $filesystem->writeStream($pathname, $handle);
                        } catch (FilesystemException $e) {
                            if (is_resource($handle)) @fclose($handle);
                            $handleImage->destroy();
                            throw $e;
                        }
                        if (is_resource($handle)) @fclose($handle);
                        $handleImage->destroy();
                        // 更新文件大小
                        $image->size = filesize($compressedPath) / 1024;
                    } else {
                        $handle = fopen($realPath, 'r');
                        try {
                            $filesystem->writeStream($pathname, $handle);
                        } catch (FilesystemException $e) {
                            if (is_resource($handle)) @fclose($handle);
                            throw $e;
                        }
                        if (is_resource($handle)) @fclose($handle);
                    }
                } else {
                    $image->path = $existing->path;
                    $image->name = $existing->name;
                }

                DB::transaction(function () use ($image, $user, $album) {
                    if ($image->save()) {
                        $user->increment('image_num');
                        if ($album) {
                            $album->increment('image_num');
                        }
                    } else {
                        throw new \Exception('图片记录保存失败');
                    }
                });

                // 生成缩略图（跳过 ico 和 gif）
                if (!in_array($extension, ['ico', 'gif'])) {
                    try {
                        @ini_set('memory_limit', '2048M');
                        $imageService = new \App\Services\ImageService();
                        $imageService->makeThumbnail($image, $realPath, 400, true);
                        gc_collect_cycles();
                    } catch (\Throwable $e) {
                        // 缩略图生成失败不中断导入
                    }
                }

                $results['success']++;
            } catch (\Throwable $e) {
                Utils::e($e, '目录导入图片时出现异常');
                $results['failed']++;
                $results['errors'][] = ['file' => $filePath, 'reason' => $e->getMessage()];
            }
        }

        return $this->success('导入完成', $results);
    }

    private function replacePathname(string $pathname, string $realPath): string
    {
        $array = [
            '{Y}' => date('Y'),
            '{y}' => date('y'),
            '{m}' => date('m'),
            '{d}' => date('d'),
            '{timestamp}' => time(),
            '{uniqid}' => uniqid(),
            '{md5}' => md5(microtime() . Str::random()),
            '{md5-16}' => substr(md5(microtime() . Str::random()), 0, 16),
            '{str-random-16}' => Str::random(),
            '{str-random-10}' => Str::random(10),
            '{filename}' => pathinfo($realPath, PATHINFO_FILENAME),
            '{uid}' => Auth::check() ? Auth::id() : 0,
        ];
        return str_replace(array_keys($array), array_values($array), $pathname);
    }
}
