<?php

namespace ModularAI;

use ModularAI\Abstracts\ServiceProvider;
use ModularAI\Utilities\Container;

class Application
{
    /**
     * The service providers that will be booted
     *
     * @var array
     */
    protected array $providers = [];

    /**
     * The container instance
     *
     * @var Container
     */
    protected Container $container;

    /**
     * Create a new Application instance
     */
    public function __construct()
    {
        $this->container = new Container();
    }

    /**
     * Configure the application with service providers
     *
     * @return static
     */
    public static function configure(): static
    {
        return new static();
    }

    /**
     * Register service providers
     *
     * @param array $providers
     * @return static
     */
    public function withProviders(array $providers): static
    {
        $this->providers = $providers;
        return $this;
    }

    /**
     * Boot the application and all service providers
     *
     * @return void
     */
    public function boot(): void
    {
        // First, register all providers
        $this->registerProviders();

        // Then boot all providers
        $this->bootProviders();
    }

    /**
     * Register all service providers
     *
     * @return void
     */
    protected function registerProviders(): void
    {
        foreach ($this->providers as $provider) {
            $this->registerProvider($provider);
        }
    }

    /**
     * Register a single service provider
     *
     * @param string $provider
     * @return void
     */
    protected function registerProvider(string $provider): void
    {
        $instance = new $provider();
        
        if (!($instance instanceof ServiceProvider)) {
            throw new \InvalidArgumentException(
                sprintf(
                    /* Translators: %s: Provider class name */
                    esc_html__('Provider %s must extend ServiceProvider', 'modular-ai'),
                    esc_html($provider)
                )
            );
        }

        $instance->register($this->container);
    }

    /**
     * Boot all service providers
     *
     * @return void
     */
    protected function bootProviders(): void
    {
        foreach ($this->providers as $provider) {
            $this->bootProvider($provider);
        }
    }

    /**
     * Boot a single service provider
     *
     * @param string $provider
     * @return void
     */
    protected function bootProvider(string $provider): void
    {
        $instance = new $provider();
        $instance->boot($this->container);
    }

    /**
     * Get the container instance
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}
