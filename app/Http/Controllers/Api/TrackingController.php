<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Dorama;
use App\Models\Episode;
use App\Models\UserWatchProgress;
use App\Models\UserFavorite;
use App\Models\UserHighlight;
use Carbon\Carbon;

class TrackingController extends Controller
{
    // Registrar visualização de episódio
    public function trackView(Request $request)
    {
        $request->validate([
            'dorama_id' => 'required|integer|exists:doramas,id',
            'episode_id' => 'required|integer|exists:episodes,id',
            'watch_time' => 'nullable|integer|min:0'
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();
        $doramaId = $request->dorama_id;
        $episodeId = $request->episode_id;
        $watchTime = $request->watch_time ?? 0;

        // Registrar visualização
        UserWatchProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'dorama_id' => $doramaId,
                'episode_id' => $episodeId,
                'date' => Carbon::today()->toDateString()
            ],
            [
                'total_seconds' => \DB::raw("GREATEST(total_seconds, {$watchTime})"),
                'last_position' => $watchTime,
                'is_completed' => $watchTime >= 300 ? true : false, // Considerar completo após 5 minutos
                'views_count' => \DB::raw('views_count + 1')
            ]
        );

        // Incrementar visualizações totais do episódio
        Episode::where('id', $episodeId)->increment('views_count');

        return response()->json(['success' => true]);
    }

    // Registrar progresso detalhado
    public function updateProgress(Request $request)
    {
        $request->validate([
            'dorama_id' => 'required|integer|exists:doramas,id',
            'episode_id' => 'required|integer|exists:episodes,id',
            'current_time' => 'required|integer|min:0'
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();
        $doramaId = $request->dorama_id;
        $episodeId = $request->episode_id;
        $currentTime = $request->current_time;

        UserWatchProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'dorama_id' => $doramaId,
                'episode_id' => $episodeId,
                'date' => Carbon::today()->toDateString()
            ],
            [
                'total_seconds' => \DB::raw("GREATEST(total_seconds, {$currentTime})"),
                'last_position' => $currentTime,
                'is_completed' => $currentTime >= 300 ? true : false, // 5 minutos = completo
                'views_count' => \DB::raw("CASE WHEN views_count = 0 THEN views_count + 1 ELSE views_count END")
            ]
        );

        return response()->json(['success' => true]);
    }

    // Obter progresso do usuário
    public function getProgress(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();
        $doramaId = $request->dorama_id ?? null;

        $query = UserWatchProgress::with(['dorama', 'episode'])
            ->where('user_id', $user->id);

        if ($doramaId) {
            $query->where('dorama_id', $doramaId);
        }

        $progress = $query->orderBy('updated_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($item) {
                return [
                    'dorama_id' => $item->dorama_id,
                    'episode_id' => $item->episode_id,
                    'episode_number' => $item->episode->episode_number,
                    'total_seconds' => $item->total_seconds,
                    'last_position' => $item->last_position,
                    'is_completed' => $item->is_completed,
                    'views_count' => $item->views_count,
                    'date' => $item->date,
                    'updated_at' => $item->updated_at,
                    'dorama' => [
                        'title' => $item->dorama->title,
                        'poster' => $item->dorama->poster_url,
                        'duration_minutes' => $item->dorama->duration_minutes
                    ],
                    'episode' => [
                        'title' => $item->episode->title,
                        'duration_seconds' => $item->episode->duration_seconds,
                        'formatted_duration' => $item->episode->formatted_duration
                    ]
                ];
            });

        return response()->json(['success' => true, 'data' => $progress]);
    }

    // Favoritar/desfavoritar dorama
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'dorama_id' => 'required|integer|exists:doramas,id'
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();
        $doramaId = $request->dorama_id;

        $favorite = UserFavorite::where('user_id', $user->id)
            ->where('dorama_id', $doramaId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['success' => true, 'is_favorited' => false]);
        } else {
            UserFavorite::create([
                'user_id' => $user->id,
                'dorama_id' => $doramaId
            ]);
            return response()->json(['success' => true, 'is_favorited' => true]);
        }
    }

    // Obter favoritos do usuário
    public function getFavorites()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();

        $favorites = UserFavorite::with(['dorama'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $dorama = $item->dorama;
                return [
                    'id' => $dorama->id,
                    'title' => $dorama->title,
                    'poster' => $dorama->poster_url,
                    'rating' => (float) $dorama->rating,
                    'year' => $dorama->year,
                    'episodes_total' => $dorama->episodes_total,
                    'synopsis' => $dorama->description,
                    'genres' => $dorama->genres,
                    'is_favorited' => true
                ];
            });

        return response()->json(['success' => true, 'data' => $favorites]);
    }

    // Destacar/desatacar dorama
    public function toggleHighlight(Request $request)
    {
        $request->validate([
            'dorama_id' => 'required|integer|exists:doramas,id'
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();
        $doramaId = $request->dorama_id;

        $highlight = UserHighlight::where('user_id', $user->id)
            ->where('dorama_id', $doramaId)
            ->first();

        if ($highlight) {
            $highlight->delete();
            return response()->json(['success' => true, 'is_highlighted' => false]);
        } else {
            UserHighlight::create([
                'user_id' => $user->id,
                'dorama_id' => $doramaId
            ]);
            return response()->json(['success' => true, 'is_highlighted' => true]);
        }
    }

    // Obter destaques do usuário
    public function getHighlights()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();

        $highlights = UserHighlight::with(['dorama'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($item) {
                $dorama = $item->dorama;
                return [
                    'id' => $dorama->id,
                    'title' => $dorama->title,
                    'poster' => $dorama->poster_url,
                    'backdrop' => $dorama->backdrop_url,
                    'rating' => (float) $dorama->rating,
                    'year' => $dorama->year,
                    'episodes_total' => $dorama->episodes_total,
                    'synopsis' => $dorama->description,
                    'genres' => $dorama->genres,
                    'is_highlighted' => true,
                    'created_at' => $item->created_at
                ];
            });

        return response()->json(['success' => true, 'data' => $highlights]);
    }

    // Estatísticas do usuário
    public function getStats()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();

        $stats = [
            'total_episodes_watched' => UserWatchProgress::where('user_id', $user->id)->distinct('episode_id')->count(),
            'total_minutes_watched' => UserWatchProgress::where('user_id', $user->id)->sum('total_seconds') / 60,
            'episodes_completed' => UserWatchProgress::where('user_id', $user->id)->where('is_completed', true)->count(),
            'favorites_count' => UserFavorite::where('user_id', $user->id)->count(),
            'highlights_count' => UserHighlight::where('user_id', $user->id)->count(),
            'recent_activity' => UserWatchProgress::with(['dorama', 'episode'])
                ->where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'dorama_title' => $item->dorama->title,
                        'episode_title' => $item->episode->title,
                        'episode_number' => $item->episode->episode_number,
                        'last_position' => $item->last_position,
                        'updated_at' => $item->updated_at
                    ];
                })
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }
}