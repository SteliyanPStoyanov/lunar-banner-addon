@extends('adminhub::layouts.app')

@section('menu')
    <x-hub::layout.side-menu>
        @livewire('components.banner.side-menu', [
            'currentGroup' => $group ?? null,
        ])
    </x-hub::layout.side-menu>
@stop

@section('main')
    <div class="space-y-6">
        <div x-data="{ showGroupSlideOver: false }">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold md:text-xl">
                    {{ __('banner::catalogue.banners.index.title') }}
                </h1>

                <div class="block lg:hidden">
                    <x-hub::button type="button"
                                   theme="gray"
                                   x-on:click="showGroupSlideOver = !showGroupSlideOver">
                        {{ __('View Banner Groups') }}
                    </x-hub::button>
                </div>
            </div>

            <x-hub::slideover-simple target="showGroupSlideOver">
                @livewire('components.banner.side-menu', [
                    'currentGroup' => $group ?? null,
                ])
            </x-hub::slideover-simple>
        </div>

        {{ $slot }}
    </div>
@stop
