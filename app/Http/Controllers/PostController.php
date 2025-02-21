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

    public function index(Request $request): View
    {
        $query = Post::with(['user', 'tagged']);

        if ($tag = $request->query('tag')) {
            $query->withAnyTag([$tag]);
        }

        $posts = $query->latest()->paginate(10);
        $posts->appends($request->query());

        return view('posts.index', compact('posts', 'tag'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'nullable|string'
        ]);

        try {
            $post = Auth::user()->posts()->create([
                'title' => $validated['title'],
                'content' => $validated['content'],
            ]);

            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
                $post->tag($tags);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '投稿が作成されました',
                    'post' => $post->load(['user', 'tagged'])
                ]);
            }

            return redirect()->route('posts.index')
                ->with('success', '投稿が作成されました');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '投稿の作成に失敗しました',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', '投稿の作成に失敗しました');
        }
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
