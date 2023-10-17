<?php

namespace Lunar\Banner\Http\Livewire\Pages\Banner;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Banner\Models\BannerGroup;

class BannerGroupShow extends Component
{
    public BannerGroup $group;

    /**
     * Render the livewire component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('banner::livewire.pages.banner.banner-groups.show')
            ->layout('banner::layouts.banner-groups', [
                'title' => __('banner::catalogue.banners.index.title'),
                'group' => $this->group,
            ]);
    }
}
