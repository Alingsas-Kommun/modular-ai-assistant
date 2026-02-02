<?php

namespace ModularAIAssistant\Utilities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Container
{
    private static array $bindings = [];
    private static array $instances = [];

    /**
     * Register a singleton binding (class name only)
     * Similar to Laravel's $this->app->singleton()
     *
     * @param string $abstract The class name to bind
     * @param callable|string|null $concrete Optional concrete implementation or factory
     * @return void
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        self::$bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'shared' => true
        ];
    }

    /**
     * Register a service instance directly
     * For backwards compatibility
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function register(string $key, $value): void
    {
        self::$instances[$key] = $value;
    }

    /**
     * Make/resolve an instance from the container
     * Similar to Laravel's $this->app->make()
     *
     * @param string $abstract
     * @return mixed
     */
    public function make(string $abstract)
    {
        // If already instantiated, return the instance
        if (isset(self::$instances[$abstract])) {
            return self::$instances[$abstract];
        }

        // If no binding exists, try to instantiate directly
        if (!isset(self::$bindings[$abstract])) {
            return $this->build($abstract);
        }

        $concrete = self::$bindings[$abstract]['concrete'];
        $shared = self::$bindings[$abstract]['shared'];

        // Build the instance
        if ($concrete instanceof \Closure) {
            $instance = $concrete($this);
        } else {
            $instance = $this->build($concrete);
        }

        // Store if shared (singleton)
        if ($shared) {
            self::$instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Build an instance of the given class
     *
     * @param string $concrete
     * @return mixed
     */
    protected function build(string $concrete)
    {
        if (!class_exists($concrete)) {
            throw new \RuntimeException(
                sprintf(
                    /* Translators: %s: Class name */
                    esc_html__('Class %s does not exist', 'modular-ai-assistant'),
                    esc_html($concrete)
                )
            );
        }

        return new $concrete();
    }

    /**
     * Get a registered service
     * For backwards compatibility
     *
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        if (!isset(self::$instances[$key])) {
            throw new \RuntimeException(esc_html("Service not registered: {$key}"));
        }
        
        return self::$instances[$key];
    }

    /**
     * Check if a service has been resolved
     *
     * @param string $abstract
     * @return bool
     */
    public function resolved(string $abstract): bool
    {
        return isset(self::$instances[$abstract]);
    }

    /**
     * Check if a binding exists
     *
     * @param string $abstract
     * @return bool
     */
    public function bound(string $abstract): bool
    {
        return isset(self::$bindings[$abstract]) || isset(self::$instances[$abstract]);
    }
} 