<?php

namespace ModularAIAssistant\Providers;

use ModularAIAssistant\Abstracts\ServiceProvider;
use ModularAIAssistant\Utilities\Container;
use ModularAIAssistant\Assets\Admin as AdminAssets;
use ModularAIAssistant\Admin\Menu;
use ModularAIAssistant\Admin\Settings;
use ModularAIAssistant\Admin\EditorIntegration;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container
     *
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        if (! is_admin()) {
            return;
        }

        $container->singleton(AdminAssets::class);
        $container->singleton(Menu::class);
        $container->singleton(Settings::class);
        $container->singleton(EditorIntegration::class);
    }

    /**
     * Boot services after all providers have been registered
     *
     * @param Container $container
     * @return void
     */
    public function boot(Container $container): void
    {
        if (! is_admin()) {
            return;
        }
        
        $container->make(AdminAssets::class);
        $container->make(Menu::class);
        $container->make(Settings::class);
        $container->make(EditorIntegration::class);
    }
}
