<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    /**
     * Stream episode video - requires authentication
     */
    public function stream(Request $request, $episodeId, $quality = '480p')
    {
        try {
            $episode = Episode::with('dorama')->findOrFail($episodeId);
            $user = $request->user();

            // Check if user can watch this episode
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você precisa estar logado para assistir este episódio.',
                    'action' => 'login'
                ], 403);
            }

            $reason = $episode->canUserWatchReason($user);
            if ($reason) {
                $messages = [
                    'login_required' => ['message' => 'Você precisa estar logado para assistir este episódio.', 'action' => 'login'],
                    'premium_required' => ['message' => 'Apenas assinantes premium podem assistir este episódio.', 'action' => 'upgrade'],
                    'daily_limit_reached' => ['message' => 'Você atingiu seu limite diário de visualizações.', 'action' => 'upgrade'],
                ];

                $error = $messages[$reason] ?? ['message' => 'Você não pode assistir este episódio.', 'action' => 'none'];
                return response()->json([
                    'success' => false,
                    'message' => $error['message'],
                    'action' => $error['action']
                ], 403);
            }

            // Check if video exists and user has permission for quality
            $videoPath = $episode->{"video_path_{$quality}"};
            if (!$videoPath) {
                return response()->json([
                    'success' => false,
                    'message' => "Vídeo não disponível em {$quality}."
                ], 404);
            }

            // Check premium requirement for 720p
            if ($quality === '720p' && $episode->is_premium_only && !$user->isPremium()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Qualidade 720p disponível apenas para assinantes premium.'
                ], 403);
            }

            // Get full path to video file
            $fullPath = storage_path('app/public/' . $videoPath);
            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo de vídeo não encontrado.'
                ], 404);
            }

            // Increment view count
            $episode->incrementViews();

            // Return video file
            return response()->file($fullPath, [
                'Content-Type' => 'video/mp4',
                'Accept-Ranges' => 'bytes',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar vídeo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get video info for player
     */
    public function getVideoInfo(Request $request, $episodeId)
    {
        try {
            $episode = Episode::findOrFail($episodeId);

            return response()->json([
                'success' => true,
                'data' => [
                    'episode' => [
                        'id' => $episode->id,
                        'title' => $episode->title,
                        'duration_seconds' => $episode->duration_seconds,
                        'qualities_available' => [
                            '480p' => !empty($episode->video_path_480p),
                            '720p' => !empty($episode->video_path_720p),
                        ],
                        'stream_urls' => [
                            '480p' => $episode->video_path_480p ? url("/api/stream/{$episode->id}/480p") : null,
                            '720p' => $episode->video_path_720p ? url("/api/stream/{$episode->id}/720p") : null,
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar informações: ' . $e->getMessage()
            ], 500);
        }
    }
}