<?php

/**
 * Plugin Name: Idea Management
 * Version: 1.0.0
 * Description: "Idea management help you to get idea from other user using a simple form. Also it's help you to vote on each idea from other user.".
 * Author: Oben
 * Author URI: https://github.com/OmarFaruque
 * Requires at least: 4.4.0
 * Tested up to: 6.0.1
 * Text Domain: idea-management
 */

define('IM_TOKEN', 'idea');
define('IM_VERSION', '1.0.0');
define('IM_FILE', __FILE__);
define('IM_PLUGIN_NAME', 'Idea Management');
define('IM_INIT_TIMESTAMP', gmdate( 'U' ) );
define('IM_PATH', realpath(plugin_dir_path(__FILE__)));


// Load and set up the Autoloader
require_once( dirname( __FILE__ ) . '/includes/class-im-autoloader.php' );
$im_autoloader = new IM_Autoloader( dirname( __FILE__ ) );
$im_autoloader->register();

IM_Admin::instance(__FILE__);
IM_Public::instance(__FILE__);

class IM_Idea{
    public static function init($autoloader = false){
        // Register custom post type for collect idea
		add_action( 'init', __CLASS__ . '::idea_register_post_types', 6 );
        
        // Register custom taxonomy for Idea custom post type
		add_action( 'init', __CLASS__ . '::idea_register_custom_taxonomy', 6 );
    }



    /**
     * Register custom custom taxonomy for Custom post type Idea
     * @access  public
     * @return register a custom taxonomy
     * @since   1.0
     */
    public static function idea_register_custom_taxonomy(){
        register_taxonomy('idea_type', ['idea'], [
            'label' => __('Idea Type', 'idea-management'),
            'hierarchical' => true,
            'rewrite' => ['slug' => 'idea-type'],
            'show_admin_column' => true,
            'show_in_rest' => true,
            'labels' => [
                'singular_name' => __('Idea Type', 'idea-management'),
                'all_items' => __('All idea types', 'idea-management'),
                'edit_item' => __('Edit Idea Type', 'idea-management'),
                'view_item' => __('View Idea Type', 'idea-management'),
                'update_item' => __('Update Idea Type', 'idea-management'),
                'add_new_item' => __('Add New Idea Type', 'idea-management'),
                'new_item_name' => __('New Idea Type Name', 'idea-management'),
                'search_items' => __('Search Idea Types', 'idea-management'),
                'popular_items' => __('Popular Idea Types', 'idea-management'),
                'separate_items_with_commas' => __('Separate Idea Types with comma', 'idea-management'),
                'choose_from_most_used' => __('Choose from most used Idea Types', 'idea-management'),
                'not_found' => __('No Idea Type found', 'idea-management'),
            ]
        ]);
        register_taxonomy_for_object_type('idea_type', 'idea');
    }


    /**
     * Register custom post type for Idea Management
     * @access  public
     * @return register a pos type
     * @since   1.0
     */
    public static function idea_register_post_types(){
        register_post_type('idea', [
            'label' => __('Idea', 'idea-management'),
            'public' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-book',
            'supports' => ['title', 'editor', 'thumbnail', 'author', 'revisions', 'comments'],
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'idea'],
            'taxonomies' => ['idea_type'],
            'labels' => [
                'singular_name' => __('Idea', 'idea-management'),
                'add_new_item' => __('Add new Idea', 'idea-management'),
                'new_item' => __('New Idea', 'idea-management'),
                'view_item' => __('View Idea', 'idea-management'),
                'not_found' => __('No ideas found', 'idea-management'),
                'not_found_in_trash' => __('No ideas found in trash', 'idea-management'),
                'all_items' => __('All Ideas', 'idea-management'),
                'insert_into_item' => __('Insert into Idea', 'idea-management')
            ],		
        ]);
    }


}
IM_Idea::init($im_autoloader);