<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Dorama;
use App\Models\Episode;
use App\Models\Comment;

class CommentController extends Controller
{
    // List comments
    public function index(Request $request)
    {
        $request->validate([
            'dorama_id' => 'required|integer|exists:doramas,id',
            'episode_id' => 'nullable|integer|exists:episodes,id'
        ]);

        $doramaId = $request->dorama_id;
        $episodeId = $request->episode_id;

        $comments = Comment::with(['user' => function($query) {
                $query->select('id', 'name', 'avatar_url');
            }])
            ->forDorama($doramaId)
            ->forEpisode($episodeId)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'total_pages' => $comments->lastPage(),
                'total_items' => $comments->total(),
                'has_more' => $comments->hasMorePages()
            ]
        ]);
    }

    // Create comment
    public function store(Request $request)
    {
        $request->validate([
            'dorama_id' => 'required|integer|exists:doramas,id',
            'episode_id' => 'nullable|integer|exists:episodes,id',
            'content' => 'required|string|min:1|max:1000'
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();

        $comment = Comment::create([
            'user_id' => $user->id,
            'dorama_id' => $request->dorama_id,
            'episode_id' => $request->episode_id,
            'content' => $request->content,
            'likes_count' => 0
        ]);

        $comment->load(['user' => function($query) {
            $query->select('id', 'name', 'avatar_url');
        }]);

        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }

    // Update comment
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|min:1|max:1000'
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Comentário não encontrado'], 404);
        }

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        $comment->update([
            'content' => $request->content
        ]);

        $comment->load(['user' => function($query) {
            $query->select('id', 'name', 'avatar_url');
        }]);

        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }

    // Delete comment
    public function destroy($id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Comentário não encontrado'], 404);
        }

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentário excluído com sucesso'
        ]);
    }

    // Like comment
    public function like($id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Comentário não encontrado'], 404);
        }

        // Simple increment for now - you can implement proper like system later
        $comment->increment('likes_count');

        return response()->json([
            'success' => true,
            'likes_count' => $comment->fresh()->likes_count
        ]);
    }
}