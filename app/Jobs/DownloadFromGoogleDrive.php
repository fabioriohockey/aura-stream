<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class DownloadFromGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutos
    public $tries = 3;

    public function __construct(
        public string $driveUrl,
        public string $savePath,
        public int $episodeId,
        public string $quality
    ) {}

    public function handle(): void
    {
        try {
            // Extrair ID do Google Drive
            preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $this->driveUrl, $matches);

            if (!isset($matches[1])) {
                throw new Exception('URL do Google Drive inválida');
            }

            $driveId = $matches[1];
            $fullPath = storage_path('app/' . $this->savePath);

            // Garantir que o diretório existe
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Usar gdown para baixar o arquivo (sintaxe corrigida)
            $directory = dirname($fullPath);
            $filename = basename($fullPath);
            $command = "cd \"{$directory}\" && gdown \"https://drive.google.com/uc?id={$driveId}\" -O \"{$filename}\" --fuzzy --continue";
            Log::info("Executando comando: {$command}");

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new Exception('Falha ao baixar arquivo com gdown: ' . implode("\n", $output));
            }

            // Verificar se o arquivo foi baixado
            if (!file_exists($fullPath)) {
                throw new Exception('Arquivo não encontrado após download');
            }

            // Atualizar o episode com o caminho do arquivo
            $episode = \App\Models\Episode::find($this->episodeId);
            if ($episode) {
                // Corrigir nome dos campos conforme o banco
                if ($this->quality === '480') {
                    $episode->video_path_480p = $this->savePath;
                    $fileSize = filesize($fullPath) / (1024 * 1024); // Converter para MB
                    $episode->file_size_480p_mb = round($fileSize, 2);
                } elseif ($this->quality === '720') {
                    $episode->video_path_720p = $this->savePath;
                    $fileSize = filesize($fullPath) / (1024 * 1024); // Converter para MB
                    $episode->file_size_720p_mb = round($fileSize, 2);
                }

                $episode->save();
            }

            Log::info("Download concluído: {$this->savePath}");

        } catch (Exception $e) {
            Log::error("Erro no download do Google Drive: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(Exception $exception): void
    {
        Log::error("Job de download falhou: " . $exception->getMessage());
    }
}