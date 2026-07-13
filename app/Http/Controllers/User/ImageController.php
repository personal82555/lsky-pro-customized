<?php

namespace App\Http\Controllers\User;

use App\Enums\ImagePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImageRenameRequest;
use App\Models\Album;
use App\Models\Image;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ImageController extends Controller
{
    public function index(): View
    {
        return view('user.images');
    }

    public function images(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();

        $images = $user->images()->filter($request)->with('group', 'strategy')->paginate(40);
        $images->getCollection()->each(function (Image $image) {
            // 图片宽高过小会导致前端排版异常
            $image->width = max($image->width, 200);
            $image->height = max($image->height, 200);

            $image->human_date = $image->created_at->diffForHumans();
            $image->date = $image->created_at->format('Y-m-d H:i:s');
            $image->append(['url', 'thumb_url', 'filename', 'links'])->setVisible([
                'id', 'filename', 'url', 'thumb_url', 'human_date', 'date', 'size', 'width', 'height', 'links'
            ]);
        });
        return $this->success('success', compact('images'));
    }

    public function image(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var Image $image */
        if (!$image = $user->images()->find($request->route('id'))) {
            return $this->fail('未找到该图片');
        }
        $image->strategy?->setVisible(['name']);
        $image->album?->setVisible(['name']);
        $image->append(['url', 'thumb_url', 'filename', 'links'])->setVisible([
            'id', 'filename', 'origin_name', 'url', 'thumb_url', 'width', 'height', 'size', 'mimetype', 'md5', 'sha1',
            'permission', 'strategy', 'album', 'uploaded_ip', 'links', 'created_at'
        ]);
        return $this->success('success', compact('image'));
    }

    public function permission(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $permission = $request->input('permission');
        $permissions = ['public' => ImagePermission::Public, 'private' => ImagePermission::Private];
        if (!in_array($permission, array_keys($permissions))) {
            return $this->fail('设置失败');
        }
        $user->images()->whereIn('id', (array) $request->input('ids'))->update([
            'permission' => $permissions[$permission],
        ]);
        return $this->success('设置成功');
    }

    public function rename(ImageRenameRequest $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var Image $image */
        if ($image = $user->images()->find($request->input('id'))) {
            $image->alias_name = $request->input('name');
            $image->save();
        }

        return $this->success('重命名成功', $image->only('id', 'filename'));
    }

    public function movement(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        DB::transaction(function () use ($user, $request) {
            /** @var null|Album $album */
            $album = $user->albums()->find((int) $request->input('id'));
            $user->images()->whereIn('id', $request->input('selected'))->update([
                'album_id' => $album->id ?? null,
            ]);
            if ($album) {
                $album->image_num = $album->images()->count();
                $album->save();
            }
            if ($albumId = (int) $request->input('album_id')) {
                /** @var Album $originAlbum */
                $originAlbum = $user->albums()->find($albumId);
                $originAlbum->image_num = $originAlbum->images()->count();
                $originAlbum->save();
            }
        });
        return $this->success('移动成功');
    }

    public function edit(Request $request, string $id): Response
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var Image|null $image */
        $image = $user->images()->find($id);
        if (is_null($image)) {
            return $this->fail('图片不存在');
        }
        if (!$request->hasFile('image')) {
            return $this->fail('请选择要上传的图片');
        }
        $file = $request->file('image');
        if (!$file->isValid()) {
            return $this->fail('图片上传失败');
        }
        try {
            // Read the uploaded image
            $resource = imagecreatefromstring(file_get_contents($file->getRealPath()));
            if (!$resource) {
                return $this->fail('无法读取图片');
            }
            // Get image dimensions
            $width = imagesx($resource);
            $height = imagesy($resource);
            // Build full path from strategy root + pathname
            $root = $image->strategy?->configs?->get('root', '');
            $fullPath = rtrim($root, '/') . '/' . ltrim($image->pathname, '/');
            $pathDir = dirname($fullPath);
            if (!is_dir($pathDir)) {
                mkdir($pathDir, 0755, true);
            }
            // Determine output format
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg'])) {
                imagejpeg($resource, $fullPath, 95);
            } elseif ($extension === 'png') {
                imagepng($resource, $fullPath);
            } else {
                // Default to JPEG
                imagejpeg($resource, $fullPath, 95);
            }
            imagedestroy($resource);
            // Update image dimensions in database
            $image->update([
                'width' => $width,
                'height' => $height,
                'size' => filesize($fullPath),
            ]);
            // Regenerate thumbnail
            try {
                $imageService = new \App\Services\ImageService();
                $imageService->makeThumbnail($image, $fullPath, 400, true);
            } catch (\Exception $e) {
                // Thumbnail generation is not critical
            }
            return $this->success('编辑保存成功');
        } catch (\Exception $e) {
            return $this->fail('编辑失败: ' . $e->getMessage());
        }
    }


    public function delete(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        (new UserService())->deleteImages($request->all() ?: [], $user);
        return $this->success('删除成功');
    }
}
