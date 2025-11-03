<?php

namespace ModularAI\Providers;

use ModularAI\Abstracts\ServiceProvider;
use ModularAI\Utilities\Container;
use ModularAI\Api\Endpoints\RunModule;
use ModularAI\Api\Endpoints\ModuleTemplate;
use ModularAI\Api\Endpoints\TestModel;
use ModularAI\Api\Endpoints\ListModels;
use ModularAI\Api\Endpoints\ListModules;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container
     *
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        // Internal endpoints
        $container->singleton(RunModule::class);
        $container->singleton(ModuleTemplate::class);
        $container->singleton(TestModel::class);
        
        // Public API endpoints
        $container->singleton(ListModels::class);
        $container->singleton(ListModules::class);
    }

    /**
     * Boot services after all providers have been registered
     *
     * @param Container $container
     * @return void
     */
    public function boot(Container $container): void
    {
        // Internal endpoints
        $container->make(RunModule::class);
        $container->make(ModuleTemplate::class);
        $container->make(TestModel::class);
        
        // Public API endpoints
        $container->make(ListModels::class);
        $container->make(ListModules::class);
    }
}

