<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $store = auth()->user()?->store;

        if ($store) {
            $data['store_id'] = $store->id;
        }

        return $data;
    }
}
