<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreBannerService
{
    public const MAX_BANNERS = 3;

    /**
     * Sinkronkan maksimal tiga banner toko dari request.
     */
    public function sync(Store $store, Request $request): void
    {
        for ($index = 1; $index <= self::MAX_BANNERS; $index++) {
            $field = 'banner_' . $index;

            if ($request->boolean('remove_' . $field) && $store->{$field}) {
                $this->deleteFile($store->{$field});
                $store->{$field} = null;
            }

            if ($request->hasFile($field)) {
                if ($store->{$field}) {
                    $this->deleteFile($store->{$field});
                }

                $store->{$field} = $request->file($field)->store('store-banners', 'public');
            }
        }

        $store->save();
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
