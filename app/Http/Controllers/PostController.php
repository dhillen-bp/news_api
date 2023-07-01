<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        // return response()->json(['data' => $posts]);
        return PostDetailResource::collection($posts->loadMissing(['author:id,username', 'comments:id,post_id,user_id']));
    }

    public function show($id)
    {
        $post = Post::with(['author:id,username,firstname,lastname'])->findOrFail($id);
        // return response()->json(['data' => $posts]);
        return new PostDetailResource($post);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|unique:posts|max:255',
            'news_content'  => 'required',
        ]);

        $fileName = '';

        if ($request->file) {
            $path = $request->file('file')->store('images');
            $fileName = basename($path);

            $request['image'] = $fileName;
        }

        $request['author_id'] = Auth::user()->id;
        $post = Post::create($request->all());

        return new PostDetailResource($post->loadMissing('author:id,username'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'         => 'required|unique:posts|max:255',
            'news_content'  => 'required',
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('author:id,username'));
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return new PostDetailResource($post->loadMissing('author:id,username'));
    }
}
