<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Dorama;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DoramaController extends Controller
{
    /**
     * Test method for debugging
     */
    public function testMethod(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Controller funcionando!',
            'timestamp' => now()->toISOString(),
            'request_method' => $request->method(),
            'request_path' => $request->path()
        ]);
    }

    /**
     * Test method for building query step by step
     */
    public function testQuery(Request $request)
    {
        try {
            // Step 1: Try basic query without scopes
            $doramas = Dorama::limit(1)->get();

            return response()->json([
                'success' => true,
                'message' => 'Query básica funcionou!',
                'count' => $doramas->count(),
                'first_dorama' => $doramas->first() ? $doramas->first()->title : 'Nenhum dorama encontrado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na query: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test method with active scope and relationships
     */
    public function testActiveQuery(Request $request)
    {
        try {
            // Step 2: Try with active() scope
            $doramas = Dorama::active()->limit(1)->get();

            return response()->json([
                'success' => true,
                'message' => 'Query com scope active() funcionou!',
                'count' => $doramas->count(),
                'first_dorama' => $doramas->first() ? $doramas->first()->title : 'Nenhum dorama encontrado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na query com active(): ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test method with relationships (the problematic part)
     */
    public function testRelationshipsQuery(Request $request)
    {
        try {
            // Step 3: Try with relationships like in index()
            $doramas = Dorama::active()->with(['categories', 'activeEpisodes'])->limit(1)->get();

            return response()->json([
                'success' => true,
                'message' => 'Query com relacionamentos funcionou!',
                'count' => $doramas->count(),
                'first_dorama' => $doramas->first() ? $doramas->first()->title : 'Nenhum dorama encontrado',
                'categories_count' => $doramas->first() ? $doramas->first()->categories->count() : 0,
                'episodes_count' => $doramas->first() ? $doramas->first()->activeEpisodes->count() : 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na query com relacionamentos: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test method with pagination (like the real index method)
     */
    public function testPaginationQuery(Request $request)
    {
        try {
            // Step 4: Try with pagination like in index()
            $query = Dorama::active()->with(['categories', 'activeEpisodes']);
            $doramas = $query->paginate(12);

            return response()->json([
                'success' => true,
                'message' => 'Query com paginação funcionou!',
                'count' => $doramas->count(),
                'total' => $doramas->total(),
                'first_dorama' => $doramas->first() ? $doramas->first()->title : 'Nenhum dorama encontrado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na query com paginação: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test method exactly like index() but simplified response
     */
    public function testExactIndex(Request $request)
    {
        // Add validators like in index()
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $query = Dorama::active()->with(['categories', 'activeEpisodes']);
            $perPage = $request->get('per_page', 12);
            $doramas = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'doramas' => $doramas->getCollection()->map(function ($dorama) {
                        return [
                            'id' => $dorama->id,
                            'title' => $dorama->title,
                            'slug' => $dorama->slug,
                            'poster_url' => $dorama->poster_url,
                            'rating' => $dorama->rating,
                            'year' => $dorama->year,
                            'episodes_total' => $dorama->episodes_total,
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get all doramas with pagination
     */
    public function index(Request $request)
    {
        return $this->testExactIndex($request);
    }

    /**
     * Get specific dorama details
     */
    public function show(Request $request, $id)
    {
        try {
            $dorama = Dorama::with(['categories', 'activeEpisodes'])
                ->where('id', $id)
                ->orWhere('slug', $id)
                ->active()
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'dorama' => [
                        'id' => $dorama->id,
                        'title' => $dorama->title,
                        'slug' => $dorama->slug,
                        'description' => $dorama->description,
                        'synopsis' => $dorama->synopsis,
                        'country' => $dorama->country,
                        'country_flag' => $dorama->country_flag,
                        'year' => $dorama->year,
                        'episodes_total' => $dorama->episodes_total,
                        'duration_minutes' => $dorama->duration_minutes,
                        'formatted_duration' => $dorama->formatted_duration,
                        'poster_url' => $dorama->poster_url,
                        'backdrop_url' => $dorama->backdrop_url,
                        'trailer_url' => $dorama->trailer_url,
                        'rating' => $dorama->rating,
                        'status' => $dorama->status,
                        'status_label' => $dorama->status_label,
                        'views_count' => $dorama->views_count,
                        'is_featured' => $dorama->is_featured,
                        'is_airing' => $dorama->isAiring(),
                        'release_date' => $dorama->release_date?->format('Y-m-d'),
                        'language' => $dorama->language,
                        'genres' => $dorama->genres,
                        'imdb_id' => $dorama->imdb_id,
                        'categories' => $dorama->categories->map(function ($category) {
                            return [
                                'id' => $category->id,
                                'name' => $category->name,
                                'slug' => $category->slug,
                                'color' => $category->color,
                            ];
                        }),
                        'episodes' => $dorama->activeEpisodes->map(function ($episode) {
                            $user = null; // Public route, no authenticated user
                            return [
                                'id' => $episode->id,
                                'episode_number' => $episode->episode_number,
                                'title' => $episode->title,
                                'description' => $episode->description,
                                'duration_seconds' => $episode->duration_seconds,
                                'formatted_duration' => $episode->formatted_duration,
                                'thumbnail_url' => $episode->thumbnail_url,
                                'subtitles_url' => $episode->subtitles_url,
                                'views_count' => $episode->views_count,
                                'air_date' => $episode->air_date?->toISOString(),
                                'is_premium_only' => $episode->is_premium_only,
                                'can_watch' => $user ? $episode->canUserWatch($user) : false, // Require login to watch
                                'can_watch_reason' => $user ? $episode->canUserWatchReason($user) : 'login_required', // Require login to watch
                                'can_watch_message' => (function () use ($episode, $user) {
                                    $reason = $user ? $episode->canUserWatchReason($user) : 'login_required'; // Require login to watch
                                    switch ($reason) {
                                        case 'login_required':
                                            return 'Você precisa entrar para assistir.';
                                        case 'episode_inactive':
                                            return 'Esse episódio está indisponível no momento.';
                                        case 'premium_required':
                                            return 'Apenas assinantes premium podem assistir. Faça upgrade.';
                                        case 'daily_limit_reached':
                                            return 'Você atingiu o limite diário de episódios grátis. Faça upgrade.';
                                        default:
                                            return null;
                                    }
                                })(),
                                'qualities_available' => [
                                    '480p' => !empty($episode->video_path_480p) && ($user ? !$episode->is_premium_only : false),
                                    '720p' => !empty($episode->video_path_720p) && ($user ? $user->isPremium() : false),
                                ],
                            ];
                        }),
                    ]
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dorama não encontrado.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dorama: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured doramas
     */
    public function featured(Request $request)
    {
        try {
            $limit = min($request->get('limit', 10), 20); // Max 20 items

            $doramas = Dorama::active()
                ->featured()
                ->with(['categories'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'featured_doramas' => $doramas->map(function ($dorama) {
                        return [
                            'id' => $dorama->id,
                            'title' => $dorama->title,
                            'slug' => $dorama->slug,
                            'description' => $dorama->description,
                            'poster_url' => $dorama->poster_url,
                            'backdrop_url' => $dorama->backdrop_url,
                            'rating' => $dorama->rating,
                            'country' => $dorama->country,
                            'country_flag' => $dorama->country_flag,
                            'year' => $dorama->year,
                            'episodes_total' => $dorama->episodes_total,
                            'is_airing' => $dorama->isAiring(),
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar destaques: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular doramas
     */
    public function popular(Request $request)
    {
        try {
            $limit = min($request->get('limit', 10), 20);
            $timeframe = $request->get('timeframe', 'week'); // week, month, all_time

            $query = Dorama::active()->with(['categories'])->popular();

            // Apply timeframe filter
            if ($timeframe === 'week') {
                $query->where('created_at', '>=', now()->subWeek());
            } elseif ($timeframe === 'month') {
                $query->where('created_at', '>=', now()->subMonth());
            }

            $doramas = $query->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'popular_doramas' => $doramas->map(function ($dorama) {
                        return [
                            'id' => $dorama->id,
                            'title' => $dorama->title,
                            'slug' => $dorama->slug,
                            'poster_url' => $dorama->poster_url,
                            'rating' => $dorama->rating,
                            'views_count' => $dorama->views_count,
                            'country' => $dorama->country,
                            'country_flag' => $dorama->country_flag,
                            'year' => $dorama->year,
                            'episodes_total' => $dorama->episodes_total,
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar populares: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get latest doramas
     */
    public function latest(Request $request)
    {
        try {
            $limit = min($request->get('limit', 10), 20);

            $doramas = Dorama::active()
                ->with(['categories'])
                ->latest()
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'latest_doramas' => $doramas->map(function ($dorama) {
                        return [
                            'id' => $dorama->id,
                            'title' => $dorama->title,
                            'slug' => $dorama->slug,
                            'poster_url' => $dorama->poster_url,
                            'rating' => $dorama->rating,
                            'country' => $dorama->country,
                            'country_flag' => $dorama->country_flag,
                            'year' => $dorama->year,
                            'episodes_total' => $dorama->episodes_total,
                            'created_at' => $dorama->created_at->toISOString(),
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar lançamentos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search doramas
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|max:100|min:2',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ], [
            'q.required' => 'Termo de busca é obrigatório',
            'q.min' => 'Termo de busca deve ter pelo menos 2 caracteres',
            'q.max' => 'Termo de busca não pode exceder 100 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $perPage = $request->get('per_page', 12);
            $searchTerm = $request->get('q');

            $doramas = Dorama::active()
                ->with(['categories'])
                ->where(function ($query) use ($searchTerm) {
                    $query->where('title', 'like', '%' . $searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $searchTerm . '%')
                          ->orWhere('synopsis', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('views_count', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'search_term' => $searchTerm,
                    'results_count' => $doramas->total(),
                    'doramas' => $doramas->getCollection()->map(function ($dorama) {
                        return [
                            'id' => $dorama->id,
                            'title' => $dorama->title,
                            'slug' => $dorama->slug,
                            'description' => $dorama->description,
                            'poster_url' => $dorama->poster_url,
                            'rating' => $dorama->rating,
                            'country' => $dorama->country,
                            'country_flag' => $dorama->country_flag,
                            'year' => $dorama->year,
                            'episodes_total' => $dorama->episodes_total,
                            'categories' => $dorama->categories->map(function ($category) {
                                return [
                                    'name' => $category->name,
                                    'slug' => $category->slug,
                                ];
                            }),
                        ];
                    }),
                    'pagination' => [
                        'current_page' => $doramas->currentPage(),
                        'per_page' => $doramas->perPage(),
                        'total' => $doramas->total(),
                        'last_page' => $doramas->lastPage(),
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na busca: ' . $e->getMessage(),
            ], 500);
        }
    }
}