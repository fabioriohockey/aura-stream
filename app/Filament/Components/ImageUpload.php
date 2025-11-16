<?php

namespace App\Filament\Components;

use Closure;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;

class ImageUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->directory('images')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(10240) // 10MB
            ->helperText('Formatos aceitos: JPEG, PNG, WebP. Tamanho máximo: 10MB')
            ->visibility('public')
            ->enableFileNameDownload()
            ->reorderable()
            ->appendFiles()
            ->multiple(false)
            ->openable()
            ->previewable(true)
            ->image()
            ->imageEditor()
            ->imageEditorMode(2)
            ->imageEditorAspectRatios([
                '16:9' => '16:9',
                '4:3' => '4:3',
                '1:1' => '1:1',
            ])
            ->storeFileNamesIn('original_filename')
            ->saveUploadedFileUsing(function ($file, $state, $set, $get) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $extension;

                $file->storeAs('images', $filename, 'public');

                // Salvar metadados da imagem
                $set('original_filename', $originalName);
                $set('file_size', $file->getSize());
                $set('mime_type', $file->getMimeType());

                return 'images/' . $filename;
            });
    }

    public static function make(string $name): static
    {
        return new static($name);
    }
}