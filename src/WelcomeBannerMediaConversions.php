<?php

namespace Lunar\Banner;

use Lunar\Base\BaseModel;
use Spatie\Image\Manipulations;

class WelcomeBannerMediaConversions
{
    /**
     * @param BaseModel $model
     * @return void
     */
    public function apply(BaseModel $model): void
    {
        $conversions = [
            'large' => [
                'width' => 1330,
                'height' => 500,
            ],
            'medium' => [
                'width' => 880,
                'height' => 330,
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
