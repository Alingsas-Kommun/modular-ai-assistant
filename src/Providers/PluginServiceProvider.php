<?php

namespace ModularAIAssistant\Providers;

use ModularAIAssistant\Abstracts\ServiceProvider;
use ModularAIAssistant\Utilities\Container;
use ModularAIAssistant\Utilities\ViteManifest;
use ModularAIAssistant\Shortcodes\ModularAIAssistant as ModularAIAssistantShortcode;
use ModularAIAssistant\Entities\Models\Repository as ModelsRepository;
use ModularAIAssistant\Entities\Modules\Repository as ModulesRepository;
use ModularAIAssistant\Entities\ApiKeys\Repository as ApiKeysRepository;
use ModularAIAssistant\Http\Client;
use ModularAIAssistant\Http\Clients\OpenAIClient;
use ModularAIAssistant\Services\ModuleRunner;
use ModularAIAssistant\Services\ModuleCacheService;

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
        $container->singleton(ModularAIAssistantShortcode::class);
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
        $container->make(ModularAIAssistantShortcode::class);
        $container->make(ModelsRepository::class);
        $container->make(ModulesRepository::class);
        $container->make(ApiKeysRepository::class);
        $container->make(Client::class);
        $container->make(OpenAIClient::class);
        $container->make(ModuleRunner::class);
        $container->make(ModuleCacheService::class);
    }
}
