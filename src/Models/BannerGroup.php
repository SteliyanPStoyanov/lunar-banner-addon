<?php

namespace Lunar\Banner\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Lunar\Base\BaseModel;


/**
 * @property int $id
 * @property string $name
 * @property string $handle
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property array $guarded
 * @property Banner $banners
 */
class BannerGroup extends BaseModel
{

    protected $guarded = [];

    const WELCOME_BANNER_ID = 2;
    const PROMO_BANNER_ID = 6;


    /**
     * @return HasMany
     */
    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }

}
