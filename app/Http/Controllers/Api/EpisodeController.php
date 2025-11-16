<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EpisodeController extends Controller
{
    /**
     * Get episode details
     */
    public function show(Request $request, $id)
    {
        try {
            $episode = Episode::with('dorama.categories')
                ->where('id', $id)
                ->active()
                ->firstOrFail();

            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'episode' => [
                        'id' => $episode->id,
                        'title' => $episode->title,
                        'full_title' => $episode->full_title,
                        'description' => $episode->description,
                        'episode_number' => $episode->episode_number,
                        'duration_seconds' => $episode->duration_seconds,
                        'formatted_duration' => $episode->formatted_duration,
                        'thumbnail_url' => $episode->thumbnail_url,
                        'subtitles_url' => $episode->subtitles_url,
                        'views_count' => $episode->views_count,
                        'air_date' => $episode->air_date?->toISOString(),
                        'is_premium_only' => $episode->is_premium_only,
                        'video_format' => $episode->video_format,
                        'video_codec' => $episode->video_codec,
                        'file_sizes' => [
                            '480p' => $episode->file_size_480p_mb,
                            '720p' => $episode->file_size_720p_mb,
                        ],
                        'dorama' => [
                            'id' => $episode->dorama->id,
                            'title' => $episode->dorama->title,
                            'slug' => $episode->dorama->slug,
                            'poster_url' => $episode->dorama->poster_url,
                            'episodes_total' => $episode->dorama->episodes_total,
                            'categories' => $episode->dorama->categories->map(function ($category) {
                                return [
                                    'name' => $category->name,
                                    'slug' => $category->slug,
                                    'color' => $category->color,
                                ];
                            }),
                        ],
                        'user_info' => [
                            'can_watch' => $user ? $episode->canUserWatch($user) : false,
                            'can_watch_reason' => $user ? $episode->canUserWatchReason($user) : 'login_required',
                            'can_watch_message' => (function () use ($episode, $user) {
                                $reason = $user ? $episode->canUserWatchReason($user) : 'login_required';
                                $message = null;
                                switch ($reason) {
                                    case 'login_required':
                                        $message = 'Você precisa entrar para assistir.';
                                        break;
                                    case 'episode_inactive':
                                        $message = 'Esse episódio está indisponível no momento.';
                                        break;
                                    case 'premium_required':
                                        $message = 'Apenas assinantes premium podem assistir. Faça upgrade.';
                                        break;
                                    case 'daily_limit_reached':
                                        $message = 'Você atingiu o limite diário de episódios grátis. Faça upgrade.';
                                        break;
                                }
                                return $message;
                            })(),
                            'can_watch_reason' => $user ? $episode->canUserWatchReason($user) : 'login_required',
                            'is_premium' => $user ? $user->isPremium() : false,
                            'remaining_episodes_today' => $user ? $user->getRemainingEpisodesToday() : 0,
                        ],
                        'qualities_available' => [
                            '480p' => !empty($episode->video_path_480p),
                            '720p' => !empty($episode->video_path_720p) && ($user ? $user->isPremium() : false),
                        ],
                        'estimated_bandwidth' => [
                            '480p' => $episode->getEstimatedBandwidth('480p'),
                            '720p' => $episode->getEstimatedBandwidth('720p'),
                        ],
                    ]
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Episódio não encontrado.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar episódio: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get episodes by dorama
     */
    public function byDorama(Request $request, $doramaId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'page' => 'sometimes|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:50',
                'quality' => 'sometimes|in:all,480p,720p',
                'type' => 'sometimes|in:all,free,premium',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $query = Episode::where('dorama_id', $doramaId)->active()->orderBy('episode_number', 'asc');
            $user = $request->user();

            // Apply filters
            $quality = $request->get('quality', 'all');
            if ($quality === '480p') {
                $query->whereNotNull('video_path_480p');
            } elseif ($quality === '720p') {
                $query->whereNotNull('video_path_720p');
            }

            $type = $request->get('type', 'all');
            if ($type === 'free') {
                $query->where('is_premium_only', false);
            } elseif ($type === 'premium') {
                $query->where('is_premium_only', true);
            }

            $perPage = $request->get('per_page', 20);
            $episodes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'episodes' => $episodes->getCollection()->map(function ($episode) use ($user) {
                        return [
                            'id' => $episode->id,
                            'episode_number' => $episode->episode_number,
                            'title' => $episode->title,
                            'full_title' => $episode->full_title,
                            'description' => $episode->description,
                            'duration_seconds' => $episode->duration_seconds,
                            'formatted_duration' => $episode->formatted_duration,
                            'thumbnail_url' => $episode->thumbnail_url,
                            'views_count' => $episode->views_count,
                            'air_date' => $episode->air_date?->toISOString(),
                            'is_premium_only' => $episode->is_premium_only,
                            'can_watch' => $user ? $episode->canUserWatch($user) : false,
                            'can_watch_reason' => $user ? $episode->canUserWatchReason($user) : 'login_required',
                            'qualities_available' => [
                                '480p' => !empty($episode->video_path_480p),
                                '720p' => !empty($episode->video_path_720p) && ($user ? $user->isPremium() : false),
                            ],
                            'file_sizes' => [
                                '480p' => $episode->file_size_480p_mb,
                                '720p' => $episode->file_size_720p_mb,
                            ],
                        ];
                    }),
                    'pagination' => [
                        'current_page' => $episodes->currentPage(),
                        'per_page' => $episodes->perPage(),
                        'total' => $episodes->total(),
                        'last_page' => $episodes->lastPage(),
                    ],
                    'filters' => [
                        'quality' => $quality,
                        'type' => $type,
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar episódios: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get next episode
     */
    public function next(Request $request, $currentEpisodeId)
    {
        try {
            $currentEpisode = Episode::findOrFail($currentEpisodeId);
            $user = $request->user();

            $nextEpisode = Episode::where('dorama_id', $currentEpisode->dorama_id)
                ->where('episode_number', '>', $currentEpisode->episode_number)
                ->active()
                ->orderBy('episode_number', 'asc')
                ->first();

            if (!$nextEpisode) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'next_episode' => null,
                        'message' => 'Este é o último episódio disponível.',
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'next_episode' => [
                        'id' => $nextEpisode->id,
                        'episode_number' => $nextEpisode->episode_number,
                        'title' => $nextEpisode->title,
                        'thumbnail_url' => $nextEpisode->thumbnail_url,
                        'duration_seconds' => $nextEpisode->duration_seconds,
                        'formatted_duration' => $nextEpisode->formatted_duration,
                        'is_premium_only' => $nextEpisode->is_premium_only,
                        'can_watch' => $user ? $nextEpisode->canUserWatch($user) : false,
                        'can_watch_reason' => $user ? $nextEpisode->canUserWatchReason($user) : 'login_required',
                    ]
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Episódio não encontrado.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar próximo episódio: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get previous episode
     */
    public function previous(Request $request, $currentEpisodeId)
    {
        try {
            $currentEpisode = Episode::findOrFail($currentEpisodeId);
            $user = $request->user();

            $previousEpisode = Episode::where('dorama_id', $currentEpisode->dorama_id)
                ->where('episode_number', '<', $currentEpisode->episode_number)
                ->active()
                ->orderBy('episode_number', 'desc')
                ->first();

            if (!$previousEpisode) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'previous_episode' => null,
                        'message' => 'Este é o primeiro episódio.',
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'previous_episode' => [
                        'id' => $previousEpisode->id,
                        'episode_number' => $previousEpisode->episode_number,
                        'title' => $previousEpisode->title,
                        'thumbnail_url' => $previousEpisode->thumbnail_url,
                        'duration_seconds' => $previousEpisode->duration_seconds,
                        'formatted_duration' => $previousEpisode->formatted_duration,
                        'is_premium_only' => $previousEpisode->is_premium_only,
                        'can_watch' => $user ? $previousEpisode->canUserWatch($user) : false,
                        'can_watch_reason' => $user ? $previousEpisode->canUserWatchReason($user) : 'login_required',
                    ]
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Episódio não encontrado.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar episódio anterior: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent episodes
     */
    public function recent(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'sometimes|integer|min:1|max:50',
                'type' => 'sometimes|in:all,free,premium',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $limit = min($request->get('limit', 20), 50);
            $type = $request->get('type', 'all');
            $user = $request->user();

            $query = Episode::with('dorama')
                ->active()
                ->latest('air_date')
                ->limit($limit);

            if ($type === 'free') {
                $query->where('is_premium_only', false);
            } elseif ($type === 'premium') {
                $query->where('is_premium_only', true);
            }

            $episodes = $query->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'recent_episodes' => $episodes->map(function ($episode) use ($user) {
                        return [
                            'id' => $episode->id,
                            'episode_number' => $episode->episode_number,
                            'title' => $episode->title,
                            'thumbnail_url' => $episode->thumbnail_url,
                            'duration_seconds' => $episode->duration_seconds,
                            'formatted_duration' => $episode->formatted_duration,
                            'air_date' => $episode->air_date?->toISOString(),
                            'views_count' => $episode->views_count,
                            'is_premium_only' => $episode->is_premium_only,
                            'can_watch' => $user ? $episode->canUserWatch($user) : false,
                            'can_watch_reason' => $user ? $episode->canUserWatchReason($user) : 'login_required',
                            'dorama' => [
                                'id' => $episode->dorama->id,
                                'title' => $episode->dorama->title,
                                'slug' => $episode->dorama->slug,
                                'poster_url' => $episode->dorama->poster_url,
                            ],
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar episódios recentes: ' . $e->getMessage(),
            ], 500);
        }
    }
}