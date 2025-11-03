<?php

namespace ModularAI\Abstracts;

use ModularAI\Utilities\Container;

abstract class ServiceProvider
{
    /**
     * Register services in the container
     *
     * @param Container $container
     * @return void
     */
    abstract public function register(Container $container): void;

    /**
     * Boot services after all providers have been registered
     *
     * @param Container $container
     * @return void
     */
    public function boot(Container $container): void
    {
        // Override this method in your service provider if needed
    }
}
