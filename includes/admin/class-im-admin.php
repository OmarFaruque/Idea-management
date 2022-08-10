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
        add_action( 'admin_menu', array($this, 'im_add_setting_menu'), 20);
        add_action( 'admin_init', array($this, 'idea_settings_init') );
    }


    /**
     * Set Settings section and field for admin settings page
     * @access  public 
     * @return  some_html 
     * @since   1.0
     */
    public function idea_settings_init(){
        // Register a new setting for "idea_settings" page.
        register_setting( 'idea_settings', 'idea_options' );
    
        // Register a new section in the "idea_settings" page.
        add_settings_section(
            'idea_section_settings',
            __( 'Settings for idea management.', 'idea-management' ), array($this, 'idea_settings_callback'),
            'idea_settings'
        );
    
        // Register a new field in the "idea_section_settings" section, inside the "idea_settings" page.
        add_settings_field(
            'idea_collection_end_date', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'Idea Collection End Date', 'idea-management' ),
            array($this, 'idea_field_pill_cb'),
            'idea_settings',
            'idea_section_settings',
            array(
                'label_for'         => 'idea_collection_end_date',
                'class'             => 'idea_row',
                'idea_custom_data' => 'custom',
                'note' => esc_html( 'User can\'t place their Idea after selected date.', 'idea-management' ), 
                'type' => 'date'
            )
        );

        // Field for vote start date
        add_settings_field(
            'vote_start_date', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'Vote Start Date', 'idea-management' ),
            array($this, 'idea_field_pill_cb'),
            'idea_settings',
            'idea_section_settings',
            array(
                'label_for'         => 'vote_start_date',
                'class'             => 'idea_row',
                'idea_custom_data' => 'custom',
                'type' => 'date',
                'note' => esc_html( 'User can\'t place their vote before selected date.', 'idea-management' )
            )
        );


        // Field for vote End date
        add_settings_field(
            'vote_end_date', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'Vote End Date', 'idea-management' ),
            array($this, 'idea_field_pill_cb'),
            'idea_settings',
            'idea_section_settings',
            array(
                'label_for'         => 'vote_end_date',
                'class'             => 'idea_row',
                'idea_custom_data' => 'custom',
                'type' => 'date',
                'note' => esc_html( 'User can\'t place their vote after selected date.', 'idea-management' )
            )
        );


        // Listing page selection
        add_settings_field(
            'idea_listing_page', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'Select Listing page', 'idea-management' ),
            array($this, 'idea_field_pill_cb'),
            'idea_settings',
            'idea_section_settings',
            array(
                'label_for'         => 'idea_listing_page',
                'class'             => 'idea_row',
                'idea_custom_data' => 'custom',
                'note' => esc_html( 'Select a page where you want to show all of idea as list.', 'idea-management' ), 
                'type' => 'select'
            )
        );
    }





    /**
     * Pill field callbakc function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
     */
    public function idea_field_pill_cb( $args ) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'idea_options' );
        if($args['type'] != 'select'):
        ?>
            <input type="date" 
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            data-custom="<?php echo esc_attr( $args['idea_custom_data'] ); ?>"
            name="idea_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
            value="<?php echo isset($options[ $args['label_for'] ]) ? $options[ $args['label_for'] ] : ''; ?>"
            placeholder="mm/dd/yyyy"
            >
        
            <p class="description">
                <?php esc_html_e($args['note']); ?>
            </p>
        <?php
        else:
        ?>
                <select
                        id="<?php echo esc_attr( $args['label_for'] ); ?>"
                        data-custom="<?php echo esc_attr( $args['idea_custom_data'] ); ?>"
                        name="idea_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
                        <option value=""><?php _e('Select a page', 'idea-management'); ?></option>
                        <?php foreach(get_all_page_ids(  ) as $page): ?>
                            <option value="<?php echo $page; ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], $page, false ) ) : ( '' ); ?>>
                                <?php echo get_the_title( $page ); ?>
                            </option>
                        <?php endforeach; ?>

                    
                </select>
                <p class="description">
                <?php esc_html_e($args['note']); ?>
                </p>
        <?php 
        endif;
    }


    /**
     * Developers section callback function.
     *
     * @param array $args  The settings array, defining title, id, callback.
     */
    public function idea_settings_callback( $args ) {
        ?>
      
        <?php
    }
    

    /**
     * Add a menu to idea post type for dashboard settings 
     * @access  public 
     * @param   null 
     * @since   1.0
     */
    public function im_add_setting_menu(){
        add_submenu_page( 'edit.php?post_type=idea', __('Idea Settings', 'idea-management'), __('Settings', 'idea-management'), 'manage_options', 'idea_settings', array($this, 'im_settings_page'), 10 );
    }



    /**
     * Admin settings page
     * @access  public 
     * @return  html_settings_page
     * @since   1.0
     */
    public function im_settings_page(){
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

         // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'idea_messages', 'idea_message', __( 'Settings Saved', 'idea-management' ), 'updated' );
        }

        // show error/update messages
        settings_errors( 'idea_messages' );

        require_once(self::$plugin_path . '/temp/admin-settings.php');


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