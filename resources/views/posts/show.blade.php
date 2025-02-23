<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $post->title }}
            </h2>
            @can('update', $post)
            <div class="flex space-x-4">
                <a href="{{ route('posts.edit', $post) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    編集
                </a>
                <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline" onsubmit="return confirm('本当に削除しますか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        削除
                    </button>
                </form>
            </div>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="flex items-center space-x-2">
                                @if($post->user->avatar)
                                    <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="w-8 h-8 rounded-full">
                                @endif
                                <span class="text-gray-600 font-medium">
                                    {{ $post->user->name }}
                                </span>
                            </div>
                            <span class="text-gray-500">
                                {{ $post->created_at->format('Y/m/d H:i') }}
                            </span>
                        </div>
                        <div class="prose max-w-none">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                        
                        <!-- ACTIONボタン -->
                        <div class="mt-4">
                            <button onclick="openReplyModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                ACTION
                            </button>
                        </div>
                    </div>

                    <!-- 返信モーダル -->
                    <div id="replyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">返信を作成</h3>
                                <form method="POST" action="{{ route('posts.store') }}" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $post->id }}">
                                    <div class="mb-4">
                                        <label for="title" class="block text-gray-700 text-sm font-bold mb-2">タイトル</label>
                                        <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="content" class="block text-gray-700 text-sm font-bold mb-2">内容</label>
                                        <textarea name="content" id="content" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                                    </div>
                                    <input type="hidden" name="visibility" value="public">
                                    <div class="flex justify-end space-x-4">
                                        <button type="button" onclick="closeReplyModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                            キャンセル
                                        </button>
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            投稿
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 返信投稿の表示 -->
                    @if($post->replies->isNotEmpty())
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">返信</h3>
                        <div class="space-y-6">
                            @foreach($post->replies as $reply)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-center space-x-4 mb-2">
                                        <div class="flex items-center space-x-2">
                                            @if($reply->user->avatar)
                                                <img src="{{ $reply->user->avatar }}" alt="{{ $reply->user->name }}" class="w-8 h-8 rounded-full">
                                            @endif
                                            <span class="text-gray-600 font-medium">{{ $reply->user->name }}</span>
                                        </div>
                                        <span class="text-gray-500">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                                    </div>
                                    @can('update', $reply)
                                    <div class="flex space-x-2">
                                        <a href="{{ route('posts.edit', $reply) }}" class="text-yellow-500 hover:text-yellow-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('posts.destroy', $reply) }}" class="inline" onsubmit="return confirm('本当に削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    @endcan
                                </div>
                                <h4 class="font-semibold mb-2">{{ $reply->title }}</h4>
                                <div class="prose max-w-none">
                                    {!! nl2br(e($reply->content)) !!}
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('posts.show', $reply) }}" class="text-blue-500 hover:text-blue-700">
                                        詳細を見る
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($post->tags->isNotEmpty())
                    <div class="mt-6 border-t pt-6">
                        <h3 class="text-lg font-semibold mb-3">タグ</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                                #{{ $tag->name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mt-8 border-t pt-6">
                        <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800">
                            &larr; 投稿一覧に戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
