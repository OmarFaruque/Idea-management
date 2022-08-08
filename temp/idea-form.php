<div id="im_form">
    <div v-if="listing">
        <div :class="style.list_row" v-for="item in ideas">
            <div>{{item.post_title}}</div>
            <div>
                <a :href="idea_url + '/' + item.post_name">
                    <span class="dashicons dashicons-visibility"></span><?php _e('View', 'idea-management'); ?>
                </a>
            </div>
        </div>
    </div>

    <div v-else >
        <button v-on:click="backtolisting()" type="button"><?php _e('Back to Listing', 'idea-management'); ?></button>
        <br/>
        <form action="" @submit.prevent="formSubmit()" method="post" enctype="multipart/form-data">
            <div :class="style.row">
                <label for="title"><?php _e('Idea Title', 'idea-management'); ?></label>
                <input type="text" ref="title" class="form-control" name="title">
            </div>
            <div :class="style.row">
                <label for="content"><?php _e('Details', 'idea-management'); ?></label>
                <textarea name="content" ref="content" id="content" cols="30" rows="10">{{message}}</textarea>
            </div>
            <div :class="style.row">
                <label for="idea_type"><?php _e('Category', 'idea-management'); ?></label>
                <select name="idea_type" ref="idea_type" id="idea_type" class="form-control">
                    <option v-for="idea in idea_types" v-bind:value="idea.term_id">{{idea.name}}</option>
                </select>
            </div>
            <div :class="style.row">
                <input type="file" name="file_upload" id="file_upload" v-on:change="handleFileUpload()" ref="file">
            </div>
            <div :class="style.row">
                <input type="submit" value="<?php _e('Submit', 'idea-management'); ?>">
            </div>
        </form>
    </div>
</div>