<?php

namespace Lunar\Banner\Traits;

use Illuminate\Support\Collection;

trait MapsBannerTree
{
    /**
     * Map collections so they're ready to be used.
     *
     * @param Collection $collections
     * @return array
     */
    public function mapCollections(Collection $collections): array
    {
        return $collections->map(function ($collection) {
            return [
                'id' => $collection->id,
                'name' => $collection->name,
                'thumbnail' => $collection->thumbnail?->getUrl('small')
            ];
        })->toArray();
    }
}
