<?php

namespace ModularAIAssistant\Providers;

use ModularAIAssistant\Abstracts\ServiceProvider;
use ModularAIAssistant\Utilities\Container;
use ModularAIAssistant\Api\Endpoints\RunModule;
use ModularAIAssistant\Api\Endpoints\ModuleTemplate;
use ModularAIAssistant\Api\Endpoints\TestModel;
use ModularAIAssistant\Api\Endpoints\ListModels;
use ModularAIAssistant\Api\Endpoints\ListModules;

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

