@section('title', '我的 Token')

<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">🔑 我的 Token</h2>
                <span class="text-sm text-gray-500">共 {{ $totalTokens }} 个</span>
            </div>

            {{-- 新建 Token 表单 --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm font-medium text-gray-700 mb-3">快速创建 Token</p>
                <form id="create-token-form" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="Token 名称（如 my-app）"
                        class="flex-1 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                        required>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600">
                        <i class="fas fa-plus mr-1"></i>创建
                    </button>
                </form>
            </div>

            {{-- 新创建 Token 显示 --}}
            <div id="new-token-box" class="hidden my-4 p-4 bg-green-50 border border-green-300 rounded-lg">
                <p class="text-sm font-semibold text-green-700 mb-2">
                    <i class="fas fa-check-circle mr-1"></i>Token 创建成功！请立即复制保存，刷新页面后将无法再次查看：
                </p>
                <div class="flex items-center gap-2">
                    <code id="new-token-value" class="flex-1 p-3 bg-white border rounded text-sm text-gray-800 break-all font-mono"></code>
                    <button onclick="copyToken()" class="px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 whitespace-nowrap">
                        <i class="fas fa-copy mr-1"></i>复制
                    </button>
                </div>
            </div>

            {{-- Token 列表 --}}
            @if($tokens->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">名称</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">来源 IP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Token</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">创建时间</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">最后使用</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">操作</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tokens as $token)
                    @php
                        $name = $token->name;
                        if (str_starts_with($name, 'hermes')) $source = 'Hermes Agent';
                        elseif (str_starts_with($name, 'py-') || str_starts_with($name, 'script')) $source = 'Python/脚本';
                        elseif (str_contains($name, 'smzdm')) $source = '值得买发布';
                        elseif (str_contains($name, 'pub-')) $source = '自动发布脚本';
                        elseif (filter_var($name, FILTER_VALIDATE_EMAIL)) $source = 'API接口(邮箱登录)';
                        else $source = $name;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ str_starts_with($name, 'hermes') ? 'bg-purple-100 text-purple-700' :
                                   (filter_var($name, FILTER_VALIDATE_EMAIL) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $name }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $token->ip ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 font-mono max-w-[200px]" title="{{ $token->plain_token ?: '旧Token无明文' }}">
                            @if($token->plain_token)
                                <span class="cursor-pointer hover:text-blue-500" onclick="copyPlainText('{{ $token->plain_token }}', this)">
                                    {{ substr($token->plain_token, 0, 16) }}...
                                </span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $token->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $token->last_used_at ? $token->last_used_at->diffForHumans() : '从未使用' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button onclick="deleteToken({{ $token->id }}, '{{ $name }}')" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 分页 --}}
            @if($tokens->hasPages())
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    显示 {{ $tokens->firstItem() }}-{{ $tokens->lastItem() }}，共 {{ $totalTokens }} 个
                </div>
                <div class="flex items-center gap-1">
                    @if($tokens->onFirstPage())
                    <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">上一页</span>
                    @else
                    <a href="{{ $tokens->previousPageUrl() }}" class="px-3 py-1 text-sm text-gray-600 bg-white border rounded hover:bg-gray-50">上一页</a>
                    @endif

                    @foreach($tokens->getUrlRange(max(1, $tokens->currentPage() - 2), min($tokens->lastPage(), $tokens->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" class="px-3 py-1 text-sm {{ $page == $tokens->currentPage() ? 'bg-blue-500 text-white' : 'text-gray-600 bg-white border hover:bg-gray-50' }} rounded">{{ $page }}</a>
                    @endforeach

                    @if($tokens->hasMorePages())
                    <a href="{{ $tokens->nextPageUrl() }}" class="px-3 py-1 text-sm text-gray-600 bg-white border rounded hover:bg-gray-50">下一页</a>
                    @else
                    <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">下一页</span>
                    @endif
                </div>
            </div>
            @endif

            @else
            <p class="text-center text-gray-400 py-8">暂无 Token，请通过上方表单创建</p>
            @endif

            {{-- 提示 --}}
            <div class="mt-6 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded text-sm text-gray-600">
                <p><i class="fas fa-info-circle mr-1"></i><b>注意</b>：Token 创建后仅在创建时显示明文，之后数据库中只保留哈希值。如需新 Token，请通过上方表单创建。</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // 创建 Token
        document.getElementById('create-token-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const name = form.querySelector('[name="name"]').value;
            const csrf = form.querySelector('[name="_token"]').value;

            fetch('{{ route("user.tokens.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name: name })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    document.getElementById('new-token-box').classList.remove('hidden');
                    document.getElementById('new-token-value').textContent = data.data.token;
                    form.querySelector('[name="name"]').value = '';
                    // 刷新页面以更新列表
                    setTimeout(() => location.reload(), 3000);
                } else {
                    alert('创建失败: ' + (data.message || '未知错误'));
                }
            })
            .catch(err => alert('请求失败: ' + err));
        });

        // 复制新 Token
        function copyToken() {
            const token = document.getElementById('new-token-value').textContent;
            navigator.clipboard.writeText(token).then(() => {
                const btn = event.target.closest('button');
                btn.innerHTML = '<i class="fas fa-check mr-1"></i>已复制';
                setTimeout(() => btn.innerHTML = '<i class="fas fa-copy mr-1"></i>复制', 1500);
            });
        }

        // 复制明文 Token
        function copyPlainText(token, el) {
            navigator.clipboard.writeText(token).then(() => {
                const orig = el.textContent;
                el.textContent = '已复制!';
                el.classList.add('text-green-500');
                setTimeout(() => {
                    el.textContent = orig;
                    el.classList.remove('text-green-500');
                }, 1500);
            });
        }

        // 删除 Token
        function deleteToken(id, name) {
            if (!confirm('确定要删除 Token "' + name + '" 吗？')) return;
            const csrf = '{{ csrf_token() }}';
            fetch('{{ route("user.tokens.destroy", "TOKEN_ID") }}'.replace('TOKEN_ID', id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    location.reload();
                } else {
                    alert('删除失败: ' + (data.message || '未知错误'));
                }
            })
            .catch(err => alert('请求失败: ' + err));
        }
    </script>
    @endpush
</x-app-layout>
