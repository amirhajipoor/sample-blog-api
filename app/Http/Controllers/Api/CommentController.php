<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::with('user')->get();

        return response()->json([
            'status' => true,
            'comments' => $comments,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:500'],
        ]);

        $comment = Comment::create([
            'post_id' => $id,
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'comment' => $comment
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $post_id, int $comment_id)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:500'],
        ]);

        $comment = Comment::findOrFail($comment_id);

        if (Gate::allows('update-comment', $comment)) {
            $comment->update([
                'body' => $request->body,
            ]);

            return response()->json([
                'status' => true,
                'comment' => $comment,
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $post_id, int $comment_id)
    {
        Gate::authorize('delete-comment', $comment);

        $comment = Comment::findOrFail($comment_id);
        
        try {
            $comment->delete();

            if ($comment) {
                return response()->json([
                    'status' => true,
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e
            ], 500);
        }
    }
}
