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
                    </div>

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
