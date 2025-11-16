<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Dorama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index(Request $request)
    {
        try {
            $categories = Category::active()
                ->ordered()
                ->withCount('doramas')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                            'description' => $category->description,
                            'color' => $category->color,
                            'doramas_count' => $category->doramas_count,
                            'order' => $category->order,
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar categorias: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category details
     */
    public function show(Request $request, $id)
    {
        try {
            $category = Category::where('id', $id)
                ->orWhere('slug', $id)
                ->active()
                ->with(['doramas' => function ($query) {
                    $query->active()->latest();
                }])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'color' => $category->color,
                        'order' => $category->order,
                        'doramas_count' => $category->doramas->count(),
                        'doramas' => $category->doramas->map(function ($dorama) {
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
                                'views_count' => $dorama->views_count,
                                'is_featured' => $dorama->is_featured,
                                'is_airing' => $dorama->isAiring(),
                            ];
                        }),
                    ]
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar categoria: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get doramas by category
     */
    public function doramas(Request $request, $id)
    {
        try {
            $validator = validator($request->all(), [
                'page' => 'sometimes|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:50',
                'sort' => 'sometimes|in:popular,top_rated,latest,a_z,z_a,year_asc,year_desc',
                'year' => 'sometimes|integer|min:2000|max:' . (date('Y') + 1),
                'country' => 'sometimes|string|in:Coreia,Japão,China,Tailândia,Taiwan',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $category = Category::where('id', $id)
                ->orWhere('slug', $id)
                ->active()
                ->firstOrFail();

            $query = $category->doramas()->active();

            // Apply filters
            if ($request->year) {
                $query->where('year', $request->year);
            }

            if ($request->country) {
                $query->where('country', $request->country);
            }

            // Apply sorting
            switch ($request->get('sort', 'latest')) {
                case 'popular':
                    $query->orderBy('views_count', 'desc');
                    break;
                case 'top_rated':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'latest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'a_z':
                    $query->orderBy('title', 'asc');
                    break;
                case 'z_a':
                    $query->orderBy('title', 'desc');
                    break;
                case 'year_asc':
                    $query->orderBy('year', 'asc');
                    break;
                case 'year_desc':
                    $query->orderBy('year', 'desc');
                    break;
            }

            $perPage = $request->get('per_page', 12);
            $doramas = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'color' => $category->color,
                    ],
                    'doramas' => $doramas->getCollection()->map(function ($dorama) {
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
                            'duration_minutes' => $dorama->duration_minutes,
                            'formatted_duration' => $dorama->formatted_duration,
                            'status' => $dorama->status,
                            'status_label' => $dorama->status_label,
                            'views_count' => $dorama->views_count,
                            'is_featured' => $dorama->is_featured,
                            'is_airing' => $dorama->isAiring(),
                            'release_date' => $dorama->release_date?->format('Y-m-d'),
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

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar doramas da categoria: ' . $e->getMessage(),
            ], 500);
        }
    }
}