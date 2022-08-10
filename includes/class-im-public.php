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
        add_filter( 'single_template', array($this, 'im_my_custom_template'), 50, 1 );

        add_action( 'idea_single_post', array($this, 'im_post_header'), 10 );
        add_action( 'idea_single_post', array($this, 'im_post_content'), 30 );

        add_action( 'idea_single_post_bottom', array($this, 'im_display_comments'), 20 );

        add_action( 'idea_post_content_after', array($this, 'im_display_attachment_download'), 10 );

        add_filter( 'the_content', array($this, 'im_idea_listing_page_callback'), 1 );

        add_action( 'idea_post_content_before', array($this, 'im_backto_listingpage_button') );
    }


    /**
     * Pring back to list button in single idea page
     * @access  public
     * @since   1.0
     */
    public function im_backto_listingpage_button(){
        $options = get_option( 'idea_options' );
        if(isset($options['idea_listing_page']) && $options['idea_listing_page'] != ''):
        ?>
            <a class="btn button" href="<?php echo get_permalink( $options['idea_listing_page'] ); ?>" ><< <?php _e('Back to List page', 'idea-management'); ?></a>
        <?php 
        endif;
    }
    

    /**
     * Change page content and pring shortcode for show idea listing
     * @access  public
     * @return  html
     */
    public function im_idea_listing_page_callback($content){
        global $post;
        $options = get_option( 'idea_options' );
        if(isset($options['idea_listing_page']) && $post->ID == $options['idea_listing_page'])
            return '[idea-management]';
        
        return $content;
    }

    /**
     * Download attachment for frontend single idea
     * @access  public
     * @return  html
     * @since   1.0
     */
    public function im_display_attachment_download(){
        global $post;
        require_once(self::$plugin_path . '/temp/admin-attachment.php');
    }


    /**
	 * idea display comments
	 *
	 * @since  1.0.0
	 */
	function im_display_comments() {
        global $post;
		// If comments are open or we have at least one comment, load up the comment template.
        
		if ( comments_open() || 0 !== intval( get_comments_number() ) ) :	
            // comments_template();
            require_once(self::$plugin_path . '/temp/comments.php');

            wp_enqueue_style( self::$token.'_comment_css', plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/css/comment.css', array(), IM_Idea::$version, 'all' );
            wp_enqueue_script( self::$token.'_comment_js', plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/js/comment.js', array(), IM_Idea::$version, true );
            
            wp_localize_script(
                self::$token . '_comment_js',
                self::$token . '_object',
                array(
                    'api_nonce' => wp_create_nonce('wp_rest'),
                    'root' => rest_url(self::$token . '/v1/'),
                    'homepage' => home_url( '/' ), 
                    'cpost' => self::$token, 
                    'post_id' => $post->ID, 
                    'user_login' => is_user_logged_in(  )
                )
            );
		endif;
	}


    /**
     * Display psot content
     * @access  public 
     * @since   1.0
     */
    public function im_post_content() {
		?>
		<div class="entry-content">
		<?php

		/**
		 * Functions hooked in to idea_post_content_before action.
		 *
		 * @hooked idea_post_thumbnail - 10
		 */
		do_action( 'idea_post_content_before' );

		the_content(
			sprintf(
				/* translators: %s: post title */
				__( 'Continue reading %s', 'idea-management' ),
				'<span class="screen-reader-text">' . get_the_title() . '</span>'
			)
		);

		do_action( 'idea_post_content_after' );

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'idea-management' ),
				'after'  => '</div>',
			)
		);
		?>
		</div><!-- .entry-content -->
		<?php
	}



    public function im_post_header() {
		?>
		<header class="entry-header">
		<?php

		/**
		 * Functions hooked in to storefront_post_header_before action.
		 *
		 * @hooked storefront_post_meta - 10
		 */
		do_action( 'storefront_post_header_before' );

		if ( is_single() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
		} else {
			the_title( sprintf( '<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
		}

		do_action( 'storefront_post_header_after' );
		?>
		</header><!-- .entry-header -->
		<?php
	}

    /**
     * Customize template load url from plugin
     * @access  public 
     * @since   1.0
     * @return  template_url
     */
    public function im_my_custom_template( $template ) {

        if ( is_singular( 'idea' ) ) {
            return self::$plugin_path . '/temp/single_idea.php';
        }
        
        return $template;
    }

    /**
     * Pring main from for collect Idea
     * @access  public
     * @since 1.0
     * @return html 
     */
    public function idea_management_shortcode_callback(){


        $options = get_option( 'idea_options' );

        $idea_collection_end_date = isset($options['idea_collection_end_date']) && $options['idea_collection_end_date'] != '' && time() > strtotime($options['idea_collection_end_date']) ? date('jS F, Y', strtotime($options['idea_collection_end_date'])) : false;

        $datediff = false; 
        if(isset($options['vote_start_date']) && $options['vote_start_date'] != '' && time() < strtotime($options['vote_start_date'])){
            $datediff = strtotime($options['vote_start_date']) - time();
            $datediff = round($datediff / (60 * 60 * 24));
        }

        $voteAllowed = isset($options['vote_start_date']) && isset($options['vote_end_date']) && $options['vote_end_date'] != '' && $options['vote_start_date'] != '' && time() > strtotime($options['vote_start_date']) && time() < strtotime($options['vote_end_date']) ? true : false;
        
        require_once(self::$plugin_path . '/temp/idea-form.php');

        wp_enqueue_style( self::$token.'_css', plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/css/idea.css', array(), IM_Idea::$version, 'all' );
        wp_enqueue_script( self::$token.'_js', plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/js/idea.js', array(), IM_Idea::$version, true );
        
        wp_localize_script(
            self::$token . '_js',
            self::$token . '_object',
            array(
                'api_nonce' => wp_create_nonce('wp_rest'),
                'root' => rest_url(self::$token . '/v1/'),
                'homepage' => home_url( '/' ), 
                'cpost' => self::$token, 
                'idea_collection_end_date' => $idea_collection_end_date, 
                'datediff' => $datediff,
                'vote_allowed' => $voteAllowed, 
                'user_login' => is_user_logged_in(  )

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