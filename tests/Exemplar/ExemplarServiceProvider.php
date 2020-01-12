<?php

declare(strict_types=1);

namespace Orchid\Tests\Exemplar;

use App\Orchid\Composers\MainMenuComposer;
use App\Orchid\Composers\SystemMenuComposer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;

class ExemplarServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard): void
    {
        $this->loadViewsFrom(Dashboard::path('tests/Exemplar/views'), 'exemplar');

        require_once Dashboard::path('routes/breadcrumbs.php');

        View::composer('platform::dashboard', MainMenuComposer::class);
        View::composer('platform::systems', SystemMenuComposer::class);

        $dashboard->registerPermissions($this->registerPermissionsSystems());

        $dashboard->registerGlobalSearch([
            //...Models
        ]);
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

    /**
     * @return ItemPermission
     */
    protected function registerPermissionsSystems(): ItemPermission
    {
        return ItemPermission::group(__('Systems'))
            ->addPermission('platform.systems.roles', __('Roles'))
            ->addPermission('platform.systems.users', __('Users'));
    }
}
