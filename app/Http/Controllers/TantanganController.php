<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class TantanganController extends Controller
{
    // 1. Filter data berdasarkan kriteria tertentu (where)
    public function getPublished()
    {
        $posts = Post::where('status', 'published')->get();
        return response()->json($posts, 200);
    }

    // 2. Ambil 1 record pertama (first)
    public function getLatest()
    {
        $post = Post::orderBy('created_at', 'desc')->first();
        return response()->json($post, 200);
    }

    // 3. Menghitung jumlah record (count)
    public function getTotalPosts()
    {
        $total = Post::count();
        return response()->json(['total_posts' => $total], 200);
    }

    // 4. Pencarian teks (like)
    public function search(Request $request)
    {
        $keyword = $request->query('q');
        $posts = Post::where('title', 'LIKE', '%' . $keyword . '%')->get();
        return response()->json($posts, 200);
    }
}