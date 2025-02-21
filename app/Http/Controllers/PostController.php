<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $posts = Post::with(['user', 'tagged'])
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'nullable|string'
        ]);

        $post = Auth::user()->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $post->tag($tags);
        }

        return redirect()->route('posts.index')
            ->with('success', '投稿が作成されました');
    }

    public function show(Post $post): View
    {
        $post->load(['user', 'tagged']);
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);
        $post->load('tagged');
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'nullable|string'
        ]);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        if (isset($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $post->retag($tags);
        }

        return redirect()->route('posts.show', $post)
            ->with('success', '投稿が更新されました');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', '投稿が削除されました');
    }
}
