<?php

namespace Lunar\Banner\Http\Livewire\Pages\Banner;

use Illuminate\View\View;
use Livewire\Component;
use Livewire\ComponentConcerns\PerformsRedirects;
use Lunar\Banner\Models\BannerGroup;

class BannerGroupsIndex extends Component
{
    use PerformsRedirects;

    public $shouldSkipRender = false;

    public function boot()
    {

        // If we have collection groups in the database, we redirect
        // and load it up we're straight into editing.
        if ($group = (new BannerGroup)->orderBy('name')->first()) {
            $this->redirectRoute('hub.banner-groups.show', [
                'group' => $group->id,
            ]);
        }
    }

    /**
     * Render the livewire component.
     *
     * @return View
     */
    public function render()
    {
        return view('banner::livewire.pages.banner.banner-groups.index')
            ->layout('banner::layouts.banner-groups', [
                'title' => __('banner::catalogue.banners.index.title'),
            ]);
    }
}
