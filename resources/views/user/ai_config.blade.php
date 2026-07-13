@section('title', 'AI 超能配置')

<x-app-layout>
    <div class="my-6 md:my-9">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-1">
                <i class="fas fa-brain text-indigo-500 mr-2"></i>AI 超能配置
            </h1>
            <p class="text-gray-500 text-sm">配置 AI 图片处理服务和文生图提供商</p>
        </div>

        {{-- AI Service Status --}}
        <div class="relative p-4 rounded-md bg-white shadow-custom" style="margin-bottom:3rem">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-gray-700">🤖 AI 服务状态</span>
                <span id="ai-status-badge" class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-500">
                    检测中...
                </span>
            </div>
            <p class="text-xs text-gray-400">自动检测 AI 处理服务是否在线</p>
        </div>

        <form action="{{ route('ai.config.save') }}" method="POST" class="js-ajax">
            @csrf

            {{-- AI Image Processing Config --}}
            <div class="relative p-4 rounded-md bg-white shadow-custom" style="margin-bottom:3rem">
                <h2 class="text-lg font-semibold text-gray-700 mb-4"><i class="fas fa-magic text-indigo-500 mr-2"></i>AI 图片处理</h2>

                <div class="mb-4">
                    <label for="ai_service_url" class="block text-sm font-medium text-gray-700 mb-1">AI 服务地址</label>
                    <x-input type="text" name="ai_service_url" id="ai_service_url" value="{{ $configs->get('ai_service_url') ?? 'http://lsky-ai:8077' }}" placeholder="http://lsky-ai:8077"/>
                    <p class="text-xs text-gray-400 mt-1">用于超分辨率、抠图去背景、风格化</p>
                </div>
                <div class="mb-4">
                    <label for="ai_service_key" class="block text-sm font-medium text-gray-700 mb-1">API Key（可选）</label>
                    <x-input type="password" name="ai_service_key" id="ai_service_key" value="{{ $configs->get('ai_service_key') ?? '' }}" placeholder="如有鉴权则填写"/>
                </div>
                <div class="mb-4">
                    <label for="ai_service_model" class="block text-sm font-medium text-gray-700 mb-1">默认模型（可选）</label>
                    <x-input type="text" name="ai_service_model" id="ai_service_model" value="{{ $configs->get('ai_service_model') ?? '' }}" placeholder="如 gpt-4o-mini、stable-diffusion"/>
                    <p class="text-xs text-gray-400 mt-1">文生图/风格化使用的模型名称</p>
                </div>
            </div>

            {{-- Text-to-Image Config --}}
            <div class="relative p-4 rounded-md bg-white shadow-custom" style="margin-bottom:3rem">
                <h2 class="text-lg font-semibold text-gray-700 mb-4"><i class="fas fa-wand-magic-sparkles text-yellow-500 mr-2"></i>文生图配置</h2>

                <div class="mb-4">
                    <label for="ai_generate_provider" class="block text-sm font-medium text-gray-700 mb-1">提供商</label>
                    <select name="ai_generate_provider" id="ai_generate_provider" class="mt-1 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0">
                        <option value="newapi" {{ ($configs->get('ai_generate_provider') ?? 'newapi') === 'newapi' ? 'selected' : '' }}>New API / One API（兼容 OpenAI）</option>
                        <option value="openai" {{ ($configs->get('ai_generate_provider') ?? '') === 'openai' ? 'selected' : '' }}>OpenAI 官方</option>
                        <option value="siliconflow" {{ ($configs->get('ai_generate_provider') ?? '') === 'siliconflow' ? 'selected' : '' }}>SiliconFlow（硅基流动）</option>
                        <option value="aliyun" {{ ($configs->get('ai_generate_provider') ?? '') === 'aliyun' ? 'selected' : '' }}>阿里云百炼</option>
                        <option value="tencent" {{ ($configs->get('ai_generate_provider') ?? '') === 'tencent' ? 'selected' : '' }}>腾讯混元</option>
                        <option value="baidu" {{ ($configs->get('ai_generate_provider') ?? '') === 'baidu' ? 'selected' : '' }}>百度千帆</option>
                        <option value="custom" {{ ($configs->get('ai_generate_provider') ?? '') === 'custom' ? 'selected' : '' }}>自定义（OpenAI 兼容）</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">选择文生图服务提供商</p>
                </div>

                <div class="mb-4">
                    <label for="ai_generate_api_url" class="block text-sm font-medium text-gray-700 mb-1">API 地址</label>
                    <x-input type="text" name="ai_generate_api_url" id="ai_generate_api_url" value="{{ $configs->get('ai_generate_api_url') ?? '' }}" placeholder="http://192.168.68.19:3099"/>
                    <p class="text-xs text-gray-400 mt-1">各服务的 API 根地址，选择提供商后自动填充</p>
                </div>
                <div class="mb-4">
                    <label for="ai_generate_api_key" class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                    <x-input type="password" name="ai_generate_api_key" id="ai_generate_api_key" value="{{ $configs->get('ai_generate_api_key') ?? '' }}" placeholder="sk-..."/>
                </div>
                <div class="mb-4">
                    <label for="ai_generate_model" class="block text-sm font-medium text-gray-700 mb-1">默认模型</label>
                    <select name="ai_generate_model" id="ai_generate_model" class="mt-1 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0">
                        <optgroup label="OpenAI">
                            <option value="dall-e-3" {{ ($configs->get('ai_generate_model') ?? 'dall-e-3') == 'dall-e-3' ? 'selected' : '' }}>DALL-E 3</option>
                            <option value="dall-e-2" {{ $configs->get('ai_generate_model') == 'dall-e-2' ? 'selected' : '' }}>DALL-E 2</option>
                        </optgroup>
                        <optgroup label="Stability AI">
                            <option value="stable-diffusion-3" {{ $configs->get('ai_generate_model') == 'stable-diffusion-3' ? 'selected' : '' }}>Stable Diffusion 3</option>
                            <option value="stable-diffusion-3.5" {{ $configs->get('ai_generate_model') == 'stable-diffusion-3.5' ? 'selected' : '' }}>Stable Diffusion 3.5</option>
                            <option value="sdxl" {{ $configs->get('ai_generate_model') == 'sdxl' ? 'selected' : '' }}>SDXL</option>
                            <option value="sd3.5" {{ $configs->get('ai_generate_model') == 'sd3.5' ? 'selected' : '' }}>SD 3.5</option>
                        </optgroup>
                        <optgroup label="Black Forest Labs">
                            <option value="flux" {{ $configs->get('ai_generate_model') == 'flux' ? 'selected' : '' }}>Flux</option>
                            <option value="flux-pro" {{ $configs->get('ai_generate_model') == 'flux-pro' ? 'selected' : '' }}>Flux Pro</option>
                            <option value="flux-dev" {{ $configs->get('ai_generate_model') == 'flux-dev' ? 'selected' : '' }}>Flux Dev</option>
                            <option value="flux-schnell" {{ $configs->get('ai_generate_model') == 'flux-schnell' ? 'selected' : '' }}>Flux Schnell</option>
                        </optgroup>
                        <optgroup label="其他">
                            <option value="kandinsky-3" {{ $configs->get('ai_generate_model') == 'kandinsky-3' ? 'selected' : '' }}>Kandinsky 3</option>
                            <option value="minimax" {{ $configs->get('ai_generate_model') == 'minimax' ? 'selected' : '' }}>MiniMax</option>
                            <option value="cogview-4" {{ $configs->get('ai_generate_model') == 'cogview-4' ? 'selected' : '' }}>CogView 4</option>
                        </optgroup>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">如果上面列表没有，可以手动输入</p>
                    <input type="text" name="ai_generate_model_custom" value="{{ $configs->get('ai_generate_model') ?? '' }}" placeholder="或手动输入模型名称" class="mt-2 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0 px-3 py-2 text-sm"/>
                </div>
            </div>

            <div class="text-center">
                <x-button type="submit"><i class="fas fa-save mr-1"></i>保存配置</x-button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
            (function() {
                var badge = document.getElementById('ai-status-badge');
                fetch('/ai/health', { method: 'GET' })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.status === 'online') {
                            badge.textContent = '\u25cf \u5728\u7ebf';
                            badge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700';
                        } else {
                            badge.textContent = '\u25cf \u79bb\u7ebf';
                            badge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700';
                        }
                    })
                    .catch(function() {
                        badge.textContent = '\u25cf \u79bb\u7ebf';
                        badge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700';
                    });
            })();

            document.getElementById('ai_generate_provider').addEventListener('change', function() {
                var urlInput = document.getElementById('ai_generate_api_url');
                var urls = {
                    'newapi': 'http://192.168.68.19:3099',
                    'openai': 'https://api.openai.com',
                    'siliconflow': 'https://api.siliconflow.cn',
                    'aliyun': 'https://dashscope.aliyuncs.com',
                    'tencent': 'https://api.hunyuan.cloud.tencent.com',
                    'baidu': 'https://aip.baidubce.com',
                    'custom': '',
                };
                if (urls[this.value] !== undefined) {
                    urlInput.value = urls[this.value];
                }
            });
        </script>
    @endpush
</x-app-layout>
