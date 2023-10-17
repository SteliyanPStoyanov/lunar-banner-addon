<?php

namespace Lunar\Banner\Http\Livewire\Components\Banner;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Banner\Models\Banner;
use Lunar\Banner\Models\BannerGroup;
use Lunar\Banner\Traits\MapsBannerTree;
use Lunar\Facades\DB;
use Lunar\Hub\Http\Livewire\Traits\Notifies;

class BannerTree extends Component
{
    use MapsBannerTree, Notifies;

    /**
     * The nodes for the tree.
     */
    public array $nodes;

    /**
     * @var string|null
     */
    public ?string $sortGroup = null;

    /**
     * The collection group.
     *
     * @var BannerGroup
     */
    public BannerGroup $owner;

    /**
     * @var string[]
     */
    protected $listeners = [
        'refreshTree',
        'bannerChanged'
    ];

    /**
     * Sort the collections.
     *
     * @param array $payload
     * @return void
     */
    public function sort(array $payload): void
    {
        DB::transaction(function () use ($payload) {
            $ids = collect($payload['items'])->pluck('id')->toArray();

            $objectIdPositions = array_flip($ids);

            $models = (new Banner)->findMany($ids)
                ->sortBy(function ($model) use ($objectIdPositions) {
                    return $objectIdPositions[$model->getKey()];
                })->values();

            Banner::rebuildSubtree(
                $models->first()->parent,
                $models->map(fn($model) => ['id' => $model->id])->toArray()
            );

            $this->nodes = $this->mapCollections($models);
        });

        $this->notify(
            __('banner::notifications.banner.reordered')
        );
    }


    /**
     * Remove a collection.
     *
     * @param string $nodeId
     * @return void
     */
    public function removeBanner(string $nodeId)
    {
        $this->emit('removeBanner', $nodeId);
    }

    /**
     * Refresh the tree with a new set of nodes.
     *
     * @return void
     */
    public function refreshTree(array $nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * Handle when collection state changes.
     *
     * @return void
     */
    public function bannerChanged()
    {
        $this->nodes = $this->mapCollections(
            Banner::inGroup($this->owner->id)->defaultOrder()->get()
        );
    }

    /**
     * @param int $nodeId
     * @return void
     */
    public function editBanner(int $nodeId)
    {
        $this->emit('editBanner', $nodeId);
    }

    /**
     * Render the livewire component.
     *
     * @return View
     */
    public function render()
    {
        return view('banner::livewire.components.banner.banner-tree')
            ->layout('adminhub::layouts.app');
    }
}
