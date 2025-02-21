<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ isset($tag) ? "タグ: $tag の投稿一覧" : __('投稿一覧') }}
            </h2>
            @auth
            <a href="{{ route('posts.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                新規投稿
            </a>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @forelse ($posts as $post)
                    <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold mb-2">
                                    <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-4">{{ Str::limit($post->content, 200) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    @if($post->user->avatar)
                                        <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="w-6 h-6 rounded-full">
                                    @endif
                                    <span class="text-sm text-gray-600">
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
                    @empty
                        <p class="text-gray-600">投稿がありません。</p>
                    @endforelse

                    <div class="mt-6">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
