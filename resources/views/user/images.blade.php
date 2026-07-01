@section('title', '我的图片')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/justified-gallery/justifiedGallery.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewer-js/viewer.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/context-js/context-js.css') }}">
    <style>
        /* Make image selector always visible for easier selection */
        #images-grid .images-item .image-selector { display: block !important; }
        #images-grid .images-item:hover { outline-color: #3b82f6; }
        #images-grid .images-item.ds-selected { outline-color: #3b82f6; }
        #images-grid .images-item .image-selector i { transition: all 0.15s; }
        #images-grid .images-item:hover .image-selector i,
        #images-grid .images-item.ds-selected .image-selector i { border-color: #fff; color: #0061ff; }
    </style>
@endpush

<x-app-layout>
    <div class="relative flex justify-between items-center px-2 py-2 z-[3] top-0 left-0 right-0 bg-white border-solid border-b">
        <div class="space-x-2 flex justify-between items-center">
            <div class="flex-row hidden lg:flex">
                <a data-operate="movements" class="hidden text-sm py-2 px-3 hover:bg-gray-100 rounded text-gray-800" href="javascript:void(0)">移动到相册</a>
                <a data-operate="remove" class="hidden text-sm py-2 px-3 hover:bg-gray-100 rounded text-gray-800" href="javascript:void(0)">移出当前相册</a>
                <a data-operate="permission" class="hidden text-sm py-2 px-3 hover:bg-gray-100 rounded text-gray-800" href="javascript:void(0)">设置权限</a>
                <a data-operate="detail" class="hidden text-sm py-2 px-3 hover:bg-gray-100 rounded text-gray-800" href="javascript:void(0)">详细信息</a>
                <a data-operate="rename" class="hidden text-sm py-2 px-3 hover:bg-gray-100 rounded text-gray-800" href="javascript:void(0)">重命名</a>
                <a data-operate="delete" class="hidden text-sm py-2 px-3 hover:bg-gray-100 rounded text-gray-800" href="javascript:void(0)">删除</a>
            </div>
            <div class="block lg:hidden">
                <x-dropdown direction="right">
                    <x-slot name="trigger">
                        <a class="text-sm py-2 px-3 hover:bg-gray-100 rounded text-gray-800" href="javascript:void(0)"><i class="fas fa-ellipsis-h text-blue-500"></i></a>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link data-operate="refresh" href="javascript:void(0)" @click="open = false">刷新</x-dropdown-link>
                        <x-dropdown-link data-operate="movements" class="hidden" href="javascript:void(0)" @click="open = false">移动到相册</x-dropdown-link>
                        <x-dropdown-link data-operate="remove" class="hidden" href="javascript:void(0)" @click="open = false">移出当前相册</x-dropdown-link>
                        <x-dropdown-link data-operate="permission" class="hidden" href="javascript:void(0)" @click="open = false">设置权限</x-dropdown-link>
                        <x-dropdown-link data-operate="detail" class="hidden" href="javascript:void(0)" @click="open = false">详细信息</x-dropdown-link>
                        <x-dropdown-link data-operate="rename" class="hidden" href="javascript:void(0)" @click="open = false">重命名</x-dropdown-link>
                        <x-dropdown-link data-operate="delete" class="hidden" href="javascript:void(0)" @click="open = false">删除</x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
        <div class="flex space-x-2 items-center">
            <input type="text" id="search" class="px-2.5 py-1.5 border-0 outline-none rounded bg-gray-100 text-sm transition-all duration-300 hidden md:block md:w-36 md:hover:w-52 md:focus:w-52" placeholder="输入关键字搜索...">
            <x-dropdown direction="left">
                <x-slot name="trigger">
                    <a id="order" class="text-sm py-2 px-3 hover:bg-gray-100 rounded text-gray-800" href="javascript:void(0)">
                        <span>最新</span>
                        <i class="fas fa-sort-alpha-up text-blue-500"></i>
                    </a>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link href="javascript:void(0)" @click="setOrderBy('newest'); open = false">最新
                    </x-dropdown-link>
                    <x-dropdown-link href="javascript:void(0)" @click="setOrderBy('earliest'); open = false">最早
                    </x-dropdown-link>
                    <x-dropdown-link href="javascript:void(0)" @click="setOrderBy('utmost'); open = false">最大
                    </x-dropdown-link>
                    <x-dropdown-link href="javascript:void(0)" @click="setOrderBy('least'); open = false">最小
                    </x-dropdown-link>
                </x-slot>
            </x-dropdown>
            <x-dropdown direction="left">
                <x-slot name="trigger">
                    <a id="permission" class="text-sm py-2 px-3 hover:bg-gray-100 rounded text-gray-800" href="javascript:void(0)">
                        <span>全部</span>
                        <i class="fas fa-eye text-blue-500"></i>
                    </a>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link href="javascript:void(0)" @click="open = false; setPermission('all')">全部
                    </x-dropdown-link>
                    <x-dropdown-link href="javascript:void(0)" @click="open = false; setPermission('public')">公开
                    </x-dropdown-link>
                    <x-dropdown-link href="javascript:void(0)" @click="open = false; setPermission('private')">私有
                    </x-dropdown-link>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
    <div class="relative inset-0 h-full overflow-hidden flex">
        <!-- 左侧相册列表 -->
        <div id="albums-sidebar" class="w-48 md:w-56 bg-gray-50 border-r border-gray-200 flex-shrink-0 flex flex-col h-full overflow-hidden">
            <div class="px-3 py-2 border-b border-gray-200 bg-white flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">相册</span>
                <a href="javascript:void(0)" onclick="$('#album-add').toggleClass('hidden')" class="text-blue-500 hover:text-blue-600">
                    <i class="fas fa-plus text-sm"></i>
                </a>
            </div>
            <div id="albums-list" class="flex-1 overflow-y-auto p-2 space-y-1">
                <!-- 相册列表将通过JS动态加载 -->
                <div class="text-center text-gray-400 py-4 text-sm">加载中...</div>
            </div>
        </div>

        <!-- 右侧图片区域 -->
        <div class="flex-1 relative">
            <!-- content -->
            <div id="images-scroll" class="absolute inset-0 overflow-y-scroll dragselect select-none">
                <div id="images-grid" class="dragselect"></div>
            </div>
            <!-- right drawer -->
            <div id="drawer-mask" class="absolute hidden inset-0 bg-gray-500 bg-opacity-50 z-[2]" onclick="drawer.close()"></div>
            <div id="drawer" class="absolute bg-white w-64 md:w-72 top-0 -right-[1000px] bottom-0 z-[2] flex flex-col transition-all duration-300">
                <div class="flex justify-between items-center text-md px-3 py-1 border-b">
                    <span class="text-gray-600 truncate" id="drawer-title"></span>
                    <a href="javascript:drawer.close()" class="p-2"><i class="fas fa-times text-blue-500"></i></a>
                </div>
                <div id="drawer-content" class="overflow-y-auto"></div>
            </div>
        </div>
    </div>

    <script type="text/html" id="images-item-tpl">
        <a href="javascript:void(0)" data-id="__id__" data-json='__json__' class="images-item relative cursor-default rounded outline outline-2 outline-offset-2 outline-transparent">
            <div class="image-selector absolute z-[2] top-0 right-0 overflow-hidden cursor-pointer sm:hidden group-hover:block">
                <div class="p-1 text-xl sm:text-2xl">
                    <i class="fas fa-check-circle block rounded-full bg-white text-white border border-gray-500"></i>
                </div>
            </div>
            <div class="image-mask absolute left-0 right-0 bottom-0 h-20 z-[1] bg-gradient-to-t from-black" onclick="$(this).siblings('img').trigger('click')">
                <div class="absolute left-2 bottom-2 text-white z-[2] w-[90%]">
                    <p class="text-sm truncate filename" title="__name__">__name__</p>
                    <p class="text-xs date" title="__human_date__">__date__</p>
                </div>
            </div>
            <img alt="__name__" data-original="__url__" src="__thumb_url__" width="__width__" height="__height__">
        </a>
    </script>

    <script type="text/html" id="album-add-tpl">
        <div id="album-add" class="flex flex-col w-full hidden border rounded p-2 bg-white mb-2">
            <p class="error-message text-white p-2 mb-2 text-sm bg-red-500 rounded hidden"></p>
            <form class="w-full space-y-2" action="/user/albums">
                <input type="text" class="w-full rounded px-2.5 py-1.5 text-sm border-0 bg-gray-200" name="name" placeholder="请输入名称">
                <textarea class="w-full resize-y rounded-md text-sm border-0 bg-gray-200" name="intro" placeholder="请输入简介"></textarea>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 py-1 px-2 bg-indigo-500 text-white text-sm text-center tracking-wider font-semibold rounded-md">创建</button>
                    <button type="button" onclick="$('#album-add').addClass('hidden')" class="py-1 px-2 bg-gray-300 text-gray-700 text-sm rounded-md">取消</button>
                </div>
            </form>
        </div>
    </script>

    <script type="text/html" id="albums-item-tpl">
        <a href="javascript:void(0)" data-id="__id__" data-json='__json__' title="__intro__" class="albums-item flex justify-between items-center group px-2 h-8 rounded w-full text-gray-700 hover:bg-blue-100 cursor-pointer">
            <span class="text-sm truncate w-[70%] name">__name__</span>
            <div class="flex items-center justify-center space-x-1 hidden group-hover:block">
                <span class="update text-gray-500 hover:text-blue-500"><i class="fas fa-edit text-xs"></i></span>
                <span class="delete text-gray-500 hover:text-red-500"><i class="fas fa-trash-alt text-xs"></i></span>
            </div>
            <span class="group-hover:hidden text-xs text-gray-400">__image_num__</span>
        </a>
    </script>

    <script type="text/html" id="album-update-tpl">
        <div id="album-edit" data-id="__id__" class="flex flex-col w-full border rounded p-2 bg-white mb-2">
            <p class="error-message text-white p-2 mb-2 text-sm bg-red-500 rounded hidden"></p>
            <form class="w-full space-y-2" action="/user/albums/__id__">
                <input type="text" class="w-full rounded px-2.5 py-1.5 text-sm border-0 bg-gray-200" placeholder="请输入名称" name="name" value="__name__">
                <textarea class="w-full resize-y rounded-md text-sm border-0 bg-gray-200" name="intro" placeholder="请输入简介">__intro__</textarea>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 py-1 px-2 bg-indigo-500 text-white text-sm text-center tracking-wider font-semibold rounded-md">确认</button>
                    <button type="button" onclick="$(this).closest('#album-edit').remove()" class="py-1 px-2 bg-gray-300 text-gray-700 text-sm rounded-md">取消</button>
                </div>
            </form>
        </div>
    </script>

    <script type="text/html" id="image-detail-tpl">
        <div class="my-4 px-4 space-y-3">
            <div>
                <span class="text-sm font-semibold">相册名称</span>
                <p class="my-2 break-words text-gray-700">__album_name__</p>
            </div>
            <div>
                <div class="text-sm font-semibold">使用策略</div>
                <p class="my-2 break-words text-gray-600">__strategy_name__</p>
            </div>
            <div>
                <div class="text-sm font-semibold">图片名称</div>
                <p class="my-2 break-words text-gray-600">__filename__</p>
            </div>
            <div>
                <div class="text-sm font-semibold">链接</div>
                <div class="my-2 p-2 bg-gray-100 rounded">
                    <div class="space-y-1">
                        <div>
                            <span class="text-xs text-gray-500">直链</span>
                            <p class="text-xs text-blue-500 truncate">__url__</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Markdown</span>
                            <p class="text-xs text-gray-600 break-all">__markdown__</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">HTML</span>
                            <p class="text-xs text-gray-600 break-all">__html__</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">BBCode</span>
                            <p class="text-xs text-gray-600 break-all">__bbcode__</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">缩略图</span>
                            <p class="text-xs text-blue-500 truncate">__thumb_url__</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-between text-sm text-gray-500">
                <span>尺寸：__width__ x __height__</span>
                <span>大小：__size__</span>
            </div>
            <div class="text-sm text-gray-500">上传时间：__date__</div>
        </div>
    </script>

    @push('scripts')
        <script src="{{ asset('js/justified-gallery/jquery.justifiedGallery.min.js') }}"></script>
        <script src="{{ asset('js/dragselect/ds.min.js') }}"></script>
        <script src="{{ asset('js/viewer-js/viewer.min.js') }}"></script>
        <script src="{{ asset('js/context-js/context-js.js') }}"></script>
        <script>
            const IMAGES_SCROLL = '#images-scroll';
            const IMAGES_GRID = '#images-grid';
            const IMAGES_ITEM = '.images-item';
            const ALBUM_ITEM = '.albums-item';
            const HEADER_TITLE = '#header-title';

            const gridConfigs = {
                rowHeight: 180,
                maxRowHeight: 360,
                margins: 8,
                lastRow: 'nojustify',
                justifyThreshold: 0.9,
                cssAnimation: true
            };

            const $headerTitle = $(HEADER_TITLE);
            const $photos = $(IMAGES_GRID);
            const $drawer = $("#drawer");
            const $drawerMask = $('#drawer-mask');
            let viewer = new Viewer(document.getElementById('images-grid'), {url: 'data-original'});
            const drawer = {
                open(title, content, callback) {
                    $drawerMask.fadeIn();
                    $drawer.css('right', 0);
                    $drawer.find('#drawer-title').html(title);
                    $drawer.find('#drawer-content').html(content);
                    callback && callback();
                },
                close(callback) {
                    $drawerMask.fadeOut();
                    $drawer.css('right', '-1000px');
                    callback && callback();
                },
                toggle(title, content, callback) {
                    if ($drawerMask.is(':hidden')) {
                        this.open(title, content, callback);
                    } else {
                        this.close(callback);
                    }
                }
            }

            // Gallery will be initialized on first image load via galleryNeedsInit

            let albumsInfinite = null;
            const imagesInfinite = utils.infiniteScroll(IMAGES_SCROLL, {
                url: '{{ route('user.images') }}',
                classes: ['dragselect'],
                success: function (response) {
                    if (!response.status) {
                        return toastr.error(response.message);
                    }

                    let images = response.data.images.data;
                    if (images.length <= 0 || response.data.images.current_page === response.data.images.last_page) {
                        this.finished = true;
                    }

                    let html = '';
                    for (const i in images) {
                        let item = $('#images-item-tpl').html()
                            .replace(/__id__/g, images[i].id)
                            .replace(/__url__/g, images[i].url)
                            .replace(/__thumb_url__/g, images[i].thumb_url)
                            .replace(/__width__/g, images[i].width)
                            .replace(/__height__/g, images[i].height)
                            .replace(/__name__/g, images[i].filename)
                            .replace(/__human_date__/g, images[i].human_date)
                            .replace(/__date__/g, images[i].date)
                            .replace(/__json__/g, JSON.stringify(images[i]))
                        html += item;
                    }

                    $photos.append(html);
                    if (galleryNeedsInit) {
                        $photos.justifiedGallery(gridConfigs);
                        galleryNeedsInit = false;
                        // Reinitialize Viewer for new images
                        if (viewer) viewer.destroy();
                        viewer = new Viewer(document.getElementById('images-grid'), {url: 'data-original'});
                        // DragSelect auto-detects new elements via event delegation
                    } else {
                        $photos.justifiedGallery('norewind');
                    }
                }
            });

            let galleryNeedsInit = true;
            const resetImages = (params) => {
                try { $photos.justifiedGallery('destroy'); } catch(e) {}
                $photos.html('');
                galleryNeedsInit = true;
                ds.clearSelection();
                params = $.extend({page: 1}, params)
                imagesInfinite.refresh(params);
            }

            // 初始化相册列表
            const initAlbums = () => {
                const $albumsList = $('#albums-list');
                const CREATE_ID = '#album-add';

                // 加载相册
                $.ajax({
                    url: '{{ route('user.albums') }}',
                    success: function(response) {
                        if (!response.status) {
                            return toastr.error(response.message);
                        }

                        let albums = response.data.albums.data;
                        let html = $('#album-add-tpl').html();
                        for (const i in albums) {
                            let item = $('#albums-item-tpl').html()
                                .replace(/__id__/g, albums[i].id)
                                .replace(/__name__/g, albums[i].name)
                                .replace(/__intro__/g, albums[i].intro)
                                .replace(/__image_num__/g, albums[i].image_num)
                                .replace(/__json__/g, JSON.stringify(albums[i]))
                            if (albums[i].id === selectedAlbum.id) {
                                item = item
                                    .replace(/bg-gray-100/g, 'bg-blue-400')
                                    .replace(/text-gray-800/g, 'text-white')
                            }
                            html += item;
                        }
                        $albumsList.html(html);

                        // 绑定点击事件
                        bindAlbumEvents();
                    }
                });
            }

            // 绑定相册事件
            const bindAlbumEvents = () => {
                const $albumsList = $('#albums-list');
                const CREATE_ID = '#album-add';
                const UPDATE_ID = '#album-edit';

                // 点击相册
                $albumsList.off('click', '>a.albums-item').on('click', '>a.albums-item', function() {
                    if (selectedAlbum.id === $(this).data('id')) {
                        selectedAlbum = {};
                    } else {
                        selectedAlbum = $(this).data('json');
                    }
                    resetImages({page: 1, album_id: selectedAlbum.id || null});
                    ds.clearSelection();
                    // 更新高亮
                    $albumsList.find('a.albums-item').removeClass('bg-blue-400 text-white').addClass('bg-transparent text-gray-700');
                    if (selectedAlbum.id) {
                        $(this).removeClass('bg-transparent text-gray-700').addClass('bg-blue-400 text-white');
                    }
                });

                // 编辑相册
                $albumsList.off('click', '.update').on('click', '.update', function(e) {
                    e.stopPropagation();
                    let $item = $(this).closest('a.albums-item');
                    $albumsList.find(UPDATE_ID).remove();
                    $item.after($('#album-update-tpl').html()
                        .replace(/__id__/g, $item.data('id'))
                        .replace(/__name__/g, $item.find('>span.name').html())
                        .replace(/__intro__/g, $item.attr('title'))
                    );
                });

                // 删除相册
                $albumsList.off('click', '.delete').on('click', '.delete', function(e) {
                    e.stopPropagation();
                    Swal.fire({
                        title: '确认删除该相册?',
                        text: "删除后相册中的图片将会被移出。",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '确认',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $(this).closest(ALBUM_ITEM).data('id');
                            axios.delete(`/user/albums/${id}`).then(response => {
                                if (response.data.status) {
                                    selectedAlbum = {};
                                    initAlbums();
                                    resetImages();
                                } else {
                                    toastr.error(response.data.message);
                                }
                            });
                        }
                    });
                });

                // 创建相册
                $albumsList.off('submit', CREATE_ID + ' form').on('submit', CREATE_ID + ' form', function(e) {
                    e.preventDefault();
                    let $form = $(this);
                    axios.post($form.attr('action'), $form.serialize()).then(response => {
                        let $errorMessage = $albumsList.find(CREATE_ID + ' .error-message').html('').hide();
                        if (response.data.status) {
                            $form.get(0).reset();
                            initAlbums();
                        } else {
                            $errorMessage.html('<i class="fas fa-exclamation-circle"></i> ' + response.data.message).show();
                        }
                    });
                });

                // 更新相册
                $albumsList.off('submit', UPDATE_ID + ' form').on('submit', UPDATE_ID + ' form', function(e) {
                    e.preventDefault();
                    let $form = $(this);
                    let $editContainer = $(this).closest(UPDATE_ID);
                    axios.put($form.attr('action'), $form.serialize()).then(response => {
                        let $errorMessage = $albumsList.find(UPDATE_ID + ' .error-message').html('').hide();
                        if (response.data.status) {
                            $albumsList.find(`>a[data-id=${$editContainer.data('id')}]`)
                                .attr('title', $form.find('textarea').val())
                                .find('.name').text($form.find('input').val());
                            $editContainer.remove();
                        } else {
                            $errorMessage.html('<i class="fas fa-exclamation-circle"></i> ' + response.data.message).show();
                        }
                    });
                });
            }

            // 页面加载时初始化相册
            initAlbums();

            const setOrderBy = function (sort) {
                resetImages({page: 1, order: sort})
                $('#order span').text({newest: '最新', earliest: '最早', utmost: '最大', least: '最小'}[sort]);
            };

            const setPermission = function (permission) {
                resetImages({page: 1, permission: permission})
                $('#permission span').text({public: '公开', private: '私有', all: '全部'}[permission]);
            };

            $('#search').keydown(function (e) {
                if (e.keyCode === 13) {
                    resetImages({page: 1, keyword: $(this).val()});
                }
            });

            $(document).keydown(e => {
                if (e.keyCode === 65 && (e.altKey || e.metaKey)) {
                    e.preventDefault();
                    ds.setSelection($(IMAGES_ITEM));
                }
            });
        </script>
        <script>
            const ds = new DragSelect({
                area: $(IMAGES_SCROLL).get(0),
                keyboardDrag: false,
            });
            // Event delegation: click image → show in Viewer
            $(IMAGES_SCROLL).on('click', '.images-item', function(e) {
                if ($(e.target).closest('.image-selector').length) return;
                e.preventDefault();
                let $item = $(this);
                let $items = $('#images-grid .images-item');
                let idx = $items.index($item);
                if (viewer) {
                    try { viewer.view(idx); } catch(err) {}
                    viewer.show();
                }
            });
            $(IMAGES_SCROLL).on('click', '.image-selector', function(e) {
                e.stopPropagation();
                let $item = $(this).closest('.images-item');
                ds.toggleSelect($item.get(0));
            });

            const bindOperates = () => {
                let selected = ds.getSelection();
                if (selected.length) {
                    $headerTitle.text(`已选择 ${selected.length} 张图片`);
                } else {
                    $headerTitle.text('我的图片');
                }
                $('[data-operate]').hide();
                let operates = [];
                if (selected.length === 0) {
                    operates = ['refresh'];
                }
                if (selected.length === 1) {
                    operates = ['refresh', 'movements', 'permission', 'detail', 'rename', 'delete'];
                }
                if (selected.length > 1) {
                    operates = ['refresh', 'movements', 'permission', 'delete'];
                }
                if (selected.length && selectedAlbum.id !== undefined) {
                    operates.push('remove');
                }
                $(operates.map(item => `[data-operate=${item}]`).toString()).css('display', 'block');
            };

            ds.addSelectionCallback(bindOperates);
            ds.addCallback((items) => {
                if (!items.length) {
                    $headerTitle.text('我的图片');
                }
            });

            $('[data-operate]').on('click', function () {
                let operate = $(this).data('operate');
                let selected = ds.getSelection();
                if (operate === 'refresh') {
                    resetImages();
                    return false;
                }

                if (!selected.length && operate !== 'refresh') {
                    return false;
                }

                switch (operate) {
                    case 'movements': // 移动到相册
                        methods.movements();
                        break;
                    case 'remove': // 移出当前相册
                        methods.remove();
                        break;
                    case 'rename': // 重命名
                        methods.rename(selected[0]);
                        break;
                    case 'permission':
                        methods.permission();
                        break;
                    case 'detail':
                        methods.detail(selected[0]);
                        break;
                    case 'delete': // 删除
                        methods.delete();
                        break;
                }
            });
        </script>
        <script>
            let selectedAlbum = {};
            const methods = {
                movements() {
                    let selected = ds.getSelection();
                    const content = $('#albums-container-tpl').html();
                    drawer.toggle('移动到相册', content, function () {
                        let $albums = $('#albums-container');
                        const CREATE_ID = '#album-add';
                        albumsInfinite = utils.infiniteScroll('#drawer-content', {
                            url: '{{ route('user.albums') }}',
                            success: function (response) {
                                if (!response.status) {
                                    return toastr.error(response.message);
                                }

                                let albums = response.data.albums.data;
                                if (albums.length <= 0 || response.data.albums.current_page === response.data.albums.last_page) {
                                    this.finished = true;
                                }

                                let html = '';
                                for (const i in albums) {
                                    let item = $('#albums-item-tpl').html()
                                        .replace(/__id__/g, albums[i].id)
                                        .replace(/__name__/g, albums[i].name)
                                        .replace(/__intro__/g, albums[i].intro)
                                        .replace(/__image_num__/g, albums[i].image_num)
                                        .replace(/__json__/g, JSON.stringify(albums[i]))
                                    html += item;
                                }

                                $albums.append(html);

                                callback && callback.call(this, $albums.get(0));
                            }
                        });

                        $albums.off('click', '>a').on('click', '>a', function () {
                            let id = $(this).data('id');
                            let ids = [];
                            selected.forEach(item => ids.push($(item).data('id')));
                            axios.put('{{ route('user.images.movement') }}', {
                                selected: ids,
                                id: id
                            }).then(response => {
                                if (response.data.status) {
                                    toastr.success('移动成功');
                                    resetImages();
                                    setTimeout(_ => drawer.close(), 300)
                                } else {
                                    toastr.error(response.data.message);
                                }
                            });
                        });
                    });
                },
                remove() {
                    let selected = ds.getSelection();
                    let ids = [];
                    selected.forEach(item => ids.push($(item).data('id')));
                    axios.put('{{ route('user.images.movement') }}', {
                        selected: ids,
                        id: 0
                    }).then(response => {
                        if (response.data.status) {
                            toastr.success('移出成功');
                            resetImages();
                        } else {
                            toastr.error(response.data.message);
                        }
                    });
                },
                rename(image) {
                    let $image = $(image);
                    Swal.fire({
                        title: '请输入新的名称',
                        input: 'text',
                        inputValue: $image.find('.filename').text(),
                        showCancelButton: true,
                        confirmButtonText: '确认',
                        cancelButtonText: '取消',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.put('{{ route('user.images.rename') }}', {
                                id: $image.data('id'),
                                name: result.value
                            }).then(response => {
                                if (response.data.status) {
                                    toastr.success('修改成功');
                                    $image.find('.filename').text(result.value);
                                    let json = $image.data('json');
                                    json.filename = result.value;
                                    $image.data('json', json);
                                } else {
                                    toastr.error(response.data.message);
                                }
                            });
                        }
                    });
                },
                permission() {
                    let selected = ds.getSelection();
                    let ids = [];
                    selected.forEach(item => ids.push($(item).data('id')));
                    let permission = selected.eq(0).data('json').permission === 0 ? 1 : 0;
                    axios.put('{{ route('user.images.permission') }}', {
                        selected: ids,
                        id: permission
                    }).then(response => {
                        if (response.data.status) {
                            toastr.success('修改成功');
                            resetImages();
                        } else {
                            toastr.error(response.data.message);
                        }
                    });
                },
                detail(image) {
                    let data = $(image).data('json');
                    let content = $('#image-detail-tpl').html()
                        .replace(/__album_name__/g, data.album_name || '未分组')
                        .replace(/__strategy_name__/g, data.strategy_name)
                        .replace(/__filename__/g, data.filename)
                        .replace(/__url__/g, data.url)
                        .replace(/__thumb_url__/g, data.thumb_url)
                        .replace(/__markdown__/g, `![${data.filename}](${data.url})`)
                        .replace(/__html__/g, `<img src="${data.url}" alt="${data.filename}">`)
                        .replace(/__bbcode__/g, `[img]${data.url}[/img]`)
                        .replace(/__width__/g, data.width)
                        .replace(/__height__/g, data.height)
                        .replace(/__size__/g, (data.size / 1024).toFixed(2) + ' KB')
                        .replace(/__date__/g, data.date);
                    drawer.toggle('图片详情', content);
                },
                delete() {
                    let selected = ds.getSelection();
                    let ids = [];
                    selected.forEach(item => ids.push($(item).data('id')));
                    Swal.fire({
                        title: '确认删除选中的图片?',
                        text: "删除后将无法恢复！",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '确认',
                        cancelButtonText: '取消',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.delete('{{ route('user.images.delete') }}', {
                                data: {selected: ids}
                            }).then(response => {
                                if (response.data.status) {
                                    toastr.success('删除成功');
                                    resetImages();
                                } else {
                                    toastr.error(response.data.message);
                                }
                            });
                        }
                    });
                }
            }
        </script>
    @endpush
</x-app-layout>
