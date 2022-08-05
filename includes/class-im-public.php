<?php

/**
 * Load Backend related actions
 *
 * @class   IM_Public
 */


if (!defined('ABSPATH')) {
    exit;
}


class IM_Public
{
    /**
     * Class intance for singleton  class
     *
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $instance = null;

    /**
     * The token.
     *
     * @var     string
     * @access  public
     */
    public static $token;

    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * @access  public
     * @return  path
     */
    public static $plugin_path;

    /**
     * Constructor function.
     *
     * @access  public
     * @param string $file plugin start file path.
     * @since   1.0.0
     */
    public function __construct($file = '')
    {
        self::$plugin_path = IM_PATH;
        self::$token = IM_TOKEN;
        add_shortcode( 'idea-management', array($this, 'idea_management_shortcode_callback') );
    }



    /**
     * Pring main from for collect Idea
     * @access  public
     * @since 1.0
     * @return html 
     */
    public function idea_management_shortcode_callback(){
        require_once(self::$plugin_path . '/temp/idea-form.php');

        wp_enqueue_script( self::$token.'_js', plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/js/idea.js', array(), IM_Idea::$version, true );
        wp_localize_script(
            self::$token . '_js',
            self::$token . '_object',
            array(
                'api_nonce' => wp_create_nonce('wp_rest'),
                'root' => rest_url(self::$token . '/v1/')
            )
        );
    }
    

    /**
     * Ensures only one instance of Class is loaded or can be loaded.
     *
     * @param string $file plugin start file path.
     * @return Main Class instance
     * @since 1.0.0
     * @static
     */
    public static function instance($file = '')
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($file);
        }
        return self::$instance;
    }
}