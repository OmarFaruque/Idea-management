<div id="attachment">
    <?php 
        if(get_post_meta( $post->ID, 'attachment', true )):   
            $url = wp_get_attachment_url( get_post_meta( $post->ID, 'attachment', true ) );
            $ext = pathinfo($url, PATHINFO_EXTENSION);
    ?>
            <div class="attachment">
                    <?php if(in_array($ext, array('jpg', 'jpeg', 'png'))): ?>
                        <img src="<?php echo esc_url( $url ); ?>" alt="attachment" />
                    <?php else: ?>
                        <div class="document">
                            <span class="dashicons dashicons-media-document"></span>
                        </div>
                    <?php endif; ?>
                    <a class="idea_download" href="<?php echo esc_url( $url ); ?>" download>
                        <span class="aicon">
                            <img src="<?php echo  esc_url(plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/img/cloud-computing.png');  ?>" alt="Download" >
                        </span>
                        <?php _e('Download', 'idea-management'); ?>
                    </a>
            </div>
    <?php else: ?>
        <div class="no_attachment">
            <span class="dashicons dashicons-no-alt"></span><br/>
            <span><?php _e('Didn\'t found any attachment.', 'idea-management'); ?></span>
        </div>
    <?php endif; ?>
</div>

<style>
    .no_attachment{
        display: block;
        text-align: center;
    }
    .no_attachment span.dashicons{
        font-size: 30px;
        margin-bottom: 5px;
    }
    div#attachment .attachment {
        display: block;
        overflow: hidden;
        float: unset;
        text-align: center;
        margin: 0 auto;
        line-height: 60px;
        width: auto;
    }
    div#attachment .attachment img{
        max-width: 100%;
    }
    .attachment .document span{
        font-size: 110px;
        display: inline-table;
    }
    a.idea_download {
        text-decoration: none;
        display: flex;
        line-height: initial;
        position: absolute;
        width: 100%;
        left: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        padding: 10px;
        color: white;
        justify-content: center;
        /* display: none; */
    }
    a.idea_download span.aicon{
        margin-right: 5px;
        margin-left: 0;
    }
    div#attachment .attachment a.idea_download span.aicon img{
        max-width: 20px;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        $("a.idea_download").slideUp();
        $("#attachment").mouseenter(function() {
            $("a.idea_download").stop().slideDown();
        });
        $("#attachment").mouseleave(function() {
            $("a.idea_download").slideUp();
        });
    });
</script>