<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ isset($tag) ? "タグ: $tag の投稿一覧" : __('投稿一覧') }}
            </h2>
            @auth
            <button onclick="openPostModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                新規投稿
            </button>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex gap-6">
                <!-- メインコンテンツ -->
                <div class="flex-grow">
                    <div class="bg-transparent">
                        <div class="p-6 space-y-4">
                            @forelse ($posts as $post)
                            <div class="rounded-lg {{ Auth::id() === $post->user_id ? 'bg-white' : 'bg-indigo-50 border-indigo-100' }} border-2 shadow hover:shadow-md transition-all duration-300">
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-2xl font-bold mb-2">
                                                    <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:text-blue-800">
                                                        {{ $post->title }}
                                                    </a>
                                                </h3>
                                                @if($post->visibility !== 'public')
                                                    <span class="px-2 py-1 text-xs rounded {{ $post->visibility === 'private' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ App\Models\Post::getVisibilityOptions()[$post->visibility] }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($post->visibility !== App\Models\Post::VISIBILITY_CONFIDENTIAL)
                                                <p class="text-gray-600 mb-4">{{ Str::limit($post->content, 200) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center space-x-2">
                                                @if($post->user->avatar)
                                                    <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="w-6 h-6 rounded-full">
                                                @endif
                                                <span class="text-sm {{ Auth::id() === $post->user_id ? 'text-gray-600' : 'text-indigo-600 font-medium' }}">
                                                    {{ $post->user->name }}
                                                </span>
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                {{ $post->created_at->format('Y/m/d H:i') }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($post->tagged as $tag)
                                            <a href="{{ route('posts.index', ['tag' => $tag->tag_name]) }}" 
                                               class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full text-sm">
                                                #{{ $tag->tag_name }}
                                            </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                                <p class="text-gray-600">投稿がありません。</p>
                            @endforelse

                            <div class="mt-6">
                                {{ $posts->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- サイドバー -->
                <div class="w-64 flex-shrink-0">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">タグ一覧</h3>
                        <div class="space-y-2">
                            @foreach($tags as $tagItem)
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('posts.index', ['tag' => $tagItem->name]) }}" 
                                       class="text-sm {{ $tag === $tagItem->name ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                                        #{{ $tagItem->name }}
                                    </a>
                                    <span class="text-xs text-gray-500">{{ $tagItem->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="postModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-blue-50 bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">新規投稿</h3>
                <form id="quickPostForm">
                    @csrf
                    <div class="mb-4">
                        <input type="text" name="title" id="title" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="{{ __('タイトル') }}" required>
                    </div>
                    <div class="mb-4">
                        <textarea name="content" id="content" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="{{ __('投稿内容を入力してください') }}" required></textarea>
                    </div>
                    <div class="mb-4">
                        <input type="text" name="tags" id="tags" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="{{ __('タグをカンマ区切りで入力（例：php, laravel）') }}">
                    </div>
                    <div class="mb-4">
                        <label for="modalVisibility" class="block text-sm font-medium text-gray-700 mb-1">公開設定</label>
                        <select id="modalVisibility" name="visibility" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="public">公開</option>
                            <option value="private">非公開</option>
                            <option value="confidential">機密</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="modalDisplayType" class="block text-sm font-medium text-gray-700 mb-1">表示形式</label>
                        <select id="modalDisplayType" name="display_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @foreach(App\Models\Post::getDisplayTypeOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closePostModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            キャンセル
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            投稿する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openPostModal() {
            document.getElementById('postModal').classList.remove('hidden');
        }

        function closePostModal() {
            document.getElementById('postModal').classList.add('hidden');
            document.getElementById('quickPostForm').reset();
        }

        function submitQuickPost(event) {
            event.preventDefault();
            
            const formData = {
                title: document.getElementById('title').value,
                content: document.getElementById('content').value,
                tags: document.getElementById('tags').value,
                visibility: document.getElementById('modalVisibility').value,
                display_type: document.getElementById('modalDisplayType').value,
                _token: document.querySelector('input[name="_token"]').value
            };

            fetch('{{ route('posts.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // 成功時の処理
                    window.location.href = data.redirect;
                }
            })
            .catch(error => {
                // エラー処理
                console.error('Error:', error);
                if (error.errors) {
                    // バリデーションエラーの処理
                    Object.keys(error.errors).forEach(key => {
                        const input = document.getElementById(key);
                        if (input) {
                            input.classList.add('border-red-500');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'text-red-500 text-sm mt-1';
                            errorDiv.textContent = error.errors[key][0];
                            input.parentNode.appendChild(errorDiv);
                        }
                    });
                }
            });
        }

        document.getElementById('quickPostForm').addEventListener('submit', submitQuickPost);
    </script>
</x-app-layout>
