@section('title', '目录导入')

<x-app-layout>
    <div class="my-6 md:my-9">
        <p class="mb-3 font-semibold text-lg text-gray-700">从目录导入图片</p>

        <div class="relative p-4 rounded-md bg-white mb-8 space-y-4 shadow-custom">
            <div>
                <label for="directory" class="block text-sm font-medium text-gray-700">
                    <span class="text-red-600">*</span>服务器目录路径
                </label>
                <x-input type="text" name="directory" id="directory" value="" placeholder="请输入服务器上的绝对路径，如 /home/images"/>
                <p class="mt-1 text-sm text-gray-500">请输入服务器上的绝对路径，系统将扫描该目录下的所有图片文件。</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="strategy_id" class="block text-sm font-medium text-gray-700">
                        <span class="text-red-600">*</span>储存策略
                    </label>
                    <select id="strategy_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        @foreach($strategies as $strategy)
                            <option value="{{ $strategy->id }}">{{ $strategy->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="album_id" class="block text-sm font-medium text-gray-700">导入相册</label>
                    <select id="album_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">不导入相册</option>
                        @foreach($albums as $album)
                            <option value="{{ $album->id }}">{{ $album->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="permission" class="block text-sm font-medium text-gray-700">权限</label>
                    <select id="permission" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="private">私有</option>
                        <option value="public">公开</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">压缩质量</label>
                <div class="flex items-center gap-3">
                    <input type="range" id="import-quality" min="10" max="100" step="5" value="100" class="flex-1">
                    <span id="import-quality-val" class="text-sm text-gray-600 w-10 text-right">100</span>
                    <span class="text-xs text-gray-400">%</span>
                </div>
                <p class="mt-1 text-xs text-gray-400">100% 为不压缩，数值越小压缩越狠、文件越小</p>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="recursive" name="recursive" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked/>
                    <label for="recursive" class="ml-2 block text-sm text-gray-700">递归扫描子目录</label>
                </div>
                <x-button type="button" id="btn-scan" onclick="scanDirectory()">
                    <i class="fas fa-search mr-1"></i> 扫描目录
                </x-button>
            </div>
        </div>

        <div id="scan-result" class="hidden">
            <p class="mb-3 font-semibold text-lg text-gray-700">扫描结果</p>
            <div class="relative p-4 rounded-md bg-white mb-8 shadow-custom">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-600">
                        共找到 <span id="total-count" class="font-bold text-blue-600">0</span> 个图片文件
                    </p>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="select-all" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked/>
                        <label for="select-all" class="text-sm text-gray-700">全选</label>
                    </div>
                </div>

                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">文件名</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">路径</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">大小</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">类型</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">修改时间</th>
                            </tr>
                        </thead>
                        <tbody id="file-list" class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>

                <div class="text-right">
                    <x-button type="button" id="btn-import" onclick="importImages()">
                        <i class="fas fa-upload mr-1"></i> 导入选中图片
                    </x-button>
                </div>
            </div>
        </div>

        <div id="import-result" class="hidden">
            <p class="mb-3 font-semibold text-lg text-gray-700">导入结果</p>
            <div class="relative p-4 rounded-md bg-white mb-8 shadow-custom">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="flex justify-between rounded-md bg-green-50 p-3 overflow-hidden">
                        <div class="flex flex-col justify-between space-y-2">
                            <p class="font-bold text-2xl text-green-700" id="result-success">0</p>
                            <p class="text-md text-green-600">成功导入</p>
                        </div>
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="flex justify-between rounded-md bg-yellow-50 p-3 overflow-hidden">
                        <div class="flex flex-col justify-between space-y-2">
                            <p class="font-bold text-2xl text-yellow-700" id="result-skipped">0</p>
                            <p class="text-md text-yellow-600">已跳过</p>
                        </div>
                        <i class="fas fa-minus-circle text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="flex justify-between rounded-md bg-red-50 p-3 overflow-hidden">
                        <div class="flex flex-col justify-between space-y-2">
                            <p class="font-bold text-2xl text-red-700" id="result-failed">0</p>
                            <p class="text-md text-red-600">导入失败</p>
                        </div>
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                </div>

                <div id="error-details" class="hidden">
                    <p class="text-sm font-medium text-gray-700 mb-2">错误详情：</p>
                    <div class="max-h-60 overflow-y-auto bg-gray-50 rounded-md p-3">
                        <ul id="error-list" class="list-disc list-inside text-sm text-red-600 space-y-1">
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="loading-overlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 shadow-xl text-center">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
                <p id="loading-text" class="text-gray-700 font-medium">正在处理...</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let allFiles = [];

        const importQuality = document.getElementById('import-quality');
        const importQualityVal = document.getElementById('import-quality-val');
        if (importQuality && importQualityVal) {
            importQuality.addEventListener('input', () => { importQualityVal.textContent = importQuality.value; });
        }

        function showLoading(text) {
            document.getElementById('loading-text').textContent = text || '正在处理...';
            document.getElementById('loading-overlay').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
        }

        function scanDirectory() {
            const directory = document.getElementById('directory').value.trim();
            if (!directory) {
                alert('请输入目录路径');
                return;
            }

            const recursive = document.getElementById('recursive').checked;
            showLoading('正在扫描目录...');

            fetch('{{ route("admin.directory-import.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    directory: directory,
                    recursive: recursive ? 1 : 0,
                }),
            })
            .then(response => response.json())
            .then(result => {
                hideLoading();
                if (result.status) {
                    allFiles = result.data.files;
                    renderFileList(result.data.files);
                    document.getElementById('total-count').textContent = result.data.total;
                    document.getElementById('scan-result').classList.remove('hidden');
                    document.getElementById('import-result').classList.add('hidden');
                } else {
                    alert(result.message);
                }
            })
            .catch(error => {
                hideLoading();
                alert('扫描失败: ' + error.message);
            });
        }

        function renderFileList(files) {
            const tbody = document.getElementById('file-list');
            tbody.innerHTML = '';

            if (files.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">未找到图片文件</td></tr>';
                return;
            }

            files.forEach((file, index) => {
                const row = document.createElement('tr');
                row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="file-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" data-path="${escapeHtml(file.path)}" checked/>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${escapeHtml(file.name)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate" title="${escapeHtml(file.path)}">${escapeHtml(file.path)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${escapeHtml(file.size_human)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${escapeHtml(file.extension.toUpperCase())}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${escapeHtml(file.modified)}</td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('select-all').addEventListener('change', function() {
                document.querySelectorAll('.file-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function importImages() {
            const selectedFiles = [];
            document.querySelectorAll('.file-checkbox:checked').forEach(cb => {
                selectedFiles.push(cb.dataset.path);
            });

            if (selectedFiles.length === 0) {
                alert('请选择要导入的图片');
                return;
            }

            const strategyId = document.getElementById('strategy_id').value;
            const permission = document.getElementById('permission').value;
            const albumId = document.getElementById('album_id').value;

            if (!strategyId) {
                alert('请选择储存策略');
                return;
            }

            if (!confirm(`确定要导入 ${selectedFiles.length} 张图片吗？`)) {
                return;
            }

            showLoading(`正在导入 ${selectedFiles.length} 张图片...`);

            fetch('{{ route("admin.directory-import.import") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    files: selectedFiles,
                    strategy_id: strategyId,
                    permission: permission,
                    album_id: albumId || null,
                    quality: parseInt(document.getElementById('import-quality').value) || 100,
                }),
            })
            .then(response => response.json())
            .then(result => {
                hideLoading();
                if (result.status) {
                    showImportResult(result.data);
                } else {
                    alert(result.message);
                }
            })
            .catch(error => {
                hideLoading();
                alert('导入失败: ' + error.message);
            });
        }

        function showImportResult(data) {
            document.getElementById('result-success').textContent = data.success;
            document.getElementById('result-skipped').textContent = data.skipped;
            document.getElementById('result-failed').textContent = data.failed;

            const errorDetails = document.getElementById('error-details');
            const errorList = document.getElementById('error-list');

            if (data.errors && data.errors.length > 0) {
                errorList.innerHTML = '';
                data.errors.forEach(err => {
                    const li = document.createElement('li');
                    li.textContent = `${err.file}: ${err.reason}`;
                    errorList.appendChild(li);
                });
                errorDetails.classList.remove('hidden');
            } else {
                errorDetails.classList.add('hidden');
            }

            document.getElementById('import-result').classList.remove('hidden');
        }
    </script>
    @endpush
</x-app-layout>
