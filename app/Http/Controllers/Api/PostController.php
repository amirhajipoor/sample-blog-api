<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('comments')->get();

        return response()->json($posts, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string', 'max:2500'],
        ]);

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'post' => $post
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $post = Post::with('comments')->findOrFail($id);

        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string', 'max:2500'],
        ]);

        $post = Post::findOrFail($id);

        if ( Gate::allows('update-post', $post) )
        {
            $post->update([
                'title' => $request->title,
                'body' => $request->body,
            ]);
    
            return response()->json([
                'status' => true,
                'post' => $post
            ], 200);
        } else {
            abort(403, 'Unauthorized');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $post = Post::findOrFail($id);

        if ( Gate::allows('delete-post', $post) )
        {
            try {
                $post->delete();

                if ($post)
                {   
                    return response()->json(['status' => true]);
                }
                
            } catch (Exception $e) {
                
                return response()->json([
                    'status' => false,
                    'message' => $e
                ], 500);
    
            }
        }

    }
}
