<?php

namespace Lunar\Banner\Http\Livewire\Components\Banner;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Kalnoy\Nestedset\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Lunar\Banner\Models\Banner;
use Lunar\Banner\Models\BannerGroup;
use Lunar\Banner\Traits\MapsBannerTree;
use Lunar\Facades\DB;
use Lunar\Hub\Http\Livewire\Traits\HasImages;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Hub\Http\Livewire\Traits\WithAttributes;
use Lunar\Models\Attribute;
use Lunar\Models\Language;

class BannerGroupShow extends Component
{
    use MapsBannerTree, Notifies;
    use HasImages;
    use Notifies;
    use WithAttributes;
    use WithFileUploads;

    /**
     * The current collection group.
     */
    public BannerGroup $group;

    /**
     * The new collection we're making.
     *
     * @var Banner
     */
    public Banner $banner;

    /**
     * Show confirmation if we want to delete the group.
     */
    public bool $showDeleteConfirm = false;

    /**
     * Failsafe confirmation in order to delete the collection group.
     */
    public bool $deletionConfirm = false;

    /**
     * The ID of the collection we want to remove.
     *
     * @var int|null
     */
    public ?int $bannerToRemoveId = null;

    /**
     * Whether we should show the create form.
     *
     * @var bool
     */
    public bool $showCreateForm = false;


    /**
     * Search term for searching collections.
     *
     * @var string|null
     */
    public ?string $searchTerm = null;

    public ?string $slug = null;

    public array $tree = [];
    private bool $slugIsRequired = false;
    private  $bannerToRemove = null;

    protected function getListeners()
    {
        return array_merge([
            'addBanner',
            'removeBanner',
            'updatedAttributes',
            'editBanner'
        ], $this->getHasImagesListeners());
    }

    /**
     * Return the validation rules.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'group.name' => 'required|string|max:255|unique:' . BannerGroup::class . ',name,' . $this->group->id,
            'banner.name' => 'required|string|max:255',

            'banner.link' => 'required|string|max:255|url:http,https',
            'deletionConfirm' => 'nullable|boolean',
        ];

        if ($this->slugIsRequired) {
            $rules['slug'] = 'required|string|max:255';
        }

        return array_merge(
            $rules,
            $this->withAttributesValidationRules(),
            $this->hasImagesValidationRules(),
        );
    }


    /**
     * @return void
     */
    public function mount(): void
    {
        $this->banner = new Banner();
        $this->loadTree();
    }

    /**
     * Load the tree.
     *
     * @return void
     */
    public function loadTree(): void
    {

        $this->tree = $this->mapCollections(
            $this->group->banners()->defaultOrder()->get()
        );
    }

    /**
     * Get the collection attribute data.
     *@return null|array
     */
    public function getAttributeDataProperty(): ?array
    {
        return $this->banner->attribute_data;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableAttributesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Attribute::whereAttributeType(Banner::class)->orderBy('position')->get();
    }

    /**
     * @return Banner
     */
    public function getMediaModel(): Banner
    {
        return $this->banner;
    }

    /**
     * Watcher for when the group name is updated.
     *
     * @return void
     * @throws ValidationException
     */
    public function updatedGroupName()
    {
        $this->validateOnly('group.name');
        $this->group->handle = Str::slug($this->group->name);
        $this->group->save();
        $this->notify(__('banner::notifications.banner-groups.updated'));
    }

    /**
     * Watcher for when the show delete confirm is updated.
     *
     * @return void
     */
    public function updatedShowDeleteConfirm()
    {
        $this->deletionConfirm = false;
    }

    /**
     * Called when component is dehydrated.
     *
     * @return void
     */
    public function dehydrate()
    {
        $this->group->unsetRelations();
    }

    /**
     * Add a collection ready for saving.
     *
     * @return void
     */
    public function addBanner()
    {
        $this->showCreateForm = true;
    }

    /**
     * Set the collection id to remove.
     *
     * @param int $nodeId
     * @return void
     */
    public function removeBanner(int $nodeId): void
    {
        $this->bannerToRemoveId = $nodeId;
    }

