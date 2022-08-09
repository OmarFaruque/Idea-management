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
                        'methods' => 'POST',
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

                //get all comments by post id
                register_rest_route(
                    $this->token . '/v1',
                    '/getcomments/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'mi_get_idea_comments'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );


                //get all comments by post id
                register_rest_route(
                    $this->token . '/v1',
                    '/postcomment/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'mi_post_idea_comment'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );


                //Comment vote process 
                register_rest_route(
                    $this->token . '/v1',
                    '/comment_vote/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'mi_post_idea_comment_votes'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );

                //Idea vote process
                register_rest_route(
                    $this->token . '/v1',
                    '/idea_vote/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'mi_post_idea_votes'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );

            }
        ); 


        add_action( 'wp_head', array($this, 'testF') );
    }


    public function testF(){
        // $comments = get_comments( array('post_id' => 3252) );
        $post_id = 3252;
        $voted_users = get_post_meta( $post_id, 'voted_users', true ) && is_array(get_post_meta( $post_id, 'voted_users', true )) ? get_post_meta( $post_id, 'voted_users', true ) : array();

        echo 'typeof ' . gettype($voted_users) . '<br/>';
        // $comments = array_map(function($v){
        //     $v->profile_img = esc_url( get_avatar_url( $v->user_id ) );
        //     return $v;
        // }, $comments);

        echo 'comments <br/><pre>';
        print_r($voted_users);
        echo '</pre>';

        echo 'tests <br/><pre>';
        print_r(get_option( 'tests' ));
        echo '</pre>'; 

        
        
        $voted_users_array = is_array($voted_users) ? array_keys($voted_users) : array();
        if(!in_array(get_current_user_id(  ), $voted_users_array)) {
            echo 'yes current user found <br/>';
        }
    }

    

    /**
     * Vote on each comment  
     * @access  public 
     * @param   post_as_array
     * @return  all_comments
     */
    public function mi_post_idea_comment_votes($data){
        $return_array = array();
        $comment_id = (int) $data['comment_id']; 
        $post_id = (int) $data['post_id']; 
        $v_type = $data['v_type'];

        $return_array['comment_id'] = $data['comment_id'];


        // Return a single meta value with the key 'vote' from a defined comment object.
        $voted_users = get_post_meta( $post_id, 'voted_users', true ) && is_array(get_post_meta( $post_id, 'voted_users', true )) ? get_post_meta( $post_id, 'voted_users', true ) : array();
        
        $voted_users_array = is_array($voted_users) ? array_keys($voted_users) : array();
        if(!in_array(get_current_user_id(  ), $voted_users_array)) {
            $vote = get_comment_meta( $comment_id, 'vote', true ) ? get_comment_meta( $comment_id, 'vote', true ) : 0;
            $new_vote = $vote + 1;  
            update_comment_meta( $comment_id, 'vote', $new_vote );

            
            $voted_users[get_current_user_id(  )] = $comment_id;
            update_post_meta( $post_id, 'voted_users', $voted_users );
        }

        // Nagative vote / cancel vote
        if(in_array(get_current_user_id(  ), $voted_users_array) && $v_type == 'nagative'){
            $vote = get_comment_meta( $comment_id, 'vote', true ) ? get_comment_meta( $comment_id, 'vote', true ) : 0;
            $new_vote = $vote - 1;  
            update_comment_meta( $comment_id, 'vote', $new_vote );

            unset($voted_users[get_current_user_id(  )]);
            update_post_meta( $post_id, 'voted_users', $voted_users );
        }
        
        // New comment query with new one
        $comments = $this->im_get_post_comments($post_id);
        
        $return_array['vote'] = $new_vote;
        $return_array['user_id'] = get_current_user_id(  );
        $return_array['comments'] = $comments;



        // Get new data after update
        $n_voted_users = get_post_meta( $post_id, 'voted_users', true ) && is_array(get_post_meta( $post_id, 'voted_users', true )) ? get_post_meta( $post_id, 'voted_users', true ) : array();
        
        $n_voted_users_array = is_array($n_voted_users) ? array_keys($n_voted_users) : array();
        $previous_vote_id = isset($n_voted_users[get_current_user_id(  )]) ? $n_voted_users[get_current_user_id(  )] : false;
        $return_array['user_vote_status'] = in_array(get_current_user_id(  ), $n_voted_users_array);
        $return_array['p_vote_id'] = $previous_vote_id;

        return new WP_REST_Response($return_array, 200);
    }





    /**
     * Vote on each comment  
     * @access  public 
     * @param   post_as_array
     * @return  all_comments
     */
    public function mi_post_idea_votes($data){
        $return_array = array();
        $post_id = (int) $data['post_id']; 
        $v_type = $data['v_type'];
        $category = $data['category'];



        // Return a single meta value with the key 'vote' from a defined post object.
        $voted_users = get_post_meta( $post_id, 'idea_voted_users', true ) && is_array(get_post_meta( $post_id, 'idea_voted_users', true )) ? get_post_meta( $post_id, 'idea_voted_users', true ) : array();
        
        
        if(!in_array(get_current_user_id(  ), $voted_users)) {
            $vote = get_post_meta( $post_id, 'vote', true ) ? get_post_meta( $post_id, 'vote', true ) : 0;
            $new_vote = $vote + 1;  
            update_post_meta( $post_id, 'vote', $new_vote );

            if(!in_array(get_current_user_id(  ), $voted_users))
                array_push($voted_users, get_current_user_id(  ));

            update_post_meta( $post_id, 'idea_voted_users', $voted_users );
        }

        // Nagative vote / cancel vote
        if(in_array(get_current_user_id(  ), $voted_users) && $v_type == 'nagative'){
            $vote = get_post_meta( $post_id, 'vote', true ) ? get_post_meta( $post_id, 'vote', true ) : 0;
            $new_vote = $vote - 1;  
            update_post_meta( $post_id, 'vote', $new_vote );

            if (($key = array_search(get_current_user_id(  ), $voted_users)) !== false) {
                unset($voted_users[$key]);
            }
            
            update_post_meta( $post_id, 'idea_voted_users', $voted_users );
        }
        

        
        // Get new data after update
        $n_voted_users = get_post_meta( $post_id, 'idea_voted_users', true ) && is_array(get_post_meta( $post_id, 'idea_voted_users', true )) ? get_post_meta( $post_id, 'idea_voted_users', true ) : array();
        
        
        
        $return_array['user_vote_status'] = in_array(get_current_user_id(  ), $n_voted_users);
        $return_array['ideas'] = $this->im_get_ideas($category);

        return new WP_REST_Response($return_array, 200);
    }

    /**
     * Insert idea comment to DB 
     * @access  public 
     * @return  comments_array
     * @param   Post_array
     */
    public function mi_post_idea_comment($data){

        $return_array = array();
        
        $comment_id = $this->im_wp_insert_comment($data['comment'], $data['post_id']);
        if($comment_id){
            update_comment_meta( $comment_id, 'vote', 0 );
            $comments = $this->im_get_post_comments($data['post_id']);
            $return_array['comments'] = $comments;
        }
            
        return new WP_REST_Response($return_array, 200);
    }



    /**
     * Insert comment in DB
     * @access  protected
     * @param post_array
     * @param post_id
     * @return comment_id
     */
    protected function im_wp_insert_comment( $content, $postId ) {
        $current_user = wp_get_current_user();
     
        if ( comments_open( $postId ) ) {
            $data = array(
                'comment_post_ID'      => $postId,
                'comment_content'      => $content,
                'comment_approved'     => 1, 
                'comment_parent'       => 0,
                'user_id'              => $current_user->ID,
                'comment_author'       => $current_user->user_login,
                'comment_author_email' => $current_user->user_email,
                'comment_author_url'   => $current_user->user_url,
                'comment_meta'         => array()
            );
     
            $comment_id = wp_insert_comment( $data );
            if ( ! is_wp_error( $comment_id ) ) {
                return $comment_id;
            }
        }
     
        return false;
    }





    /**
     * Return all comment by post id
     * @access  protected
     * @param   post_id
     * @return  comments_as_array
     */
    protected function im_get_post_comments($post_id, $comment_filter = 'recent'){
        $args = array('post_id' => (int) $post_id);
        if($comment_filter != 'recent'){
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'vote';
            $args['order'] = 'DESC';
        }

        $comments = get_comments( $args );

        $comments = array_map(function($v){
            $v->profile_img = esc_url( get_avatar_url( $v->user_id ) );
            $v->vote_count = get_comment_meta( $v->comment_ID, 'vote', true );
            return $v;
        }, $comments);

        return $comments;
    }

    /**
     * get all comments by idea id
     * @access  public 
     * @since   1.0
     * @param   array
     */
    public function mi_get_idea_comments($post){
        $return_array = array();
        $post_id = (int) $post['post_id'];
        
        $comments = $this->im_get_post_comments($post['post_id'], $post['comment_filter']);
        
        $voted_users = get_post_meta( $post_id, 'voted_users', true ) ? get_post_meta( $post_id, 'voted_users', true ) : array();
        $previous_vote_id = isset($voted_users[get_current_user_id(  )]) ? $voted_users[get_current_user_id(  )] : false;
        $voted_users = is_array($voted_users) ? array_keys($voted_users) : array();


        $user_vote_status = in_array(get_current_user_id(  ), $voted_users); 


        $return_array['id'] = $post['post_id'];
        $return_array['comments'] = $comments;
        $return_array['user_vote_status'] = $user_vote_status;
        $return_array['p_vote_id'] = $previous_vote_id;
        $return_array['comment_filter'] = $post['comment_filter'];


        return new WP_REST_Response($return_array, 200);
    }

    /**
     * Create new idea on form submit from frontend
     * @access  public 
     * @since   1.0
     * @param   array
     */
    public function mi_create_idea($post){
        $return_array = array();
        $title = $_POST['title'];
        $content = $_POST['content'];
        $idea_type = $_POST['idea_type'];
        $file = $_FILES['file'];

        // insert the post and set the category
        $post_id = wp_insert_post(array (
            'post_type' => IM_Idea::$token,
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'pending'
        ));

        if($post_id){
            wp_set_post_terms( $post_id, array((int) $idea_type ), IM_Idea::$taxonomy, false );

            // upload file to direcotry
            $file_name = $file['name'];
            $file_temp = $file['tmp_name'];
    
            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents( $file_temp );
            $filename = basename( $file_name );
            $filetype = wp_check_filetype($file_name);
            $filename = time().'.'.$filetype['ext'];
    
            if ( wp_mkdir_p( $upload_dir['path'] ) ) {
              $file = $upload_dir['path'] . '/' . $filename;
            }
            else {
              $file = $upload_dir['basedir'] . '/' . $filename;
            }
    
            file_put_contents( $file, $image_data );
            $wp_filetype = wp_check_filetype( $filename, null );
            $attachment = array(
              'post_mime_type' => $wp_filetype['type'],
              'post_title' => sanitize_file_name( $filename ),
              'post_content' => '',
              'post_status' => 'inherit'
            );
    
            $attach_id = wp_insert_attachment( $attachment, $file );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            update_post_meta( $post_id, 'attachment', $attach_id );
            update_post_meta( $post_id, 'vote', 0 );

            $return_array['msg'] = 'success';
        }

        
        return new WP_REST_Response($return_array, 200);
    }

    /**
     * @access  public 
     * @return Single Shipping mehod configration as json
     */
    public function mi_getconfig($data)
    {

        $category = $data['category'];

        $return_array = array();

        $taxonomies = get_terms( array(
            'taxonomy' => 'idea_type',
            'hide_empty' => false
        ) );

        // All ideas
        $list_ideas = $this->im_get_ideas($category);

        $return_array['idea_type'] = $taxonomies;
        $return_array['ideas'] = $list_ideas;
        $return_array['cat'] = $category;

        return new WP_REST_Response($return_array, 200);
    }


    /**
     * Get ideas from DB
     * @access  protected 
     * @param   category_id
     * @return  post_array
     * @since   1.0
     */
    protected function im_get_ideas($category = false){
        $args = array(
            'numberposts' => -1,
            'post_type'   => IM_Idea::$token
        );

        if(isset($category) && !empty($category)){
            $args['tax_query'] = array(
                array(
                  'taxonomy' => 'idea_type',
                  'field' => 'term_id', 
                  'terms' => (int) $category, /// Where term_id of Term 1 is "1".
                  'include_children' => true
                )
            );
        }
           
        $list_ideas = get_posts( $args );

        $list_ideas = array_map(function($v){
            $idea_voted_users = get_post_meta( $v->ID, 'idea_voted_users', true ) ? get_post_meta( $v->ID, 'idea_voted_users', true ) : array();
            $v->user_vote_status = in_array(get_current_user_id(  ), $idea_voted_users);
            $v->vote_count = get_post_meta( $v->ID, 'vote', true ) ? get_post_meta( $v->ID, 'vote', true ) : 0;
            return $v;
        }, $list_ideas);

        return $list_ideas;
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
