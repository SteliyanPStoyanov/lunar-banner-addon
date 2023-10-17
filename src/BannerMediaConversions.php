<?php

namespace Lunar\Banner;

use Lunar\Base\BaseModel;
use Spatie\Image\Manipulations;

class BannerMediaConversions
{
    public function apply(BaseModel $model)
    {
        $conversions = [
            'large' => [
                'width' => 660,
                'height' => 350,
            ],
            'medium' => [
                'width' => 333,
                'height' => 175,
            ],
        ];

        foreach ($conversions as $key => $conversion) {
            $model->addMediaConversion($key)
                ->fit(
                    Manipulations::FIT_FILL,
                    $conversion['width'],
                    $conversion['height']
                )->keepOriginalImageFormat();
        }
    }
}
