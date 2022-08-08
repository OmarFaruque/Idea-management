<?php

/**
 * Load Backend related actions
 *
 * @class   IM_Admin
 */


if (!defined('ABSPATH')) {
    exit;
}


class IM_Admin
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
    public $token;

    /**
     * @access  public
     * @return  path
     */
    public static $plugin_path;

    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

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
        add_action( "add_meta_boxes", array( $this, "im_add_mata_box" ) );
    }

    /**
     * Add meta box to idea post type for show attachment information 
     * @param   null 
     * @since   1.0
     * @access  public
     */
    public function im_add_mata_box(){
        add_meta_box( 'idea_attachment', __( 'Attachment','idea-management' ), array($this, 'im_attachment_metabox_callback'), 'idea', 'side' );
    }


    /**
     * Add meta box to idea post type for show attachment information 
     * @param   post_id 
     * @since   1.0
     * @access  public
     */
    public function im_attachment_metabox_callback($post){
        require_once(self::$plugin_path . '/temp/admin-attachment.php');
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