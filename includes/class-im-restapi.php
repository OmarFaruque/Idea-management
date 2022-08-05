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

                //Delete shipping option from custom page
                register_rest_route(
                    $this->token . '/v1',
                    '/delete_shipping_option/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'acotrs_delete_shipping_callback'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );



                //Delete shipping multiple option
                register_rest_route(
                    $this->token . '/v1',
                    '/delete_methods/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'acotrs_delete_multiple_shipping_callback'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );

                
                // Licenced Info
                register_rest_route(
                    $this->token . '/v1',
                    '/initial_config/',
                    array(
                        'methods' => 'GET',
                        'callback' => array($this, 'acotrs_get_initial_config'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );


                register_rest_route(
                    $this->token . '/v1',
                    '/getconfig/',
                    array(
                        'methods' => 'GET',
                        'callback' => array($this, 'mi_getconfig'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );


                // Update subscript settings to option
                register_rest_route(
                    $this->token . '/v1',
                    '/updatedata/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'awc_update_config'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );

                register_rest_route(
                    $this->token . '/v1',
                    '/subscription-status-action/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'awc_subscription_status_action_callback'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );

                register_rest_route(
                    $this->token . '/v1',
                    '/search-subscriptions/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'awc_subscription_search'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );
            }
        );

        
        
    }


    


    /**
     * @access  public 
     * @return Single Shipping mehod configration as json
     */
    public function mi_getconfig()
    {

       $return_array = array('thisismymessage');

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
        if (current_user_can('administrator') || current_user_can('manage_woocommerce')) {
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
