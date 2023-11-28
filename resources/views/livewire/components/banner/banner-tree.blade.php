<div class="space-y-2" wire:sort sort.options='{group: "{{ $sortGroup }}", method: "sort"}'>
    @foreach ($nodes as $node)
        <div wire:key="node_{{ $node['id'] }}" sort.item="{{ $sortGroup }}" sort.id="{{ $node['id'] }}" >
            <div class="flex items-center">
                <div wire:loading wire:target="sort">
                    <x-hub::icon ref="refresh" style="solid" class="w-5 mr-2 text-gray-300 rotate-180 animate-spin" />
                </div>
                <div wire:loading.remove wire:target="sort">
                    <div sort.handle class="cursor-grab">
                        <x-hub::icon ref="selector" style="solid" class="mr-2 text-gray-400 hover:text-gray-700" />
                    </div>
                </div>
                <div class="flex items-center justify-between w-full p-3 text-sm bg-white border border-transparent rounded shadow-sm sort-item-element hover:border-gray-300">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center">
                            @if ($node['thumbnail'])
                                <img alt="{{ $node['name'] }}" style="width: 100px" class="rounded" src="{{ $node['thumbnail'] }}" />
                            @else
                                <x-hub::icon ref="photograph" class="w-6 mx-auto text-gray-300" />
                            @endif
                            <div class="ml-2 truncate">{{ $node['name'] }}</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end w-16">
                        <x-hub::dropdown  minimal="minimal">
                            <x-slot name="options">
                                <x-hub::dropdown.link wire:click.prevent="editBanner('{{ $node['id'] }}')" class="flex items-center cursor-pointer justify-between px-4 py-2 text-sm text-gray-700 border-b hover:bg-gray-50">
                                    {{ __('banner::catalogue.banners.groups.node.edit') }}
                                    <x-hub::icon ref="pencil" style="solid" class="w-4" />
                                </x-hub::dropdown.link>
                                <x-hub::dropdown.button wire:click.prevent="removeBanner('{{ $node['id'] }}')">
                                    {{ __('banner::catalogue.banners.groups.node.delete') }}
                                </x-hub::dropdown.button>
                            </x-slot>
                        </x-hub::dropdown>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
