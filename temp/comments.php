<div id="comments" style="position:relative;">
    <div v-if="loader" :class="style.loader">
        <div>
            <img src="<?php echo esc_url( plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/img/loader.svg' ); ?>" alt="<?php _e('loader', 'idea-management'); ?>">
        </div>
    </div>
    <div :class="style.commentlists">
        <div :class="filter_row">
            <select v-on:change="commentFilter()" name="comment_filter" id="comment_filter" ref="comment_filter">
                <option value="recent"><?php _e('Most Recent', 'idea-management'); ?></option>
                <option value="top_rated"><?php _e('Top Tated', 'idea-management'); ?></option>
            </select>
        </div>
        <div v-for="comment in comments" :class="style.row">
            <div>
                <img :src="comment.profile_img" :alt="comment.comment_author" />
                <p><small><i>{{comment.comment_author}}</i></small></p>
            </div>
            <div>
                <p>{{comment.comment_content}}</p>
                <div :class="[user_vote_status ? style.nvote : style.vote]">
                    <span v-if="user_vote_status"><i>{{comment.vote_count}}</i></span>
                    <span :class="style.votespan" v-if="!user_vote_status" v-on:click="voteprocess(comment.comment_ID, 'pogetive')" style="background-image:url(<?php echo esc_url( plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/img/thumb-up.png' );  ?>)"></span>
                    <span :class="style.voteshow" v-if="user_vote_status && comment.comment_ID != p_vote_id" style="background-image:url(<?php echo esc_url( plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/img/heart.png' );  ?>)"></span>
                    <span :class="style.nagativevote" v-on:click="voteprocess(comment.comment_ID, 'nagative')" v-if="user_vote_status && comment.comment_ID == p_vote_id" style="background-image:url(<?php echo esc_url( plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/img/thumb-up.png' );  ?>)"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <br>
        <hr>
        <br>
    </div>
    <div class="commentform">
        <h4><?php _e('Leave a Comment', 'idea-management'); ?></h4>
        <form action="" @submit.prevent="formSubmit()" method="post">
            <div :class="style.row">
                <label for="comment"><?php _e('Comment', 'idea-management'); ?></label>
                <textarea ref="comment" name="comment" id="comment" cols="30" rows="10"></textarea>
            </div>
            <div :class="style.row">
                <button type="submit"><?php _e('Post Comment', 'idea-management'); ?></button>
            </div>
        </form>
    </div>
</div>