<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoramaController;
use App\Http\Controllers\Api\EpisodeController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\StreamController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware([])->group(function () {
    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Rota de teste simples
    Route::get('/test-simple', function () {
        return response()->json([
            'success' => true,
            'message' => 'API pública funcionando!',
            'timestamp' => now()->toISOString()
        ]);
    });

    // Rota de teste do controller
    Route::get('/test-controller', [DoramaController::class, 'testMethod']);

    // Rota de teste de query
    Route::get('/test-query', [DoramaController::class, 'testQuery']);

    // Rota de teste de query com active scope
    Route::get('/test-active-query', [DoramaController::class, 'testActiveQuery']);

    // Rota de teste de query com relacionamentos
    Route::get('/test-relationships-query', [DoramaController::class, 'testRelationshipsQuery']);

    // Rota de teste de query com paginação
    Route::get('/test-pagination-query', [DoramaController::class, 'testPaginationQuery']);

    // Rota de teste exatamente como index()
    Route::get('/test-exact-index', [DoramaController::class, 'testExactIndex']);

    // Public dorama routes (para a homepage!)
    Route::get('/doramas', [DoramaController::class, 'index']);
    Route::get('/doramas/featured', [DoramaController::class, 'featured']);
    Route::get('/doramas/popular', [DoramaController::class, 'popular']);
    Route::get('/doramas/latest', [DoramaController::class, 'latest']);
    Route::get('/doramas/search', [DoramaController::class, 'search']);
    Route::get('/doramas/{id}', [DoramaController::class, 'show']);

    // Public category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/categories/{id}/doramas', [CategoryController::class, 'doramas']);

    // Public episode routes (para detalhes)
    Route::get('/episodes/{id}', [EpisodeController::class, 'show']);
    Route::get('/episodes/dorama/{doramaId}', [EpisodeController::class, 'byDorama']);
    Route::get('/episodes/recent', [EpisodeController::class, 'recent']);

    // Tracking routes (PRECISA AUTENTICAÇÃO)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/tracking/view', [TrackingController::class, 'trackView']);
        Route::post('/tracking/progress', [TrackingController::class, 'updateProgress']);
        Route::get('/tracking/progress', [TrackingController::class, 'getProgress']);
        Route::post('/tracking/favorites/toggle', [TrackingController::class, 'toggleFavorite']);
        Route::get('/tracking/favorites', [TrackingController::class, 'getFavorites']);
        Route::post('/tracking/highlights/toggle', [TrackingController::class, 'toggleHighlight']);
        Route::get('/tracking/highlights', [TrackingController::class, 'getHighlights']);
        Route::get('/tracking/stats', [TrackingController::class, 'getStats']);
    });

    // Comment routes (PRECISA AUTENTICAÇÃO)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/comments', [CommentController::class, 'index']);
        Route::post('/comments', [CommentController::class, 'store']);
        Route::put('/comments/{id}', [CommentController::class, 'update']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
        Route::post('/comments/{id}/like', [CommentController::class, 'like']);
    });

    // Test streaming route (SEM AUTENTICAÇÃO)
    Route::get('/test-stream/{episodeId}', [StreamController::class, 'stream']);

    // Image serving route (SEM AUTENTICAÇÃO)
    Route::get('/serve-image/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) {
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }
        return response()->file($fullPath);
    })->where('path', '.*');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/auth/profile', [AuthController::class, 'profile']);

        // User routes
        Route::get('/user/profile', [AuthController::class, 'profile']);

        // Streaming routes (REQUEREM AUTENTICAÇÃO)
        Route::get('/stream/{episodeId}', [StreamController::class, 'stream']);
        Route::get('/stream/{episodeId}/{quality}', [StreamController::class, 'stream']);
        Route::get('/stream/{episodeId}/info', [StreamController::class, 'getVideoInfo']);
        Route::post('/stream/{episodeId}/progress', [StreamController::class, 'recordProgress']);
        Route::get('/stream/history', [StreamController::class, 'getWatchHistory']);

        // Episode routes (adicionais que requerem autenticação)
        Route::get('/episodes/{id}/next', [EpisodeController::class, 'next']);
        Route::get('/episodes/{id}/previous', [EpisodeController::class, 'previous']);

        // Upload routes (admin only)
        Route::post('/upload/poster', [UploadController::class, 'uploadPoster']);
        Route::post('/upload/backdrop', [UploadController::class, 'uploadBackdrop']);
        Route::post('/upload/video/480p', [UploadController::class, 'uploadEpisodeVideo480p']);
        Route::post('/upload/video/720p', [UploadController::class, 'uploadEpisodeVideo720p']);
        Route::post('/upload/thumbnail', [UploadController::class, 'uploadEpisodeThumbnail']);
        Route::post('/upload/subtitles', [UploadController::class, 'uploadSubtitles']);
        Route::post('/upload/directories', [UploadController::class, 'createDoramaDirectories']);
    });
});

// Test route
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now()->toISOString()
    ]);
});