<?php
/**
 * Settings Page Wrapper Template
 *
 * @var string $title
 * @var array $tabs
 * @var string $active_tab
 * @var string $menu_slug
 * @var SettingsPage $settings_page
 */
?>

<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>
    
    <?php if (count($tabs) > 1): ?>
        <nav class="nav-tab-wrapper">
            <?php foreach ($tabs as $tab_key => $tab): ?>
                <a 
                    href="<?php echo esc_url(add_query_arg(['page' => $menu_slug, 'tab' => $tab_key], admin_url('admin.php'))); ?>" 
                    class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>"
                >
                    <?php echo esc_html($tab['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
    
    <?php settings_errors(); ?>
    
    <form method="post" action="options.php">
        <?php
            settings_fields($menu_slug . '_' . $active_tab);
            do_settings_sections($menu_slug . '_' . $active_tab);
            submit_button();
        ?>
    </form>
</div>


