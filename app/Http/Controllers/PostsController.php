<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index()
    {
        $posts = Post::simplePaginate(12);
        return view('blog-listing')->with('posts', $posts);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $slug, Post $post)
    {
        return view('blog-reader')->with('post', $post);
    }

    public function showTag(string $tag)
    {
        $posts = Post::where('tags', 'like', "%$tag%")->simplePaginate(12);
        return view('blog-listing')->with('posts', $posts);
    }

    public function edit(Post $post)
    {
        //
    }

    public function update(Request $request, Post $post)
    {
        //
    }

    public function destroy(Post $post)
    {
        //
    }
}