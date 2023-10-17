<?php

namespace Lunar\Banner\Http\Livewire\Components\Banner;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Banner\Models\BannerGroup;

class SideMenu extends Component
{
    use Notifies;

    /**
     * @var bool
     */
    public bool $showCreateModal = false;

    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var BannerGroup|null
     */
    public ?BannerGroup $currentGroup = null;

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:'.BannerGroup::class.',name',
        ];
    }

    public function createBannerGroup()
    {
        $this->validate();

        $newGroup = (new BannerGroup)->create([
            'name' => $this->name,
            'handle' => Str::slug($this->name),
        ]);

        $redirect = null;

        if ((new BannerGroup)->count() == 1) {
            $redirect = 'hub.banner-groups.show';
        }

        $this->notify('Banner group created', $redirect, [
            'group' => $newGroup,
        ]);

        $this->name = '';
        $this->showCreateModal = false;

        return redirect()->route('hub.banner-groups.show', $newGroup);
    }

    /**
     * @return Collection|\Illuminate\Support\Collection|BannerGroup[]
     */
    public function getBannerGroupsProperty(): Collection|array|\Illuminate\Support\Collection
    {
        return (new BannerGroup)->orderBy('name')->get();
    }

    /**
     * Render the livewire component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('banner::livewire.components.banner.side-menu')
            ->layout('adminhub::layouts.app');
    }
}
