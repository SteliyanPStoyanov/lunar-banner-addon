<?php

use Illuminate\Support\Facades\Route;
use Lunar\Banner\Http\Livewire\Pages\Banner\BannerGroupShow;
use Lunar\Banner\Http\Livewire\Pages\Banner\BannerGroupsIndex;
use Lunar\Hub\Http\Middleware\Authenticate;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'prefix' => config('lunar-hub.system.path', 'hub'),
    'middleware' => config('lunar-hub.system.middleware', ['web']),
], function () {
    Route::group([
        'middleware' => [
            Authenticate::class,
        ],
    ], function () {
        Route::group([
            'prefix' => 'banner-groups',
            'middleware' => 'can:manage-banner',
        ], function () {
            Route::get('/', BannerGroupsIndex::class)->name('hub.banner-groups.index');
            Route::group([
                'prefix' => '{group}',
            ], function () {
                Route::get('/', BannerGroupShow::class)->name('hub.banner-groups.show');
            });
        });
    });
});

