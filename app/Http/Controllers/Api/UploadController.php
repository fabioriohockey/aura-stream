<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dorama;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload poster for dorama
     */
    public function uploadPoster(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'poster' => 'required|image|mimes:jpeg,jpg,png|max:2048', // 2MB max
            'dorama_id' => 'required|exists:doramas,id',
        ], [
            'poster.required' => 'O arquivo de poster é obrigatório',
            'poster.image' => 'O arquivo deve ser uma imagem',
            'poster.mimes' => 'Formato aceito: JPEG, JPG, PNG',
            'poster.max' => 'Tamanho máximo: 2MB',
            'dorama_id.required' => 'ID do dorama é obrigatório',
            'dorama_id.exists' => 'Dorama não encontrado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dorama = Dorama::findOrFail($request->dorama_id);
            $file = $request->file('poster');

            // Delete old poster if exists
            if ($dorama->poster_path) {
                Storage::disk('public')->delete($dorama->poster_path);
            }

            // Generate unique filename
            $filename = 'doramas/' . $dorama->id . '/poster_' . time() . '.' . $file->getClientOriginalExtension();

            // Store file
            $path = $file->storeAs('', $filename, 'public');

            // Update dorama
            $dorama->poster_path = $path;
            $dorama->save();

            return response()->json([
                'success' => true,
                'message' => 'Poster enviado com sucesso!',
                'data' => [
                    'poster_url' => asset('storage/' . $path),
                    'poster_path' => $path,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar poster: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload backdrop for dorama
     */
    public function uploadBackdrop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'backdrop' => 'required|image|mimes:jpeg,jpg,png|max:3072', // 3MB max
            'dorama_id' => 'required|exists:doramas,id',
        ], [
            'backdrop.required' => 'O arquivo de backdrop é obrigatório',
            'backdrop.image' => 'O arquivo deve ser uma imagem',
            'backdrop.mimes' => 'Formato aceito: JPEG, JPG, PNG',
            'backdrop.max' => 'Tamanho máximo: 3MB',
            'dorama_id.required' => 'ID do dorama é obrigatório',
            'dorama_id.exists' => 'Dorama não encontrado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dorama = Dorama::findOrFail($request->dorama_id);
            $file = $request->file('backdrop');

            // Delete old backdrop if exists
            if ($dorama->backdrop_path) {
                Storage::disk('public')->delete($dorama->backdrop_path);
            }

            // Generate unique filename
            $filename = 'doramas/' . $dorama->id . '/backdrop_' . time() . '.' . $file->getClientOriginalExtension();

            // Store file
            $path = $file->storeAs('', $filename, 'public');

            // Update dorama
            $dorama->backdrop_path = $path;
            $dorama->save();

            return response()->json([
                'success' => true,
                'message' => 'Backdrop enviado com sucesso!',
                'data' => [
                    'backdrop_url' => asset('storage/' . $path),
                    'backdrop_path' => $path,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar backdrop: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload episode video (480p)
     */
    public function uploadEpisodeVideo480p(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video' => 'required|file|mimes:mp4,webm,mov|max:1048576', // 1GB max
            'episode_id' => 'required|exists:episodes,id',
        ], [
            'video.required' => 'O arquivo de vídeo é obrigatório',
            'video.file' => 'O arquivo deve ser válido',
            'video.mimes' => 'Formatos aceitos: MP4, WEBM, MOV',
            'video.max' => 'Tamanho máximo: 1GB',
            'episode_id.required' => 'ID do episódio é obrigatório',
            'episode_id.exists' => 'Episódio não encontrado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $episode = Episode::with('dorama')->findOrFail($request->episode_id);
            $file = $request->file('video');

            // Delete old video if exists
            if ($episode->video_path_480p) {
                Storage::disk('public')->delete($episode->video_path_480p);
            }

            // Generate unique filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = 'doramas/' . $episode->dorama_id . '/episodes/' .
                       'ep' . $episode->episode_number . '_480p_' . time() . '.' . $extension;

            // Store file
            $path = $file->storeAs('', $filename, 'public');

            // Get file size in MB
            $fileSizeMb = round($file->getSize() / 1024 / 1024, 2);

            // Update episode
            $episode->video_path_480p = $path;
            $episode->file_size_480p_mb = $fileSizeMb;
            $episode->video_format = $extension;
            $episode->save();

            return response()->json([
                'success' => true,
                'message' => 'Vídeo 480p enviado com sucesso!',
                'data' => [
                    'video_path' => $path,
                    'video_url' => asset('storage/' . $path),
                    'file_size_mb' => $fileSizeMb,
                    'video_format' => $extension,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar vídeo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload episode video (720p) - premium only
     */
    public function uploadEpisodeVideo720p(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video' => 'required|file|mimes:mp4,webm,mov|max:2097152', // 2GB max
            'episode_id' => 'required|exists:episodes,id',
        ], [
            'video.required' => 'O arquivo de vídeo é obrigatório',
            'video.file' => 'O arquivo deve ser válido',
            'video.mimes' => 'Formatos aceitos: MP4, WEBM, MOV',
            'video.max' => 'Tamanho máximo: 2GB',
            'episode_id.required' => 'ID do episódio é obrigatório',
            'episode_id.exists' => 'Episódio não encontrado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $episode = Episode::with('dorama')->findOrFail($request->episode_id);
            $file = $request->file('video');

            // Delete old video if exists
            if ($episode->video_path_720p) {
                Storage::disk('public')->delete($episode->video_path_720p);
            }

            // Generate unique filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = 'doramas/' . $episode->dorama_id . '/episodes/' .
                       'ep' . $episode->episode_number . '_720p_' . time() . '.' . $extension;

            // Store file
            $path = $file->storeAs('', $filename, 'public');

            // Get file size in MB
            $fileSizeMb = round($file->getSize() / 1024 / 1024, 2);

            // Update episode
            $episode->video_path_720p = $path;
            $episode->file_size_720p_mb = $fileSizeMb;
            $episode->save();

            return response()->json([
                'success' => true,
                'message' => 'Vídeo 720p enviado com sucesso!',
                'data' => [
                    'video_path' => $path,
                    'video_url' => asset('storage/' . $path),
                    'file_size_mb' => $fileSizeMb,
                    'video_format' => $extension,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar vídeo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload episode thumbnail
     */
    public function uploadEpisodeThumbnail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'thumbnail' => 'required|image|mimes:jpeg,jpg,png|max:1024', // 1MB max
            'episode_id' => 'required|exists:episodes,id',
        ], [
            'thumbnail.required' => 'O arquivo de thumbnail é obrigatório',
            'thumbnail.image' => 'O arquivo deve ser uma imagem',
            'thumbnail.mimes' => 'Formato aceito: JPEG, JPG, PNG',
            'thumbnail.max' => 'Tamanho máximo: 1MB',
            'episode_id.required' => 'ID do episódio é obrigatório',
            'episode_id.exists' => 'Episódio não encontrado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $episode = Episode::with('dorama')->findOrFail($request->episode_id);
            $file = $request->file('thumbnail');

            // Delete old thumbnail if exists
            if ($episode->thumbnail_path) {
                Storage::disk('public')->delete($episode->thumbnail_path);
            }

            // Generate unique filename
            $filename = 'doramas/' . $episode->dorama_id . '/episodes/' .
                       'thumb_ep' . $episode->episode_number . '_' . time() . '.' .
                       $file->getClientOriginalExtension();

            // Store file
            $path = $file->storeAs('', $filename, 'public');

            // Update episode
            $episode->thumbnail_path = $path;
            $episode->save();

            return response()->json([
                'success' => true,
                'message' => 'Thumbnail enviada com sucesso!',
                'data' => [
                    'thumbnail_url' => asset('storage/' . $path),
                    'thumbnail_path' => $path,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar thumbnail: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload subtitles file
     */
    public function uploadSubtitles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subtitles' => 'required|file|mimes:vtt,srt|max:1024', // 1MB max
            'episode_id' => 'required|exists:episodes,id',
        ], [
            'subtitles.required' => 'O arquivo de legendas é obrigatório',
            'subtitles.file' => 'O arquivo deve ser válido',
            'subtitles.mimes' => 'Formatos aceitos: VTT, SRT',
            'subtitles.max' => 'Tamanho máximo: 1MB',
            'episode_id.required' => 'ID do episódio é obrigatório',
            'episode_id.exists' => 'Episódio não encontrado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $episode = Episode::with('dorama')->findOrFail($request->episode_id);
            $file = $request->file('subtitles');

            // Delete old subtitles if exists
            if ($episode->subtitles_path) {
                Storage::disk('public')->delete($episode->subtitles_path);
            }

            // Generate unique filename
            $filename = 'doramas/' . $episode->dorama_id . '/episodes/' .
                       'legendas_ep' . $episode->episode_number . '_' . time() . '.' .
                       $file->getClientOriginalExtension();

            // Store file
            $path = $file->storeAs('', $filename, 'public');

            // Update episode
            $episode->subtitles_path = $path;
            $episode->save();

            return response()->json([
                'success' => true,
                'message' => 'Legendas enviadas com sucesso!',
                'data' => [
                    'subtitles_url' => asset('storage/' . $path),
                    'subtitles_path' => $path,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar legendas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create directory structure for dorama
     */
    public function createDoramaDirectories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dorama_id' => 'required|exists:doramas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $dorama = Dorama::findOrFail($request->dorama_id);

            // Create directories
            $directories = [
                "doramas/{$dorama->id}",
                "doramas/{$dorama->id}/episodes",
            ];

            foreach ($directories as $directory) {
                Storage::disk('public')->makeDirectory($directory);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estrutura de diretórios criada com sucesso!',
                'data' => [
                    'directories_created' => $directories,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar diretórios: ' . $e->getMessage(),
            ], 500);
        }
    }
}