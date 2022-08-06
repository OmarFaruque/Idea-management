<?php

if (!defined('ABSPATH')) {
    exit;
}

class IM_Restapi
{


    /**
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $instance = null;

    /**
     * The version number.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $version;
    /**
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    /**
     * Wp dB 
     * @var     string
     * @access  private
     * 
     */
    private $wpdb;

    /**
     * Item ID for remote api request to acoweb server for API Key
     */
    public $item_id;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->token = IM_TOKEN;
        

        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    $this->token . '/v1',
                    '/getconfig/',
                    array(
                        'methods' => 'GET',
                        'callback' => array($this, 'mi_getconfig'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );
                

                //Create new idea
                register_rest_route(
                    $this->token . '/v1',
                    '/create/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'mi_create_idea'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );
            }
        ); 
    }


    


    /**
     * Create new idea on form submit from frontend
     * @access  public 
     * @since   1.0
     * @param   array
     */
    public function mi_create_idea($post){
        $return_array = array();
        // $title = $post['title'];
        // $content = $post['content'];
        // $idea_type = $post['idea_type'];

        // insert the post and set the category
        // $post_id = wp_insert_post(array (
        //     'post_type' => IM_Idea::$token,
        //     'post_title' => $title,
        //     'post_content' => $content,
        //     'post_status' => 'pending'
        // ));

        // if($post_id){
        //     wp_set_post_terms( $post_id, array((int) $idea_type ), IM_Idea::$taxonomy, false );
        // }
        
        $return_array['msg'] = 'success';
        $return_array['title'] = $post['title'];
        $return_array['file'] = $post['file'];
        return new WP_REST_Response($return_array, 200);
    }

    /**
     * @access  public 
     * @return Single Shipping mehod configration as json
     */
    public function mi_getconfig()
    {

       $return_array = array();

       $taxonomies = get_terms( array(
            'taxonomy' => 'idea_type',
            'hide_empty' => false
        ) );
        $return_array['idea_type'] = $taxonomies;

        return new WP_REST_Response($return_array, 200);
    }

    /**
     *
     * Ensures only one instance of APIFW is loaded or can be loaded.
     *
     * @param string $file Plugin root path.
     * @return Main APIFW instance
     * @see WordPress_Plugin_Template()
     * @since 1.0.0
     * @static
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Permission Callback
     **/
    public function getPermission()
    {
        if (is_user_logged_in(  )) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }
}
