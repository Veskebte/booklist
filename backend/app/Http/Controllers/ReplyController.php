<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Like;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($bookId)
    {
        $replies = Reply::where('book_id', $bookId)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($replies);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'content' => 'required',
        ]);

        $reply = Reply::create([
            'user_id' => auth()->id(),
            'book_id' => $request->book_id,
            'content' => $request->content,
        ]);

        return response()->json($reply, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reply = Reply::withCount('likes')->findOrFail($id);

        return response()->json([
            'reply' => $reply,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $reply = Reply::find($id);

        if (!$reply) {
            return response()->json(['message' => 'Komentar tidak ditemukan'], 404);
        }

        if ($reply->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required',
        ]);

        $reply->update([
            'content' => $request->content,
        ]);

        return response()->json($reply);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $reply = Reply::find($id);

        if (!$reply) {
            return response()->json(['message' => 'Komentar tidak ditemukan'], 404);
        }

        if ($reply->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reply->delete();
        return response()->json(['message' => 'Reply deleted successfully']);
    }

    public function like(Request $request, $replyId) {
        $reply = Reply::findOrFail($replyId);
        $user = $request->user();

        if ($user->likes()->where('reply_id', $reply->id)->exists()) {
            return response()->json(['message' => 'Anda telah menyukai komentar ini.'], 400);
        }

        $user->likes()->create(['reply_id' => $reply->id]);

        return response()->json(['message' => 'Liked successfully'], 200);
    }

    public function unlike(Request $request, $replyId) {
        $like = Like::where('user_id', $request->user_id)->where('reply_id', $request->$replyId)->first();

        if ($like) {
            $like->delete();
        }

        return response()->json(['message' => 'Unlike successfully'], 200);
    }
}
