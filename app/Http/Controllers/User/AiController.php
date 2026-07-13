<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AiController extends Controller
{
    private function getConfig(): array
    {
        $all = Cache::rememberForever('configs', fn() => Utils::config()->toArray());
        return [
            'service_url'  => $all['ai_service_url'] ?? 'http://lsky-ai:8077',
            'service_key'  => $all['ai_service_key'] ?? '',
            'service_model'=> $all['ai_service_model'] ?? '',
            'generate' => [
                'provider' => $all['ai_generate_provider'] ?? 'newapi',
                'api_url'  => $all['ai_generate_api_url'] ?? 'http://192.168.68.19:3099',
                'api_key'  => $all['ai_generate_api_key'] ?? '',
                'model'    => $all['ai_generate_model'] ?? 'dall-e-3',
            ],
        ];
    }

    public function index(Request $request): View
    {
        $imageUrl = $request->query('image_url', '');
        $imageId = $request->query('image_id', '');
        return view('user.ai', [
            'config' => $this->getConfig(),
            'imageUrl' => $imageUrl,
            'imageId' => $imageId,
        ]);
    }

    public function process(Request $request)
    {
        $config = $this->getConfig();
        $operation = $request->input('operation');
        $params = $request->only(['scale', 'style', 'prompt', 'negative_prompt', 'width', 'height', 'model']);

        if (empty($params['model']) && !empty($config['service_model'])) {
            $params['model'] = $config['service_model'];
        }

        $serviceUrl = rtrim($config['service_url'], '/');

        try {
            $url = $serviceUrl . '/' . $this->mapOperation($operation);

            $http = Http::timeout($operation === 'generate' ? 120 : 60);
            if (!empty($config['service_key'])) {
                $http = $http->withHeader('Authorization', 'Bearer ' . $config['service_key']);
            }

            if ($operation === 'generate') {
                $gen = $config['generate'];
                $params['provider'] = $gen['provider'];
                $params['api_url'] = $gen['api_url'];
                $params['api_key'] = $gen['api_key'];
                $params['model'] = $params['model'] ?: $gen['model'];
                $response = $http->post($url, $params);
            } elseif ($operation === 'merge') {
                $files = $request->file('files');
                if (!is_array($files) || count($files) < 2) {
                    return response()->json(['error' => '请至少上传2张图片'], 400);
                }
                $params['layout'] = $request->input('layout', 'horizontal');
                foreach ($files as $file) {
                    $http = $http->attach(
                        'files[]',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    );
                }
                $response = $http->post($url, $params);
            } else {
                if (!$request->hasFile('file')) {
                    return response()->json(['error' => '请上传图片'], 400);
                }
                $file = $request->file('file');
                $response = $http->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post($url, $params);
            }

            if ($response->successful()) {
                $resultImage = $this->saveResult($request, $response->body(), $operation);
                return response()->json([
                    'success' => true,
                    'url'     => $resultImage['url'] ?? '',
                    'message' => '处理完成',
                ]);
            }
            return response()->json(['error' => 'AI 服务返回错误: ' . $response->status()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'AI 服务不可用: ' . $e->getMessage()], 502);
        }
    }

    public function config(): View
    {
        return view('user.ai_config', ['configs' => Cache::rememberForever('configs', fn() => Utils::config())]);
    }

    public function saveConfig(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            \App\Models\Config::query()->where('name', $key)->update(['value' => $value]);
        }
        Cache::forget('configs');
        return redirect()->route('ai.config')->with('success', '配置已保存');
    }

    public function health()
    {
        $config = $this->getConfig();
        try {
            $response = Http::timeout(5)->get(rtrim($config['service_url'], '/') . '/health');
            $online = $response->successful();
            return response()->json([
                'status' => $online ? 'online' : 'error',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'offline']);
        }
    }

    private function mapOperation(string $operation): string
    {
        return match ($operation) {
            'enhance' => 'enhance',
            'remove_bg' => 'remove-bg',
            'style' => 'style',
            'generate' => 'generate',
            'watermark' => 'watermark',
            'remove_watermark' => 'remove-watermark',
            'text_overlay' => 'text-overlay',
            'merge' => 'merge',
            'poster' => 'poster',
            'product' => 'product',
            default => $operation,
        };
    }

    private function saveResult(Request $request, string $imageContent, string $operation): array
    {
        $user = Auth::user();
        $datePath = date('Y/m/d');
        $filename = 'ai_' . $operation . '_' . time() . '.png';
        $savePath = storage_path('app/uploads/' . $datePath);

        if (!is_dir($savePath)) {
            @mkdir($savePath, 0755, true);
        }
        $fullPath = $savePath . '/' . $filename;
        file_put_contents($fullPath, $imageContent);

        try {
            $token = $user->tokens()->first();
            if (!$token) {
                $token = $user->createToken('ai-processor');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token->plain_token,
                'Accept' => 'application/json',
            ])->attach('image', file_get_contents($fullPath), $filename)
              ->post(url('/api/v1/upload'));

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['links']['url'])) {
                    @unlink($fullPath);
                    return [
                        'url' => $data['data']['links']['url'],
                        'filename' => $data['data']['name'] ?? $filename,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AI upload failed: ' . $e->getMessage());
        }

        return [
            'url' => url('/i/' . $datePath . '/' . $filename),
            'filename' => $filename,
        ];
    }
}
