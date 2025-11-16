<?php

namespace App\Filament\Resources\DoramaResource\Pages;

use App\Filament\Resources\DoramaResource;
use Filament\Resources\Pages\Page;

class Settings extends Page
{
    protected static string $resource = DoramaResource::class;

    protected static string $view = 'filament.resources.dorama-resource.pages.settings';
}
