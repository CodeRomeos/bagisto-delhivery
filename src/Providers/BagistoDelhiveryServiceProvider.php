<?php

namespace CodeRomeos\BagistoDelhivery\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class BagistoDelhiveryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin-routes.php');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'bagistodelhivery');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'bagistodelhivery');

        Event::listen('bagisto.admin.layout.head', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('bagistodelhivery::admin.layouts.style');
        });

        Blade::component('bagistodelhivery::shop.components.pincode-availability', 'bagistodelhivery::pincode-availability');

        $this->registerBladeDirectives();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php',
            'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php',
            'acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/delhivery.php',
            'delhivery'
        );
    }

    protected function registerBladeDirectives()
    {
        Blade::directive('delhiveryPincode', function ($pincode) {
            return "<?php echo 'check'; ?>";
        });
    }
}
