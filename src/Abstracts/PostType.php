<?php

namespace ModularAI\Abstracts;

abstract class PostType
{
    protected static $post_type_slug = '';
    protected static $public = false;
    protected static $publicly_queryable = false;
    protected static $show_ui = true;
    protected static $show_in_menu = 'modular-ai';
    protected static $show_in_rest = true;
    protected static $supports = ['title', 'revisions'];
    protected static $menu_icon = 'dashicons-admin-generic';
    protected static $has_archive = false;
    protected static $hierarchical = false;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        add_action('init', [$this, 'registerPostType']);
        add_filter('manage_' . static::$post_type_slug . '_posts_columns', [$this, 'customColumns']);
        add_action('manage_' . static::$post_type_slug . '_posts_custom_column', [$this, 'customColumnContent'], 10, 2);
    }

    /**
     * Register the custom post type
     *
     * @return void
     */
    public function registerPostType()
    {
        $args = [
            'labels'             => $this->getLabels(),
            'description'        => $this->getDescription(),
            'public'             => static::$public,
            'publicly_queryable' => static::$publicly_queryable,
            'show_ui'            => static::$show_ui,
            'show_in_menu'       => static::$show_in_menu,
            'show_in_rest'       => static::$show_in_rest,
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => static::$has_archive,
            'hierarchical'       => static::$hierarchical,
            'menu_position'      => null,
            'supports'           => static::$supports,
            'menu_icon'          => static::$menu_icon,
        ];

        register_post_type(static::$post_type_slug, $args);
    }

    /**
     * Add custom columns for post type
     *
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    public function customColumns($columns)
    {
        // Remove date temporarily
        unset($columns['date']);

        $columns_to_add = $this->getColumns();

        foreach ($columns_to_add as $col) {
            $this->arraySpliceAssoc($columns, $col['priority'], 0, [$col['slug'] => $col['title']]);
        }

        // Re-add date at the end
        $columns['date'] = __('Date', 'modular-ai');

        return $columns;
    }

    /**
     * Display custom column content for post type
     *
     * @param string $column Column name
     * @param int $post_id Post ID
     * @return void
     */
    public function customColumnContent($column, $post_id)
    {
        // Convert snake_case to camelCase for method name
        $method_suffix = str_replace('_', '', ucwords($column, '_'));
        $method = 'renderColumn' . $method_suffix;
        
        if (method_exists($this, $method)) {
            $this->$method($post_id);
        }
    }

    /**
     * Helper function to insert array elements at specific position
     *
     * @param array $input Input array
     * @param int|string $offset Position to insert at
     * @param int $length Number of elements to remove
     * @param array $replacement Elements to insert
     * @return void
     */
    protected function arraySpliceAssoc(&$input, $offset, $length, $replacement = [])
    {
        $replacement = (array) $replacement;
        $key_indices = array_flip(array_keys($input));

        if (isset($input[$offset]) && is_string($offset)) {
            $offset = $key_indices[$offset];
        }

        if (isset($input[$length]) && is_string($length)) {
            $length = $key_indices[$length] - $offset;
        }

        $input = array_slice($input, 0, $offset, true) + $replacement + array_slice($input, $offset + $length, null, true);
    }

    /**
     * Get post type labels
     *
     * @return array
     */
    abstract protected function getLabels();

    /**
     * Get post type description
     *
     * @return string
     */
    abstract protected function getDescription();

    /**
     * Get custom columns configuration
     *
     * @return array Array of column definitions with 'slug', 'title', and 'priority'
     */
    abstract protected function getColumns();
}

