<div id="im_form">


    <form action="" @submit.prevent="formSubmit()" method="post" enctype="multipart/form-data">
        <div :class="style.row">
            <input type="text" ref="title" class="form-control" name="title">
        </div>
        <div :class="style.row">
            <textarea name="content" ref="content" id="content" cols="30" rows="10">{{message}}</textarea>
        </div>
        <div :class="style.row">
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