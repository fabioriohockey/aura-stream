<?php

namespace App\Filament\Components;

use Closure;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;

class VideoUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->directory('videos')
            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/avi', 'video/mkv'])
            ->maxSize(10240) // 10GB em KB (PHP usa KB para maxSize)
            ->helperText('Formatos aceitos: MP4, WebM, AVI, MKV, OGG. Tamanho máximo: 10GB')
            ->visibility('public')
            ->enableFileNameDownload()
            ->reorderable()
            ->appendFiles()
            ->multiple(false)
            ->openable()
            ->previewable(false)
            ->storeFileNamesIn('original_filename')
            ->saveUploadedFileUsing(function ($file, $state, $set, $get) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $extension;

                $file->storeAs('videos', $filename, 'public');

                // Salvar metadados do vídeo
                $set('original_filename', $originalName);
                $set('file_size', $file->getSize());
                $set('mime_type', $file->getMimeType());

                return 'videos/' . $filename;
            });
    }

    public static function make(string $name): static
    {
        return new static($name);
    }
}