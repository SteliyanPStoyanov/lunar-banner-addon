<div>
    <div wire:loading
         wire:target="deleteGroup">
        {{ __('adminhub::global.deleting') }}
    </div>

    <div wire:loading.remove
         wire:target="deleteGroup">
        <div class="flex items-center justify-between">
            <div class="w-full">
                <input id="group-name" wire:model.lazy="group.name"
                    @class([
                        'w-full px-3 py-2 bg-transparent border border-dashed border-gray-300 rounded',
                        'border-red-500' => $errors->first('group.name'),
                        'hover:border-gray-400' => !$errors->first('group.name'),
                    ]) />

                <label for="group-name" class="text-sm text-red-500">
                    {{ $errors->first('group.name') }}
                </label>
            </div>

            <div class="ml-4 w-80">
                <div class="flex justify-end w-full space-x-4">
                    <x-hub::button wire:click.prevent="addBanner">
                        {{ __('banner::catalogue.banners.groups.add_banner_btn') }}
                    </x-hub::button>

                    <button type="button"
                            class="text-gray-400 hover:text-red-600"
                            wire:click.prevent="$set('showDeleteConfirm', true)">

                        <x-hub::icon ref="trash"
                                     class="w-4"/>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <x-hub::modal.dialog wire:model="showDeleteConfirm"
                                 form="deleteGroup">
                <x-slot name="title">
                    {{ __('banner::catalogue.banners.groups.delete.strap-line') }}
                </x-slot>

                <x-slot name="content">
                    <div class="space-y-4">
                        <x-hub::alert level="danger">
                            {{ __('banner::catalogue.banners.groups.delete.warning') }}
                        </x-hub::alert>

                        <x-hub::input.group :label="__('banner::catalogue.banners.groups.delete.confirm')"
                                            for="confirmation">
                            <x-hub::input.toggle wire:model="deletionConfirm"
                                                 id="confirmation"/>
                        </x-hub::input.group>
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <x-hub::button type="button"
                                   wire:click.prevent="$set('showDeleteConfirm', false)"
                                   theme="gray">
                        {{ __('adminhub::global.cancel') }}
                    </x-hub::button>

                    <x-hub::button type="submit"
                                   theme="danger"
                                   :disabled="!$deletionConfirm">
                        {{ __('banner::catalogue.banners.groups.delete.btn') }}
                    </x-hub::button>
                </x-slot>
            </x-hub::modal.dialog>

            <x-hub::modal.dialog wire:model="showCreateForm"
                                 form="createBanner">
                <x-slot name="title">
                    {{ __('banner::catalogue.banners.create.root.title') }}
                </x-slot>

                <x-slot name="content">
                    <div class="space-y-4">
                        <x-hub::input.group :label="__('adminhub::inputs.name')"
                                            for="name"
                                            :error="$errors->first('banner.name')"
                                            required="required">
                            <x-hub::input.text wire:model="banner.name"
                                               :error="$errors->first('banner.name')"/>
                        </x-hub::input.group>
                        <x-hub::input.group :label="__('banner::inputs.link')"
                                            for="name"
                                            :error="$errors->first('banner.link')"
                                            required="required">
                            <x-hub::input.text wire:model="banner.link"
                                               :error="$errors->first('banner.link')"/>
                        </x-hub::input.group>
                        <div id="attributes">
                            @include('adminhub::partials.attributes')
                        </div>
                        @include('adminhub::partials.image-manager', [
                            'existing' => $images,
                            'wireModel' => 'imageUploadQueue',
                            'filetypes' => ['image/*']
                        ])
                        @if ($this->slugIsRequired)
                            <x-hub::input.group :label="__('adminhub::inputs.slug.label')"
                                                for="slug"
                                                :error="$errors->first('slug')"
                                                required="required">
                                <x-hub::input.text wire:model.lazy="slug"
                                                   :error="$errors->first('slug')"/>
                            </x-hub::input.group>
                        @endif
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <x-hub::button type="button"
                                   wire:click.prevent="$set('showCreateForm', false)"
                                   theme="gray">
                        {{ __('adminhub::global.cancel') }}
                    </x-hub::button>

                    <x-hub::button type="submit">
                        {{ __('banner::catalogue.banners.create.btn') }}
                    </x-hub::button>
                </x-slot>
            </x-hub::modal.dialog>

            @if ($this->bannerToRemove)
                <x-hub::modal.dialog wire:model="bannerToRemoveId"
                                     form="deleteBanner">
                    <x-slot name="title">
                        {{ __('banner::catalogue.banners.delete.title') }}
                    </x-slot>

                    <x-slot name="content">
                        <p>{{ __('banner::catalogue.banners.delete.root.warning') }}</p>

                    </x-slot>

                    <x-slot name="footer">
                        <div class="flex justify-between">
                            <x-hub::button type="button"
                                           wire:click.prevent="$set('bannerToRemoveId', null)"
                                           theme="gray">
                                {{ __('adminhub::global.cancel') }}
                            </x-hub::button>

                            <x-hub::button type="submit"
                                           theme="danger">
                                {{ __('banner::catalogue.banners.delete.btn') }}
                            </x-hub::button>
                        </div>
                    </x-slot>
                </x-hub::modal.dialog>
            @endif
            <div class="mt-4 space-y-2">
                @livewire(
                'components.banner.tree',
                [
                'nodes' => $tree,
                'sortGroup' => 'root',
                'owner' => $group,
                ],
                key('tree-root'),
                )
            </div>
        </div>
    </div>
</div>
