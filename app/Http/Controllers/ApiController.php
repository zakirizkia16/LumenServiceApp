<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    // 1. INSERT data (Create)
    public function createPost(Request $request)
{
    $post = Post::create([
        'title'       => $request->input('title'),
        'slug'        => strtolower(str_replace(' ', '-', $request->input('title'))),
        'content'     => $request->input('content'),  // ✅ FIX
        'status'      => $request->input('status', 'draft'),
        'user_id'     => $request->input('user_id'),
        'category_id' => $request->input('category_id'),
    ]);

    return response()->json([
        'message' => 'Post berhasil dibuat',
        'data'    => $post
    ], 201);
}

    // 3. READ post by ID + relasi
    public function getPostById($id)
    {
        $post = Post::with(['user', 'category', 'tags', 'comments.user'])
                    ->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        // Increment view count
        $post->increment('view_count');
        $post->refresh();

        return response()->json($post, 200);
    }

    // 4. UPDATE data
    public function updatePost(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $post->update($request->only(['title', 'content', 'status', 'category_id']));

        return response()->json([
            'message' => 'Post berhasil diupdate',
            'data'    => $post
        ], 200);
    }

    // 5. DELETE data
    public function deletePost($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Post berhasil dihapus'], 200);
    }

    // 6. FILTER by status (WHERE)
    public function getPostsByStatus($status)
    {
        $posts = Post::where('status', $status)
                     ->orderBy('id', 'DESC')
                     ->get();

        return response()->json([
            'message' => "Posts dengan status: $status",
            'total'   => $posts->count(),
            'data'    => $posts
        ], 200);
    }

    // 7. SEARCH by keyword (LIKE)
    public function searchPosts(Request $request)
    {
        $keyword = $request->input('q');

        $posts = Post::where('title', 'LIKE', "%{$keyword}%")
                     ->orWhere('content', 'LIKE', "%{$keyword}%")
                     ->orderBy('id', 'DESC')
                     ->get();

        return response()->json([
            'message' => "Hasil pencarian: $keyword",
            'total'   => $posts->count(),
            'data'    => $posts
        ], 200);
    }

    // 8. AGGREGATE - hitung total per kategori
    public function countPostsByCategory()
    {
        $result = Post::select('category_id', DB::raw('COUNT(*) as total'))
                      ->with('category')
                      ->groupBy('category_id')
                      ->get();

        return response()->json($result, 200);
    }

    // 9. SORTING dinamis
    public function getSortedPosts(Request $request)
    {
        $sortBy  = $request->input('sort_by', 'id');
        $orderBy = $request->input('order', 'DESC');

        $posts = Post::orderBy($sortBy, $orderBy)->paginate(10);

        return response()->json($posts, 200);
    }

    // 10. STATISTIK - dashboard
    public function getStatistics()
    {
        return response()->json([
            'total_users'      => User::count(),
            'total_posts'      => Post::count(),
            'total_categories' => Category::count(),
            'total_comments'   => Comment::count(),
            'total_tags'       => Tag::count(),
            'published_posts'  => Post::where('status', 'published')->count(),
            'draft_posts'      => Post::where('status', 'draft')->count(),
            'most_viewed'      => Post::orderBy('view_count', 'DESC')->first(),
        ], 200);
    }
}