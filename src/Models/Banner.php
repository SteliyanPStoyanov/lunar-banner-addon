<?php

namespace Lunar\Banner\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\NodeTrait;
use Lunar\Banner\BannerMediaConversions;
use Lunar\Banner\WelcomeBannerMediaConversions;
use Lunar\Base\Casts\AsAttributeData;
use Lunar\Base\Traits\HasMacros;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Lunar\Base\Traits\HasTranslations;
use Lunar\Base\BaseModel;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int $banner_group_id
 * @property string $status
 * @property string $name
 * @property string $link
 * @property string $type
 * @property array $attribute_data
 * @property string $sort
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property BannerGroup $group
 */
class Banner extends BaseModel implements SpatieHasMedia
{
    use HasMacros,
        InteractsWithMedia,
        HasTranslations,
        HasUrls,
        NodeTrait,
        Searchable {
        NodeTrait::usesSoftDelete insteadof Searchable;
    }

    public $registerMediaConversionsUsingModelInstance = true;

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attribute_data' => AsAttributeData::class
    ];

    /**
     * @var array
     */
    protected $guarded = [];


    /**
     * Return the product images relation.
     *
     * @return MorphMany
     */
    public function images(): MorphMany
    {
        return $this->media()->where('collection_name', 'images');
    }


    /**
     * Return the group relationship.
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(BannerGroup::class, 'banner_group_id');
    }

    /**
     * @param Builder $builder
     * @param $id
     * @return Builder
     */
    public function scopeInGroup(Builder $builder, $id): Builder
    {
        return $builder->where('banner_group_id', $id);
    }

    /**
     * @param Media|null $media
     * @return void
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        if ($this->banner_group_id === BannerGroup::WELCOME_BANNER_ID) {
            $conversionClasses = [WelcomeBannerMediaConversions::class];
        } else {
             $conversionClasses = [BannerMediaConversions::class];
        }


        foreach ($conversionClasses as $classname) {
            app($classname)->apply($this);
        }

        // Add a conversion that the hub uses...
        $this->addMediaConversion('small')
            ->fit(Manipulations::FIT_FILL, 300, 300)
            ->sharpen(10)
            ->keepOriginalImageFormat();
    }

    /**
     * Relationship for thumbnail.
     */
    public function thumbnail(): MorphOne
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('custom_properties->primary', true);
    }
}