    /**
     * Delete the collection group.
     *
     * @return void
     */
    public function deleteGroup(): void
    {
        $this->showDeleteConfirm = false;
        DB::transaction(function () {
            foreach ($this->group->banners as $banner) {
                $banner->forceDelete();
            }
            $this->group->forceDelete();
        });

        $this->notify(__('banner::notifications.banner-groups.deleted'), 'hub.banner-groups.index');
    }

    /**
     * Returns whether the slug should be required.
     *
     * @return bool
     */
    public function getSlugIsRequiredProperty()
    {
        return config('lunar.urls.required', false) &&
            !config('lunar.urls.generator');
    }

    /**
     * Handler for when the slug is updated.
     *
     * @param string $value
     * @return void
     */
    public function updatedSlug(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    /**
     * Delete the collection.
     *
     * @return void
     */
    public function deleteBanner(): void
    {
        DB::transaction(function () {
            foreach ($this->bannerToRemove->descendants()->get() as $descendant) {
                $descendant->forceDelete();
            }

            $this->bannerToRemove->forceDelete();
            $this->bannerToRemoveId = null;

            $this->emit('bannerChanged', $this->bannerToRemove->id);

            $this->notify(
                __('banner::notifications.banners.deleted')
            );
        });
    }

    /**
     * Create the new collection.
     *
     * @return void
     */
    public function createBanner(): void
    {

        $this->validate($this->rules(), [
            'banner.name.required' => __('adminhub::validation.generic_required'),
            'banner.link.required' => __('adminhub::validation.generic_required'),
            'banner.link.url' => __('banner::validation.link'),
        ]);

        $data = $this->prepareAttributeData();


        $this->banner->banner_group_id = $this->group->id;
        $this->banner->attribute_data = $data;


        $this->banner->save();

        if ($this->slug) {
            $this->banner->urls()->create([
                'slug' => $this->slug,
                'default' => true,
                'language_id' => Language::getDefault()->id,
            ]);
        }

        $this->updateImages();
        $this->banner = new Banner();
        $this->showCreateForm = false;
        $this->images = [];
        $this->loadTree();

        $this->emit('refreshTree', $this->tree);

        $this->notify(
            __('banner::notifications.banner.added')
        );
    }

    /**
     * Return the collection tree.
     *
     * @return Collection
     */
    public function getBannerTree()
    {
        return $this->group->load('banners')->banners()->defaultOrder()->get()->toTree();
    }

    /**
     * Getter for returning the collection to remove.
     *
     * @return \Lunar\Models\Collection|null
     */
    public function getBannerToRemoveProperty()
    {
        return $this->bannerToRemoveId ?
            (new Banner)->find($this->bannerToRemoveId) :
            null;
    }

    /**
     * @param int $id
     * @return void
     */
    public function editBanner(int $id): void
    {

        $this->showCreateForm = true;
        $this->banner = (new Banner)->find($id);

        $this->images = $this->banner->getMedia('images')->mapWithKeys(function ($media) {
            $key = Str::random();

            return [
                $key => [
                    'id' => $media->id,
                    'sort_key' => $key,
                    'thumbnail' => $media->getFullUrl('small'),
                    'original' => $media->getFullUrl(),
                    'preview' => false,
                    'edit' => false,
                    'caption' => $media->getCustomProperty('caption'),
                    'primary' => $media->getCustomProperty('primary'),
                    'position' => $media->getCustomProperty('position', 1),
                ],
            ];
        })->sortBy('position')->toArray();

    }

    /**
     * @return Language
     */
    public function getDefaultLanguageProperty(): Language
    {
        return Language::getDefault();
    }

    /**
     * Get the default language code.
     *
     * @return Language
     */
    public function getLanguagesProperty()
    {

        return Language::getDefault();
    }

    /**
     * Render the livewire component.
     *
     * @return View
     */
    public function render()
    {

        return view('banner::livewire.components.banner.banner-groups.show')
            ->layout('banner::layouts.banner-groups', [
                'title' => __('adminhub::catalogue.banners.index.title'),
            ]);
    }
}
