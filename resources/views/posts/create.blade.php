<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規投稿') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('posts.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="title" :value="__('タイトル')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="content" :value="__('内容')" />
                            <textarea id="content" name="content" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('content') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('content')" />
                        </div>

                        <div>
                            <x-input-label for="tags" :value="__('タグ（カンマ区切り）')" />
                            <x-text-input id="tags" name="tags" type="text" class="mt-1 block w-full" :value="old('tags')" placeholder="例: Laravel, PHP, Web開発" />
                            <x-input-error class="mt-2" :messages="$errors->get('tags')" />
                        </div>

                        <div>
                            <x-input-label for="visibility" :value="__('公開設定')" />
                            <select id="visibility" name="visibility" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>公開</option>
                                <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>非公開</option>
                                <option value="confidential" {{ old('visibility') == 'confidential' ? 'selected' : '' }}>機密</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('visibility')" />
                        </div>

                        <div>
                            <x-input-label for="display_type" :value="__('表示形式')" />
                            <select id="display_type" name="display_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="0" {{ old('display_type', 0) == 0 ? 'selected' : '' }}>テキスト</option>
                                <option value="1" {{ old('display_type', 0) == 1 ? 'selected' : '' }}>Markdown</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('display_type')" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button onclick="window.history.back()" type="button" class="mr-3">
                                {{ __('キャンセル') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('投稿する') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
