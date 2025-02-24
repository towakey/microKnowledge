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

        if (!Auth::check()) {
            $query->where('visibility', Post::VISIBILITY_PUBLIC);
        } else {
            $query->where(function($q) {
                $q->where('visibility', Post::VISIBILITY_PUBLIC)
                  ->orWhere('user_id', Auth::id());
            });
        }

        if ($tag = $request->query('tag')) {
            $query->withAnyTag([$tag]);
        }

        $posts = $query->latest()->paginate(10);
        $posts->appends($request->query());

        // タグ一覧を取得（アクセス可能な投稿のタグのみ）
        $tagsQuery = \Illuminate\Support\Facades\DB::table('tagging_tagged')
            ->select('tag_name as name')
            ->selectRaw('COUNT(*) as count')
            ->where('taggable_type', Post::class)
            ->join('posts', function($join) {
                $join->on('tagging_tagged.taggable_id', '=', 'posts.id')
                     ->where('tagging_tagged.taggable_type', '=', Post::class);
            });

        if (!Auth::check()) {
            // 未ログインユーザーは公開投稿のタグのみ
            $tagsQuery->where('posts.visibility', Post::VISIBILITY_PUBLIC);
        } else {
            // ログインユーザーは公開投稿と自分の投稿のタグ
            $tagsQuery->where(function($q) {
                $q->where('posts.visibility', Post::VISIBILITY_PUBLIC)
                  ->orWhere('posts.user_id', Auth::id());
            });
        }

        $tags = $tagsQuery->groupBy('tag_name')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(20)
            ->get();

        return view('posts.index', compact('posts', 'tag', 'tags'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'required|string|in:public,private,confidential',
            'parent_id' => 'nullable|exists:posts,id',
            'tags' => 'nullable|string',
            'display_type' => 'required|integer|in:0,1'
        ]);

        $post = new Post($validated);
        $post->user_id = auth()->id();
        $post->save();

        if (!empty($validated['tags'])) {
            $tags = explode(',', $validated['tags']);
            $post->tag($tags);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '投稿が作成されました',
                'redirect' => route('posts.index')
            ]);
        }

        return redirect()->route('posts.index');
    }

    public function show(Post $post): View
    {
        if (!$post->isVisibleTo(Auth::user())) {
            abort(403);
        }

        $post->load(['user', 'tagged']);
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);
        $post->load(['tagged']);
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'nullable|string',
            'visibility' => 'required|in:' . implode(',', array_keys(Post::getVisibilityOptions())),
            'display_type' => 'required|integer|in:0,1'
        ]);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'visibility' => $validated['visibility'],
            'display_type' => $validated['display_type']
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
