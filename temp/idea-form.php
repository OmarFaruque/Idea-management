<div id="im_form">
    <div v-if="loader" :class="style.loader">
        <div>
            <img src="<?php echo esc_url( plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/img/loader.svg' ); ?>" alt="<?php _e('loader', 'idea-management'); ?>">
        </div>
    </div>
    <div v-if="listing">
        <div v-if="datediff" :class="style.counter">
            <div>
                <span>{{datediff}} <?php _e(' day left for start vote.', 'idea-management'); ?></span>
            </div>
        </div>
        <div :class="style.filterwrap">
            <div>
                <div :class="style.frow">
                    <label for="category"><?php _e('Category', 'idea-management'); ?>:&nbsp;</label>
                    <select v-on:change="listByCategory()" ref="list_by_category" name="category" id="category">
                        <option value=""><?php _e('All', 'idea-management'); ?></option>
                        <option v-for="idea in idea_types" v-bind:value="idea.term_id">{{idea.name}}</option>
                    </select>
                </div>
                &nbsp;
                |
                &nbsp;
                <div :class="style.frow">
                    <select v-on:change="ideaFilter()" name="idea_filter" id="idea_filter" ref="idea_filter">
                        <option value="top_rated"><?php _e('Top Tated', 'idea-management'); ?></option>
                        <option value="recent"><?php _e('Most Recent', 'idea-management'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div :class="style.list_row" v-for="item in ideas">
            <div>{{item.post_title}}</div>
            <div :class="style.details">
                <a :href="idea_url + '/' + item.post_name">
                    <span class="dashicons dashicons-visibility"></span><?php _e('View', 'idea-management'); ?>
                </a>
            </div>
            <div  :class="[item.user_vote_status ? style.nvote : style.vote]">
                

                    <span :class="style.vote_count"><i>{{item.vote_count}}</i></span>
                    <span :class="style.votespan" v-if="!item.user_vote_status && vote_allowed" v-on:click="voteprocess(item.ID, 'pogetive')" style="background-image:url(<?php echo esc_url( plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/img/thumb-up.png' );  ?>)"></span>
                    <span :class="style.nagativevote" v-on:click="voteprocess(item.ID, 'nagative')" v-else-if="item.user_vote_status && vote_allowed" style="background-image:url(<?php echo esc_url( plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/img/thumb-up.png' );  ?>)"></span>
                    <span :class="style.hart"  v-else style="background-image:url(<?php echo esc_url( plugin_dir_url( IM_Idea::$plugin_file ) . 'assets/img/heart.png' );  ?>)"></span>
            </div>
        </div>

        
        <div :class="style.addnewIdea">
            <span v-if="idea_collection_end_date"><?php _e('New idea collection are closed since ', 'dea-management'); ?>{{idea_collection_end_date}}. </span>
            <button v-else v-on:click="gotoNewIdeaForm()"><?php _e('Add New Idea', 'idea-management'); ?></button>
        </div>
    </div>

    <div v-else >
        <button v-on:click="gotoNewIdeaForm()" type="button"><?php _e('Back to Listing', 'idea-management'); ?></button>
        <br/>
        <form action="" @submit.prevent="formSubmit()" method="post" enctype="multipart/form-data">
            <div :class="style.row">
                <label for="title"><?php _e('Idea Title', 'idea-management'); ?></label>
                <input type="text" :value="title" ref="title" class="form-control" name="title">
            </div>
            <div :class="style.row">
                <label for="content"><?php _e('Details', 'idea-management'); ?></label>
                <textarea name="content" :value="content" ref="content" id="content" cols="30" rows="10">{{message}}</textarea>
            </div>
            <div :class="style.row">
                <label for="idea_type"><?php _e('Category', 'idea-management'); ?></label>
                <select name="idea_type" ref="idea_type" id="idea_type" class="form-control">
                    <option v-for="idea in idea_types" v-bind:value="idea.term_id">{{idea.name}}</option>
                </select>
            </div>
            <div :class="style.row">
                <input type="file" name="file_upload"  id="file_upload" v-on:change="handleFileUpload()" ref="file">
            </div>
            <br>
            <div v-if="show_msg" :class="style.message">
                <span><?php _e('Your idea are submitted to admin for approval. Please wait for admin approval.', 'idea-management'); ?></span>
            </div>
            <div :class="style.row">
                <input type="submit" value="<?php _e('Submit', 'idea-management'); ?>">
            </div>
        </form>
    </div>
</div>