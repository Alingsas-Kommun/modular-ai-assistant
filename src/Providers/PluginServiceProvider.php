<?php

namespace ModularAI\Providers;

use ModularAI\Abstracts\ServiceProvider;
use ModularAI\Utilities\Container;
use ModularAI\Utilities\ViteManifest;
use ModularAI\Shortcodes\ModularAI as ModularAIShortcode;
use ModularAI\Entities\Models\Repository as ModelsRepository;
use ModularAI\Entities\Modules\Repository as ModulesRepository;
use ModularAI\Entities\ApiKeys\Repository as ApiKeysRepository;
use ModularAI\Http\Client;
use ModularAI\Http\Clients\OpenAIClient;
use ModularAI\Services\ModuleRunner;
use ModularAI\Services\ModuleCacheService;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container
     *
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        $container->singleton(ViteManifest::class);
        $container->singleton(ModularAIShortcode::class);
        $container->singleton(ModelsRepository::class);
        $container->singleton(ModulesRepository::class);
        $container->singleton(ApiKeysRepository::class);
        $container->singleton(Client::class);
        $container->singleton(OpenAIClient::class);
        $container->singleton(ModuleRunner::class);
        $container->singleton(ModuleCacheService::class);
    }

    /**
     * Boot services after all providers have been registered
     *
     * @param Container $container
     * @return void
     */
    public function boot(Container $container): void
    {
        $container->make(ViteManifest::class);
        $container->make(ModularAIShortcode::class);
        $container->make(ModelsRepository::class);
        $container->make(ModulesRepository::class);
        $container->make(ApiKeysRepository::class);
        $container->make(Client::class);
        $container->make(OpenAIClient::class);
        $container->make(ModuleRunner::class);
        $container->make(ModuleCacheService::class);
    }
}
