<?php

declare(strict_types=1);

namespace App\Providers;

use App\Orchid\Composers\MainMenuComposer;
use App\Orchid\Composers\SystemMenuComposer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;

class OrchidServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard)
    {
        View::composer('platform::dashboard', MainMenuComposer::class);
        View::composer('platform::systems', SystemMenuComposer::class);

        $dashboard->registerPermissions($this->registerPermissionsSystems());

        $dashboard->registerGlobalSearch([
            //...Models
        ]);
    }

    /**
     * @return ItemPermission
     */
    protected function registerPermissionsSystems(): ItemPermission
    {
        return ItemPermission::group(__('Systems'))
            ->addPermission('platform.systems.roles', __('Roles'))
            ->addPermission('platform.systems.users', __('Users'));
    }

    /**
     *
     */
    public function register()
    {
        Route::domain((string)config('platform.domain'))
            ->prefix(Dashboard::prefix('/'))
            ->middleware(config('platform.middleware.private'))
            ->group(base_path('routes/platform.php'));
    }
}
