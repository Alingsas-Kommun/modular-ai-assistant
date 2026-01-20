<?php

namespace ModularAIAssistant\Content;

use ModularAIAssistant\Content\Interfaces\AdapterInterface;

class Adapters
{
    /** @var AdapterInterface[] */
    private array $adapters = [];

    /**
     * Registers a new adapter.
     * 
     * @param AdapterInterface $adapter
     * @return void
     */
    public function registerAdapter(AdapterInterface $adapter): void
    {
        $this->adapters[] = $adapter;
    }

    /**
     * Injects all adapter content into the post content.
     * 
     * @param string $content
     * @param int $post_id
     * @return string
     */
    public function injectAll(string $content, int $post_id): string
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->supports($post_id)) {
                $content = $adapter->inject($content, $post_id);
            }
        }

        return $content;
    }

    /**
     * Get all adapter file paths.
     *
     * @return array
     */
    public static function getAdapterFiles(): array
    {
        $adapter_dir = __DIR__ . '/Adapters';
        
        if (!is_dir($adapter_dir)) {
            return [];
        }

        $files = [];
        $items = scandir($adapter_dir);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $adapter_dir . '/' . $item;
            if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $files[] = $path;
            }
        }
        
        return $files;
    }

    /**
     * Get all adapter class names.
     *
     * @return array
     */
    public static function getAdapterClasses(): array
    {
        $files = self::getAdapterFiles();
        $namespace = 'ModularAIAssistant\\Content\\Adapters\\';

        return array_map(function($file) use ($namespace) {
            return $namespace . basename($file, '.php');
        }, $files);
    }
}

