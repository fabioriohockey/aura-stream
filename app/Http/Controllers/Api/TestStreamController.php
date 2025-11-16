<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestStreamController extends Controller
{
    public function stream(Request $request, $episodeId)
    {
        try {
            $episode = Episode::findOrFail($episodeId);

            // Sem verificação de usuário para teste
            $quality = $request->get('quality', '720p');
            $videoPath = $episode->{"video_path_{$quality}"};

            if (!$videoPath) {
                return response()->json([
                    'success' => false,
                    'message' => "Vídeo {$quality} não encontrado para o episódio {$episodeId}",
                    'available_paths' => [
                        '480p' => $episode->video_path_480p,
                        '720p' => $episode->video_path_720p,
                    ]
                ], 404);
            }

            $fullPath = Storage::disk('public')->path($videoPath);

            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo de vídeo não encontrado no storage.',
                    'path' => $videoPath,
                    'full_path' => $fullPath
                ], 404);
            }

            $fileSize = filesize($fullPath);
            $mimeType = mime_content_type($fullPath);

            // Streaming direto do vídeo
            return response()->file($fullPath, [
                'Content-Type' => 'video/mp4',
                'Content-Length' => $fileSize,
                'Accept-Ranges' => 'bytes',
                'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}