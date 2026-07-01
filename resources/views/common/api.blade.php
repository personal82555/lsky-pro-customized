@section('title', '接口')

<x-app-layout>
    <div class="my-6 md:my-9">
        <p class="text-xl mb-2 text-gray-800 font-semibold">接口说明</p>
        <div class="space-y-4 bg-white p-3 rounded-md mb-10 shadow-custom">
            <div>
                <p class="text-lg text-gray-700 font-semibold">接口URL</p>
                <x-code>{{ request()->getSchemeAndHttpHost() }}/api/v1</x-code>
            </div>

            <div>
                <p class="text-lg text-gray-700 font-semibold">验证方式</p>
                <div class="my-2 text-sm bg-white rounded-md p-4 overflow-x-auto">
                    当前版本接口采用 「HTTP 基本验证」的方式验证授权，获取到 token 后，通过设置请求 header 标头来验证请求(Bearer Token)，例如：
                    <b class="block my-2 text-gray-600 text-sm">"Authorization": "Bearer 1|1bJbwlqBfnggmOMEZqXT5XusaIwqiZjCDs7r1Ob5"</b>
                    <p class="text-sm">如果未设置 Authorization 的情况下请求上传接口，将会被视为游客上传。</p>
                </div>
            </div>

            <div class="my-4 overflow-x-auto">
                <p class="text-sm mb-2">公共请求 headers 说明</p>
                <table class="min-w-full">
                    <thead class="bg-white border">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            字段
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            类型
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            说明
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white border divide-y text-sm">
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap">Authorization</td>
                        <td class="px-3 py-2 whitespace-nowrap">String</td>
                        <td class="px-3 py-2 whitespace-nowrap">授权 Token，例如：Bearer 1|1bJbwlqBfnggmOMEZqXT5XusaIwqiZjCDs7r1Ob5</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap"><span class="text-red-500">*</span>Accept</td>
                        <td class="px-3 py-2 whitespace-nowrap">String</td>
                        <td class="px-3 py-2 whitespace-nowrap">必须设置为 application/json</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="my-4 overflow-x-auto">
                <p class="text-sm mb-2">公共响应 headers 说明</p>
                <table class="min-w-full">
                    <thead class="bg-white border">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            字段
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            类型
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            说明
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white border divide-y text-sm">
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap">X-RateLimit-Limit</td>
                        <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                        <td class="px-3 py-2 whitespace-nowrap">当前客户端一分钟内请求配额</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap">X-RateLimit-Remaining</td>
                        <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                        <td class="px-3 py-2 whitespace-nowrap">当前客户端剩余请求配额</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="my-4 overflow-x-auto">
                <p class="text-sm mb-2">响应状态码 HTTP Status Code 说明</p>
                <table class="min-w-full">
                    <thead class="bg-white border">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            状态码
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            说明
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white border divide-y text-sm">
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap">401</td>
                        <td class="px-3 py-2 whitespace-nowrap">未登录或授权失败</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap">403</td>
                        <td class="px-3 py-2 whitespace-nowrap">管理员关闭了接口功能</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap">429</td>
                        <td class="px-3 py-2 whitespace-nowrap">超出请求配额，请求受限</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap">500</td>
                        <td class="px-3 py-2 whitespace-nowrap">服务端出现异常</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-sm my-2 text-red-500">文档中接口的请求参数，使用红色「*」符号标注，则表示为必传项。</p>
        </div>

        <p class="text-xl mb-2 text-gray-800 font-semibold">授权相关</p>
        <div class="space-y-4 bg-white p-3 rounded-md mb-10 shadow-custom">
            <div>
                <p class="text-lg text-gray-700 font-semibold">生成 Token</p>
                <x-code><span class="text-green-500 select-none">POST </span>/tokens</x-code>
                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">请求参数(Body)</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap"><span class="text-red-500">*</span>email</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">邮箱</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap"><span class="text-red-500">*</span>password</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">密码</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">返回参数</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">status</td>
                            <td class="px-3 py-2 whitespace-nowrap">Boolean</td>
                            <td class="px-3 py-2 whitespace-nowrap">状态，true 或 false</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">message</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">描述信息</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">数据</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">token</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">Token</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <p class="text-lg text-gray-700 font-semibold">🔑 我的 Token</p>
                <p class="text-sm text-gray-500 mb-3">管理你已生成的 API Token。Token 创建后仅显示一次，请妥善保存。</p>

                @if($newToken)
                    <div class="my-3 p-3 bg-green-50 border border-green-300 rounded">
                        <p class="text-sm font-semibold text-green-700 mb-1">✅ Token 创建成功！请立即复制保存，刷新页面后将无法再次查看：</p>
                        <div class="flex items-center gap-2">
                            <code id="new-token" class="flex-1 p-2 bg-white border rounded text-sm text-gray-800 break-all">{{ $newToken }}</code>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('new-token').textContent);this.textContent='已复制';setTimeout(()=>this.textContent='复制',1500)"
                                class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 whitespace-nowrap">复制</button>
                        </div>
                    </div>
                @endif

                <div class="my-3 p-3 bg-gray-50 border rounded">
                    <p class="text-sm font-medium text-gray-700 mb-2">快速创建 Token</p>
                    <form method="POST" action="{{ route('api') }}" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="token_name" placeholder="Token 名称（如 my-app）"
                            class="flex-1 px-3 py-1.5 border rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                            required>
                        <button type="submit" class="px-4 py-1.5 bg-green-500 text-white text-sm rounded hover:bg-green-600">创建</button>
                    </form>
                </div>

                @if($tokens->count() > 0)
                    <div class="my-4 overflow-x-auto">
                        <p class="text-sm mb-2 text-gray-600">最近创建的 Token（共 {{ $totalTokens }} 个，仅显示最新 10 条）</p>
                        <table class="min-w-full">
                            <thead class="bg-white border">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">名称</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">创建来源</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">来源 IP</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Token</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">创建时间</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">最后使用</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white border divide-y text-sm">
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
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ str_starts_with($name, 'hermes') ? 'bg-purple-100 text-purple-700' :
                                           (filter_var($name, FILTER_VALIDATE_EMAIL) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ $name }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-500 text-xs">{{ $source }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-500 text-xs font-mono">{{ $token->ip ?: '-' }}</td>
                                <td class="px-3 py-2 text-gray-500 text-xs font-mono max-w-[200px] truncate" title="{{ $token->plain_token ?: '旧Token无明文' }}">
                                    @if($token->plain_token)
                                        <span class="cursor-pointer hover:text-blue-500" onclick="navigator.clipboard.writeText('{{ $token->plain_token }}');this.textContent='已复制!';setTimeout(()=>this.textContent='{{ substr($token->plain_token, 0, 16) }}...',1500)">{{ substr($token->plain_token, 0, 16) }}...</span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-500">{{ $token->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-500">{{ $token->last_used_at ? $token->last_used_at->diffForHumans() : '从未使用' }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-400 my-2">暂无已创建的 Token</p>
                @endif

                <div class="my-3 p-2 bg-yellow-50 border-l-4 border-yellow-400 rounded text-sm text-gray-600">
                    <p>💡 <b>注意</b>：Token 创建后仅在创建时显示明文，之后数据库中只保留哈希值。如需新 Token，请通过上方表单创建。</p>
                </div>
            </div>

            <div>
                <p class="text-lg text-gray-700 font-semibold">清空 Token</p>
                <x-code><span class="text-red-300 select-none">DELETE </span>/tokens</x-code>
                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">返回参数</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">status</td>
                            <td class="px-3 py-2 whitespace-nowrap">Boolean</td>
                            <td class="px-3 py-2 whitespace-nowrap">状态，true 或 false</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">message</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">描述信息</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">数据</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <p class="text-lg text-gray-700 font-semibold">用户资料</p>
                <x-code><span class="text-sky-500 select-none">GET </span>/profile</x-code>
                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">返回参数</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">status</td>
                            <td class="px-3 py-2 whitespace-nowrap">Boolean</td>
                            <td class="px-3 py-2 whitespace-nowrap">状态，true 或 false</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">message</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">描述信息</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">数据</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">name</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">用户名</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">avatar</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">头像地址</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">email</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">邮箱地址</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">capacity</td>
                            <td class="px-3 py-2 whitespace-nowrap">Float</td>
                            <td class="px-3 py-2 whitespace-nowrap">总容量</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">used_capacity</td>
                            <td class="px-3 py-2 whitespace-nowrap">Float</td>
                            <td class="px-3 py-2 whitespace-nowrap">已使用容量</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">url</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">个人主页地址</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">image_num</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片数量</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">album_num</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">相册数量</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">registered_ip</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">注册 IP</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <p class="text-xl mb-2 text-gray-800 font-semibold">策略相关</p>
        <div class="space-y-4 bg-white p-3 rounded-md mb-10 shadow-custom">
            <div>
                <p class="text-lg text-gray-700 font-semibold">策略列表</p>
                <x-code><span class="text-sky-500 select-none">GET </span>/strategies</x-code>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">请求参数(Query)</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">keyword</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">筛选关键字</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">返回参数</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">status</td>
                            <td class="px-3 py-2 whitespace-nowrap">Boolean</td>
                            <td class="px-3 py-2 whitespace-nowrap">状态，true 或 false</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">message</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">描述信息</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">数据</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">strategies</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object[]</td>
                            <td class="px-3 py-2 whitespace-nowrap">策略数据</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">id</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">策略 ID</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">name</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">策略名称</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <p class="text-xl mb-2 text-gray-800 font-semibold">图片相关</p>
        <div class="space-y-4 bg-white p-3 rounded-md mb-10 shadow-custom">
            <div>
                <p class="text-lg text-gray-700 font-semibold">上传图片</p>
                <x-code><span class="text-green-500 select-none">POST </span>/upload</x-code>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">Headers</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap"><span class="text-red-500">*</span>Content-Type</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">需要设置为 multipart/form-data</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">请求参数(Body)</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap"><span class="text-red-500">*</span>file</td>
                            <td class="px-3 py-2 whitespace-nowrap">File</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片文件</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">strategy_id</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">储存策略ID</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">album_id</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">相册ID，上传后图片自动归入该相册。不传则归入默认相册（若有）</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="my-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded text-sm text-gray-700">
                    <p class="font-semibold text-blue-700 mb-2">📷 相册使用说明</p>
                    <p class="mb-1">上传时传入 <code class="bg-blue-100 px-1 rounded">album_id</code> 参数，图片会自动归入对应相册。</p>
                    <p class="mb-2">当前已有相册：</p>
                    <table class="min-w-full mb-2">
                        <thead class="bg-blue-100">
                        <tr>
                            <th class="px-3 py-1 text-left text-xs font-medium text-blue-700">相册 ID</th>
                            <th class="px-3 py-1 text-left text-xs font-medium text-blue-700">相册名称</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                        @forelse($albums as $album)
                        <tr>
                            <td class="px-3 py-1 text-sm">{{ $album->id }}</td>
                            <td class="px-3 py-1 text-sm">{{ $album->name }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-3 py-1 text-sm text-gray-400">暂无相册，请先创建</td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                    <p class="mb-1">如果没有合适的相册，可以先创建：</p>
                    <ul class="list-disc pl-5 mb-2 text-gray-600">
                        <li>登录后进入「我的图片」页面 → 点击左上角「相册」按钮 → 点击「+」新建</li>
                        <li>新建后刷新此页面即可看到新相册的 ID</li>
                    </ul>
                    @if($albums->count() > 0)
                    <p class="text-gray-500">示例：<code class="bg-blue-100 px-1 rounded">curl -F "file=@photo.jpg" -F "album_id={{ $albums->first()->id }}" …</code>  → 图片归入 {{ $albums->first()->name }} 相册</p>
                    @endif
                </div>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">返回参数</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">status</td>
                            <td class="px-3 py-2 whitespace-nowrap">Boolean</td>
                            <td class="px-3 py-2 whitespace-nowrap">状态，true 或 false</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">message</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">描述信息</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">数据</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">key</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片唯一密钥</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">name</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片名称</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">pathname</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片路径名</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">origin_name</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片原始名</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">size</td>
                            <td class="px-3 py-2 whitespace-nowrap">Float</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片大小，单位 KB</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">mimetype</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片类型</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">extension</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片拓展名</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">md5</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片 md5 值</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">sha1</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片 sha1 值</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">links</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">链接</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">url</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片访问 url</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">html</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">-</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">bbcode</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">-</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">markdown</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">-</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">markdown_with_link</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">-</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">thumbnail_url</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">缩略图 url</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <p class="text-lg text-gray-700 font-semibold">图片列表</p>
                <x-code><span class="text-sky-500 select-none">GET </span>/images</x-code>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">请求参数(Query)</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">page</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">页码</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">order</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">排序方式，newest=最新，earliest=最早，utmost=最大，least=最小</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">permission</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">权限，public=公开的，private=私有的</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">album_id</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">相册 ID</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">keyword</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">筛选关键字</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">返回参数</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">status</td>
                            <td class="px-3 py-2 whitespace-nowrap">Boolean</td>
                            <td class="px-3 py-2 whitespace-nowrap">状态，true 或 false</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">message</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">描述信息</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">数据</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">current_page</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">当前所在页页码</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">last_page</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">最后一页页码</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">per_page</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">每页展示数据数量</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">total</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片总数量</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object[]</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片列表</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">key</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片唯一密钥</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">name</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片名称</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">origin_name</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片原始名称</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">pathname</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片路径名</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">size</td>
                            <td class="px-3 py-2 whitespace-nowrap">Float</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片大小，单位 KB</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">width</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片宽度</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">height</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片高度</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">md5</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片 md5 值</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">sha1</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片 sha1 值</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">human_date</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">上传时间(友好格式)</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">date</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">上传日期(yyyy-MM-dd HH:mm:ss)</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">links</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">链接，与上传接口返回参数中的 links 相同</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <p class="text-lg text-gray-700 font-semibold">删除图片</p>
                <x-code><span class="text-red-300 select-none">DELETE </span>/images/:key</x-code>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">请求参数(Params)</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap"><span class="text-red-500">*</span>key</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片密钥</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">返回参数</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">status</td>
                            <td class="px-3 py-2 whitespace-nowrap">Boolean</td>
                            <td class="px-3 py-2 whitespace-nowrap">状态，true 或 false</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">message</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">描述信息</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">数据</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <p class="text-xl mb-2 text-gray-800 font-semibold">相册相关</p>
        <div class="space-y-4 bg-white p-3 rounded-md mb-10 shadow-custom">
            <div>
                <p class="text-lg text-gray-700 font-semibold">相册列表</p>
                <x-code><span class="text-sky-500 select-none">GET </span>/albums</x-code>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">请求参数(Query)</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">page</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">页码</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">order</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">排序方式，newest=最新，earliest=最早，most=图片最多，least=图片最少</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">keyword</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">筛选关键字</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">返回参数</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">status</td>
                            <td class="px-3 py-2 whitespace-nowrap">Boolean</td>
                            <td class="px-3 py-2 whitespace-nowrap">状态，true 或 false</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">message</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">描述信息</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">数据</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">current_page</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">当前所在页页码</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">last_page</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">最后一页页码</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">per_page</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">每页展示数据数量</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">total</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">图片总数量</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-6">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object[]</td>
                            <td class="px-3 py-2 whitespace-nowrap">相册列表</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">id</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">相册自增 ID</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">name</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">相册名称</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">intro</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">相册简介</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap pl-10">image_num</td>
                            <td class="px-3 py-2 whitespace-nowrap">Integer</td>
                            <td class="px-3 py-2 whitespace-nowrap">相册图片数量</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <p class="text-lg text-gray-700 font-semibold">删除相册</p>
                <x-code><span class="text-red-300 select-none">DELETE </span>/albums/:id</x-code>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">请求参数(Params)</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap"><span class="text-red-500">*</span>id</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">相册自增 ID</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="my-4 overflow-x-auto">
                    <p class="text-sm mb-2">返回参数</p>
                    <table class="min-w-full">
                        <thead class="bg-white border">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                字段
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                类型
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                说明
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white border divide-y text-sm">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">status</td>
                            <td class="px-3 py-2 whitespace-nowrap">Boolean</td>
                            <td class="px-3 py-2 whitespace-nowrap">状态，true 或 false</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">message</td>
                            <td class="px-3 py-2 whitespace-nowrap">String</td>
                            <td class="px-3 py-2 whitespace-nowrap">描述信息</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">data</td>
                            <td class="px-3 py-2 whitespace-nowrap">Object</td>
                            <td class="px-3 py-2 whitespace-nowrap">数据</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
