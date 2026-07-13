@section('title', 'AI 图片处理')

<x-app-layout>
    <div class="max-w-5xl mx-auto p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                <i class="fas fa-magic text-indigo-500 mr-2"></i>AI 图片处理
            </h1>
            <p class="text-gray-500 text-sm">上传图片或输入文字，使用 AI 进行智能处理</p>
            <div id="ai-status" class="mt-2 text-sm"></div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex border-b border-gray-200 mb-6">
            <button onclick="switchTab('enhance')" id="tab-enhance"
                class="tab-btn px-4 py-2 text-sm font-medium text-indigo-600 hover:text-gray-700 border-b-2 border-indigo-500 active">
                <i class="fas fa-expand-arrows-alt mr-1"></i>超分辨率
            </button>
            <button onclick="switchTab('remove_bg')" id="tab-remove_bg"
                class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-cut mr-1"></i>抠图去背景
            </button>
            <button onclick="switchTab('style')" id="tab-style"
                class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-palette mr-1"></i>风格化
            </button>
            <button onclick="switchTab('generate')" id="tab-generate"
                class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-wand-magic-sparkles mr-1"></i>文生图
            </button>
            <button onclick="switchTab('watermark')" id="tab-watermark"
                class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-copyright mr-1"></i>添加水印
            </button>
            <button onclick="switchTab('remove_watermark')" id="tab-remove_watermark"
                class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-eraser mr-1"></i>去水印
            </button>
            <button onclick="switchTab('text_overlay')" id="tab-text_overlay"
                class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-font mr-1"></i>文字美化
            </button>
            <button onclick="switchTab('merge')" id="tab-merge"
                class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-layer-group mr-1"></i>多图合并
            </button>
            <button onclick="switchTab('poster')" id="tab-poster"
                class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-pen-fancy mr-1"></i>海报设计
            </button>
            <button onclick="switchTab('product')" id="tab-product"
                class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-box-open mr-1"></i>产品修改
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Left: Input --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">
                    <i class="fas fa-upload mr-1"></i>输入
                </h2>

                {{-- File Upload (for enhance/remove_bg/style) --}}
                <div id="upload-section" class="mb-4">
                    <div id="drop-zone"
                        class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">拖拽图片到这里，或 <span class="text-blue-500">点击选择</span></p>
                        <p class="text-gray-400 text-xs mt-2">支持 JPG, PNG, WebP (最大 10MB)</p>
                        <input type="file" id="file-input" class="hidden" accept="image/*">
                    </div>
                    <div id="preview-container" class="mt-4 hidden">
                        <img id="preview-img" class="max-h-64 mx-auto rounded-lg shadow">
                        <p id="file-name" class="text-sm text-gray-500 mt-2 text-center"></p>
                    </div>
                </div>

                {{-- Options per tab --}}
                {{-- Enhance Options --}}
                <div id="options-enhance" class="tab-options">
                    <label class="block text-sm font-medium text-gray-700 mb-1">放大倍数</label>
                    <select id="scale-select" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="2">2x（推荐）</option>
                        <option value="3">3x</option>
                        <option value="4">4x</option>
                    </select>
                </div>

                {{-- Remove BG Options --}}
                <div id="options-remove_bg" class="tab-options hidden">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded text-sm text-gray-700">
                        <p class="font-semibold text-blue-700">💡 抠图说明</p>
                        <p>使用 U2-Net 模型自动识别前景，生成透明背景 PNG。首次运行需下载模型。</p>
                    </div>
                </div>

                {{-- Style Options --}}
                <div id="options-style" class="tab-options hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">选择风格</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="style-option flex items-center p-2 border rounded-lg cursor-pointer hover:bg-indigo-50 ring-2 ring-indigo-500"
                            data-style="cartoon">
                            <input type="radio" name="style" value="cartoon" checked class="mr-2">
                            <span>🎨 卡通</span>
                        </label>
                        <label class="style-option flex items-center p-2 border rounded-lg cursor-pointer hover:bg-indigo-50"
                            data-style="oil_painting">
                            <input type="radio" name="style" value="oil_painting" class="mr-2">
                            <span>🖌️ 油画</span>
                        </label>
                        <label class="style-option flex items-center p-2 border rounded-lg cursor-pointer hover:bg-indigo-50"
                            data-style="pencil_sketch">
                            <input type="radio" name="style" value="pencil_sketch" class="mr-2">
                            <span>✏️ 素描</span>
                        </label>
                        <label class="style-option flex items-center p-2 border rounded-lg cursor-pointer hover:bg-indigo-50"
                            data-style="emboss">
                            <input type="radio" name="style" value="emboss" class="mr-2">
                            <span>🗿 浮雕</span>
                        </label>
                        <label class="style-option flex items-center p-2 border rounded-lg cursor-pointer hover:bg-indigo-50"
                            data-style="comic">
                            <input type="radio" name="style" value="comic" class="mr-2">
                            <span>📖 漫画</span>
                        </label>
                    </div>
                </div>

                {{-- Generate Options --}}
                <div id="options-generate" class="tab-options hidden">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">提示词 (Prompt)</label>
                        <textarea id="prompt-input" rows="3" placeholder="描述你想要生成的图片..."
                            class="w-full border rounded-lg px-3 py-2 text-sm resize-none"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">反向提示词 (可选)</label>
                        <textarea id="negative-prompt-input" rows="2" placeholder="不希望出现的内容..."
                            class="w-full border rounded-lg px-3 py-2 text-sm resize-none"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">宽度</label>
                            <select id="gen-width" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="512">512</option>
                                <option value="768">768</option>
                                <option value="1024" selected>1024</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">高度</label>
                            <select id="gen-height" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="512">512</option>
                                <option value="768" selected>768</option>
                                <option value="1024">1024</option>
                            </select>
                        </div>
                    </div>
                </div>
                {{-- Watermark --}}
                <div id="options-watermark" class="tab-options hidden">
                    <div class="mb-3 p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">输入水印文字</label>
                        <div class="flex gap-2">
                            <input type="text" id="wm-text" value="© 资享网" class="flex-1 border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                            <button type="button" id="wm-ai-btn" onclick="beautifyWatermark()" class="px-3 py-2.5 bg-purple-500 text-white text-sm rounded-lg hover:bg-purple-600 whitespace-nowrap"><i class="fas fa-wand-magic-sparkles mr-1"></i>AI美化</button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">输入文字 → 点击AI美化自动生成精美水印</p>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div><label class="text-xs text-gray-500">位置</label><select id="wm-position" class="w-full border rounded-lg px-2 py-1.5 text-xs"><option value="bottom-right">右下</option><option value="center">居中</option><option value="top-left">左上</option></select></div>
                        <div><label class="text-xs text-gray-500">颜色</label><input type="color" id="wm-color" value="#FFFFFF" class="w-full h-8 border rounded-lg cursor-pointer"></div>
                        <div><label class="text-xs text-gray-500">大小</label><select id="wm-size" class="w-full border rounded-lg px-2 py-1.5 text-xs"><option value="24">小</option><option value="36" selected>中</option><option value="56">大</option><option value="72">特大</option></select></div>
                    </div>
                    <details class="mb-3"><summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600 select-none"><i class="fas fa-cog mr-1"></i>高级设置</summary>
                        <div class="mt-2 p-3 bg-gray-50 rounded-lg space-y-2">
                            <div class="flex items-center justify-between"><span class="text-xs text-gray-500">透明度</span><input type="range" id="wm-opacity" min="0.1" max="1.0" step="0.1" value="0.5" class="flex-1 mx-2"><span id="wm-opacity-val" class="text-xs text-gray-500 w-8 text-right">0.5</span></div>
                            <div><label class="text-xs text-gray-500">上传水印图片</label><input type="file" id="wm-image-input" accept="image/*" class="w-full text-xs mt-1"></div>
                            <div><label class="text-xs text-gray-500">字号</label><input type="number" id="wm-font-size" value="36" class="w-full border rounded-lg px-2 py-1 text-xs"></div>
                        </div>
                    </details>
                </div>
                {{-- Remove Watermark --}}
                <div id="options-remove_watermark" class="tab-options hidden">
                    <div class="mb-3"><label class="block text-sm font-medium text-gray-700 mb-1">去除方式</label><select id="rw-method" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="auto">自动检测</option></select></div>
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded text-sm"><p class="font-semibold text-blue-700">自动检测半透明水印</p></div>
                </div>
                {{-- Text Overlay --}}
                <div id="options-text_overlay" class="tab-options hidden">
                    <div class="mb-3"><label class="block text-sm font-medium text-gray-700 mb-1">文字内容</label><textarea id="to-text" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">Hello</textarea></div>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div><label class="text-xs text-gray-500">字号</label><input type="number" id="to-size" value="48" class="w-full border rounded-lg px-2 py-1 text-sm"></div>
                        <div><label class="text-xs text-gray-500">颜色</label><input type="color" id="to-color" value="#FFFFFF" class="w-full h-9 border rounded-lg"></div>
                        <div><label class="text-xs text-gray-500">位置</label><select id="to-position" class="w-full border rounded-lg px-2 py-1 text-sm"><option value="center">居中</option><option value="top">顶部</option><option value="bottom">底部</option></select></div>
                        <div><label class="text-xs text-gray-500">背景</label><input type="range" id="to-bg" min="0" max="0.8" step="0.1" value="0" class="w-full"></div>
                    </div>
                    <label class="flex items-center text-sm"><input type="checkbox" id="to-outline" checked class="mr-2">描边</label>
                </div>
                {{-- Merge --}}
                <div id="options-merge" class="tab-options hidden">
                    <div class="mb-3"><div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-400 transition" onclick="document.getElementById('merge-input').click()"><i class="fas fa-images text-3xl text-gray-300 mb-2"></i><p class="text-gray-500 text-sm">选择多张图片</p><input type="file" id="merge-input" multiple accept="image/*" class="hidden"></div></div>
                    <div id="merge-preview" class="mb-3 hidden"><div id="merge-preview-list" class="flex gap-2 overflow-x-auto pb-2"></div><p id="merge-count" class="text-xs text-gray-400 mt-1"></p></div>
                    <div class="mb-3"><label class="block text-sm font-medium text-gray-700 mb-1">排列</label><select id="merge-layout" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="horizontal">横向</option><option value="vertical">纵向</option><option value="grid">网格</option></select></div>
                </div>
                {{-- Poster --}}
                <div id="options-poster" class="tab-options hidden">
                    <div class="mb-3"><label class="block text-sm font-medium text-gray-700 mb-2">选择模板</label>
                        <div id="poster-templates" class="grid grid-cols-2 gap-2">
                            <div class="template-opt border-2 border-indigo-500 rounded-lg p-2 text-center cursor-pointer bg-indigo-50" data-template="promo"><div class="text-2xl mb-1">🏷️</div><div class="text-xs font-medium">促销海报</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="social"><div class="text-2xl mb-1">📱</div><div class="text-xs font-medium">社交媒体</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="event"><div class="text-2xl mb-1">📅</div><div class="text-xs font-medium">活动通知</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="business"><div class="text-2xl mb-1">💼</div><div class="text-xs font-medium">简约商务</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="holiday"><div class="text-2xl mb-1">🎉</div><div class="text-xs font-medium">节日祝福</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="recruit"><div class="text-2xl mb-1">👥</div><div class="text-xs font-medium">招聘海报</div></div>
                        </div>
                    </div>
                    <div class="mb-3"><label class="block text-sm font-medium text-gray-700 mb-1">文字</label><textarea id="poster-text" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">限时特惠
全场5折起</textarea></div>
                    <div class="mb-3"><label class="block text-sm font-medium text-gray-700 mb-1">尺寸</label><select id="poster-size" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="1080x1080">1080×1080</option><option value="1080x1920">1080×1920</option><option value="1920x1080">1920×1080</option></select></div>
                </div>
                {{-- Product --}}
                <div id="options-product" class="tab-options hidden">
                    <div class="mb-3"><label class="block text-sm font-medium text-gray-700 mb-2">选择模板</label>
                        <div id="product-templates" class="grid grid-cols-2 gap-2">
                            <div class="template-opt border-2 border-indigo-500 rounded-lg p-2 text-center cursor-pointer bg-indigo-50" data-template="main"><div class="text-2xl mb-1">🛒</div><div class="text-xs font-medium">电商主图</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="white"><div class="text-2xl mb-1">⬜</div><div class="text-xs font-medium">产品白底</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="label"><div class="text-2xl mb-1">🏷️</div><div class="text-xs font-medium">信息标签</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="collage"><div class="text-2xl mb-1">🧩</div><div class="text-xs font-medium">多角度</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="scene"><div class="text-2xl mb-1">🌅</div><div class="text-xs font-medium">场景展示</div></div>
                            <div class="template-opt border-2 border-gray-200 rounded-lg p-2 text-center cursor-pointer hover:border-indigo-300" data-template="compare"><div class="text-2xl mb-1">📊</div><div class="text-xs font-medium">对比效果</div></div>
                        </div>
                    </div>
                    <div class="mb-3"><label class="block text-sm font-medium text-gray-700 mb-1">文字/标签</label><textarea id="product-text" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm">热销爆款</textarea></div>
                    <div class="mb-3"><label class="block text-sm font-medium text-gray-700 mb-1">尺寸</label><select id="product-size" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="800x800">800×800</option><option value="800x1200">800×1200</option><option value="1200x800">1200×800</option></select></div>
                </div>

                {{-- Action Button --}}
                <div class="mt-6 flex gap-3">
                    <button onclick="processImage()" id="process-btn"
                        class="flex-1 bg-indigo-500 hover:bg-indigo-600 text-white font-medium py-2.5 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-wand-magic-sparkles mr-1"></i>开始处理
                    </button>
                    <button onclick="downloadResult()" id="download-btn"
                        class="bg-green-500 hover:bg-green-600 text-white font-medium py-2.5 px-4 rounded-lg transition hidden">
                        <i class="fas fa-download mr-1"></i>下载
                    </button>
                </div>
            </div>

            {{-- Right: Result --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">
                    <i class="fas fa-eye mr-1"></i>处理结果
                </h2>

                <div id="result-placeholder" class="border-2 border-dashed border-gray-200 rounded-lg p-12 text-center">
                    <i class="fas fa-image text-4xl text-gray-200 mb-3"></i>
                    <p class="text-gray-400">处理后的图片将显示在这里</p>
                </div>

                <div id="result-container" class="hidden">
                    <img id="result-img" class="max-h-96 mx-auto rounded-lg shadow">
                    <div class="mt-4 flex gap-2">
                        <button onclick="copyUrl()" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-sm py-2 px-3 rounded-lg transition">
                            <i class="fas fa-link mr-1"></i>复制链接
                        </button>
                        <button onclick="downloadResult()" class="flex-1 bg-green-500 hover:bg-green-600 text-white text-sm py-2 px-3 rounded-lg transition">
                            <i class="fas fa-download mr-1"></i>下载
                        </button>
                        <button onclick="useAsOriginal()" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white text-sm py-2 px-3 rounded-lg transition">
                            <i class="fas fa-redo mr-1"></i>再次处理
                        </button>
                    </div>
                </div>

                <div id="loading-overlay" class="hidden mt-4 text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-indigo-500 border-r-transparent mb-3"></div>
                    <p class="text-gray-500">AI 处理中，请稍候...</p>
                    <p class="text-gray-400 text-xs mt-1" id="loading-tip">首次运行可能需要下载模型</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const AI_BASE = '{{ config("services.lsky_ai.url", "http://lsky-ai:8077") }}';
        let currentTab = 'enhance';
        let currentFile = null;
        let mergeFiles = [];
        let lastResultBlob = null;
        let lastResultUrl = null;

        // Check AI service health on load
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const resp = await fetch('/ai/health');
                const data = await resp.json();
                const el = document.getElementById('ai-status');
                if (data.status === 'online') {
                    el.innerHTML = '<span class="text-green-500">● AI 服务在线</span>';
                } else {
                    el.innerHTML = '<span class="text-red-500">● AI 服务离线</span>';
                }
            } catch (e) {
                document.getElementById('ai-status').innerHTML =
                    '<span class="text-red-500">● 无法连接 AI 服务</span>';
            }
        });

        function switchTab(tab) {
            currentTab = tab;
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active', 'text-indigo-600', 'border-indigo-500');
                btn.classList.add('text-gray-500', 'border-transparent');
            });
            const activeBtn = document.getElementById('tab-' + tab);
            activeBtn.classList.add('active', 'text-indigo-600', 'border-indigo-500');
            activeBtn.classList.remove('text-gray-500', 'border-transparent');

            document.querySelectorAll('.tab-options').forEach(el => el.classList.add('hidden'));
            document.getElementById('options-' + tab).classList.remove('hidden');

            const uploadSection = document.getElementById('upload-section');
            uploadSection.style.display = (tab === 'generate' || tab === 'merge') ? 'none' : 'block';
        }

        // File upload
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');

        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('border-blue-400', 'bg-blue-50'); });
        dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('border-blue-400', 'bg-blue-50'); });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
            handleFile(e.dataTransfer.files[0]);
        });
        fileInput.addEventListener('change', () => { if (fileInput.files[0]) handleFile(fileInput.files[0]); });

        function handleFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            currentFile = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-container').classList.remove('hidden');
                document.getElementById('file-name').textContent = file.name + ' (' + formatSize(file.size) + ')';
            };
            reader.readAsDataURL(file);
        }

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        // Style selection
        document.querySelectorAll('.style-option').forEach(el => {
            el.addEventListener('click', () => {
                document.querySelectorAll('.style-option').forEach(e => e.classList.remove('ring-2 ring-indigo-500', 'bg-indigo-50', 'border-indigo-400'));
                el.classList.add('ring-2 ring-indigo-500', 'bg-indigo-50', 'border-indigo-400');
            });
        });

        // Template selection (poster & product)
        document.querySelectorAll('.template-opt').forEach(el => {
            el.addEventListener('click', () => {
                const container = el.parentElement;
                container.querySelectorAll('.template-opt').forEach(e => {
                    e.classList.remove('border-indigo-500', 'bg-indigo-50', 'selected');
                    e.classList.add('border-gray-200');
                });
                el.classList.remove('border-gray-200');
                el.classList.add('border-indigo-500', 'bg-indigo-50', 'selected');
            });
        });

        // Merge file selection
        const mergeInput = document.getElementById('merge-input');
        if (mergeInput) {
            mergeInput.addEventListener('change', () => {
                const files = mergeInput.files;
                const preview = document.getElementById('merge-preview');
                const list = document.getElementById('merge-preview-list');
                const count = document.getElementById('merge-count');
                if (!files || files.length === 0) {
                    mergeFiles = [];
                    preview.classList.add('hidden');
                    return;
                }
                mergeFiles = Array.from(files);
                list.innerHTML = '';
                for (let i = 0; i < files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'h-16 w-16 object-cover rounded border flex-shrink-0';
                        list.appendChild(img);
                    };
                    reader.readAsDataURL(files[i]);
                }
                preview.classList.remove('hidden');
                count.textContent = '已选择 ' + files.length + ' 张图片';
            });
        }

        // Watermark opacity slider
        const wmOpacity = document.getElementById('wm-opacity');
        const wmOpacityVal = document.getElementById('wm-opacity-val');
        if (wmOpacity && wmOpacityVal) {
            wmOpacity.addEventListener('input', () => {
                wmOpacityVal.textContent = wmOpacity.value;
            });
        }

        // Watermark AI beautify
        function beautifyWatermark() {
            const text = document.getElementById('wm-text').value.trim();
            if (!text) { alert('请输入水印文字'); return; }
            const btn = document.getElementById('wm-ai-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>美化中...';
            fetch('/ai/process', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'operation=watermark_beautify&text=' + encodeURIComponent(text),
            }).then(r => r.json()).then(data => {
                if (data.success && data.text) {
                    document.getElementById('wm-text').value = data.text;
                } else {
                    alert('美化失败: ' + (data.error || '请稍后重试'));
                }
            }).catch(e => {
                alert('请求失败: ' + e.message);
            }).finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-wand-magic-sparkles mr-1"></i>AI美化';
            });
        }

        async function processImage() {
            const btn = document.getElementById('process-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>处理中...';

            document.getElementById('result-placeholder').classList.add('hidden');
            document.getElementById('result-container').classList.add('hidden');
            document.getElementById('loading-overlay').classList.remove('hidden');

            try {
                const formData = new FormData();
                formData.append('operation', currentTab);

                if (currentTab === 'generate') {
                    formData.append('prompt', document.getElementById('prompt-input').value);
                    formData.append('negative_prompt', document.getElementById('negative-prompt-input').value);
                    formData.append('width', document.getElementById('gen-width').value);
                    formData.append('height', document.getElementById('gen-height').value);
                } else if (currentTab === 'merge') {
                    if (mergeFiles.length < 2) { alert('请选择至少2张图片'); return; }
                    for (var i = 0; i < mergeFiles.length; i++) formData.append('files[]', mergeFiles[i]);
                    formData.append('layout', document.getElementById('merge-layout').value);
                } else {
                    if (!currentFile) {
                        alert('请先上传图片');
                        return;
                    }
                    formData.append('file', currentFile);

                    if (currentTab === 'enhance') {
                        formData.append('scale', document.getElementById('scale-select').value);
                    } else if (currentTab === 'style') {
                        formData.append('style', document.querySelector('input[name="style"]:checked').value);
                    } else if (currentTab === 'watermark') {
                        formData.append('text', document.getElementById('wm-text').value || 'Watermark');
                        formData.append('position', document.getElementById('wm-position').value);
                        formData.append('opacity', document.getElementById('wm-opacity') ? document.getElementById('wm-opacity').value : '0.5');
                        formData.append('color', document.getElementById('wm-color') ? document.getElementById('wm-color').value : '#FFFFFF');
                        formData.append('text_size', document.getElementById('wm-font-size') ? document.getElementById('wm-font-size').value : '36');
                        var wf = document.getElementById('wm-image-input');
                        if (wf && wf.files && wf.files[0]) formData.append('watermark_file', wf.files[0]);
                    } else if (currentTab === 'remove_bg') {
                        formData.append('method', 'u2net');
                    } else if (currentTab === 'remove_watermark') {
                        formData.append('method', document.getElementById('rw-method').value);
                    } else if (currentTab === 'text_overlay') {
                        formData.append('text', document.getElementById('to-text').value || 'Hello');
                        formData.append('font_size', document.getElementById('to-size').value);
                        formData.append('color', document.getElementById('to-color').value);
                        formData.append('position', document.getElementById('to-position').value);
                        formData.append('outline', document.getElementById('to-outline').checked ? 'true' : 'false');
                        formData.append('bg_opacity', document.getElementById('to-bg').value);
                    } else if (currentTab === 'poster') {
                        var pt = document.querySelector('#poster-templates .template-opt.selected') || document.querySelector('#poster-templates .template-opt');
                        formData.append('template_id', pt ? pt.dataset.template : 'promo');
                        formData.append('text', document.getElementById('poster-text').value);
                        formData.append('size', document.getElementById('poster-size').value);
                    } else if (currentTab === 'product') {
                        var pt = document.querySelector('#product-templates .template-opt.selected') || document.querySelector('#product-templates .template-opt');
                        formData.append('template_id', pt ? pt.dataset.template : 'main');
                        formData.append('text', document.getElementById('product-text').value);
                        formData.append('size', document.getElementById('product-size').value);
                    }
                }

                const resp = await fetch('/ai/process', {
                    method: 'POST',
                    body: formData,
                });

                const data = await resp.json();

                if (data.success) {
                    // Show result
                    lastResultUrl = data.url;
                    document.getElementById('result-img').src = data.url;
                    document.getElementById('result-container').classList.remove('hidden');
                    document.getElementById('download-btn').classList.remove('hidden');
                } else {
                    alert('处理失败: ' + (data.error || '未知错误'));
                    document.getElementById('result-placeholder').classList.remove('hidden');
                }
            } catch (e) {
                alert('请求失败: ' + e.message);
                document.getElementById('result-placeholder').classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-wand-magic-sparkles mr-1"></i>开始处理';
                document.getElementById('loading-overlay').classList.add('hidden');
            }
        }

        function copyUrl() {
            if (lastResultUrl) {
                // Build full URL if it's a relative path
                var fullUrl = lastResultUrl;
                if (fullUrl.startsWith('/')) {
                    fullUrl = window.location.protocol + '//' + window.location.host + fullUrl;
                }
                // Try modern clipboard API first
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(fullUrl).then(function() {
                        alert('链接已复制');
                    }).catch(function() {
                        fallbackCopy(fullUrl);
                    });
                } else {
                    fallbackCopy(fullUrl);
                }
            }
        }

        function fallbackCopy(text) {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
                alert('链接已复制');
            } catch (e) {
                prompt('复制失败，请手动复制：', text);
            }
            document.body.removeChild(ta);
        }

        function downloadResult() {
            if (lastResultUrl) {
                const a = document.createElement('a');
                a.href = lastResultUrl;
                a.download = 'ai_result_' + currentTab + '.png';
                a.click();
            }
        }

        function useAsOriginal() {
            if (lastResultUrl) {
                // Fetch the result image and set as input
                fetch(lastResultUrl)
                    .then(r => r.blob())
                    .then(blob => {
                        const file = new File([blob], 'ai_result.png', { type: 'image/png' });
                        handleFile(file);
                        switchTab(currentTab === 'generate' ? 'enhance' : currentTab);
                    });
            }
        }
    </script>
    @endpush
</x-app-layout>