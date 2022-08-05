<div id="im_form">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            <input type="text" class="form-control" name="title">
        </div>
        <div class="row">
            <textarea name="content" id="content" cols="30" rows="10">{{message}}</textarea>
        </div>
        <div class="row">
            <select name="idea_type" id="idea_type" class="form-control">
                <option value=""><?php __('Select Idea Type', 'idea-management'); ?></option>
            </select>
        </div>
    </form>
</div>