<?php

namespace ModularAI\Providers;

use ModularAI\Abstracts\ServiceProvider;
use ModularAI\Utilities\Container;
use ModularAI\Assets\Frontend as FrontendAssets;

class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container
     *
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        $container->singleton(FrontendAssets::class);
    }

    /**
     * Boot services after all providers have been registered
     *
     * @param Container $container
     * @return void
     */
    public function boot(Container $container): void
    {
        $container->make(FrontendAssets::class);
    }
}

