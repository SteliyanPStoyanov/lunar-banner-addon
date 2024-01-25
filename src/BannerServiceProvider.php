<?php

namespace Lunar\Banner;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Lunar\Banner\Http\Livewire\Components\Banner\BannerGroupShow;
use Lunar\Banner\Http\Livewire\Components\Banner\BannerTree;
use Lunar\Banner\Http\Livewire\Components\Banner\SideMenu;
use Lunar\Base\AttributeManifestInterface;
use Lunar\Hub\Auth\Manifest;
use Lunar\Hub\Auth\Permission;
use Lunar\Hub\Facades\Menu;
use Lunar\Banner\Models\Banner;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BannerServiceProvider extends ServiceProvider
{

    public function register()
    {

    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function boot(): void
    {

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'banner');

        $manifestAttribute = app(AttributeManifestInterface::class);
        $manifestAttribute->addType(Banner::class);

        $this->app->booted(function () {
            $manifest = $this->app->get(Manifest::class);
            $manifest->addPermission(function (Permission $permission) {
                $permission->name = __('banner::global.manage.banner.title');
                $permission->handle = 'manage-banner'; // or 'group:handle to group permissions
                $permission->description = __('banner::global.manage.banner.description');
            });
        });


        $slot = Menu::slot('sidebar');

        $slot->addItem(function ($item) {
            $item
                ->name(__('banner::menu.sidebar.banner'))
                ->handle('hub.banner')
                ->route('hub.banner-groups.index')
                ->icon('photograph');
        });

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'banner');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([__DIR__ . '/../resources/lang' => resource_path('lang/vendor/banner')], 'banner_lang');

        $this->registerLivewireComponents();

    }

    public function registerLivewireComponents(): void
    {
        Livewire::component('components.banner.side-menu', SideMenu::class);
        Livewire::component('components.banner.banner-groups.show', BannerGroupShow::class);
        Livewire::component('components.banner.tree', BannerTree::class);
    }
}
