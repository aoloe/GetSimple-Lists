<?php function value($value) { echo ($value == '' ? '' : ' value = "'.$value.'"'); } ?>
<style>
.widesec input.text {
	width: 92%;
}	
.widesec select.text {
	width: 96%;
}
</style>
<h3><?= $list_name == '' ? i18n_r('Lists/SETTINGS_TITLE_NEWLIST') : $list_name ?></h3>

<form class="largeform" action="load.php?id=<?= $plugin_id ?>&<?= $plugin_id ?>_settings=new" method="post" accept-charset="utf-8">
    <input type="hidden" name="lists_item_id" value="<?php echo $id; ?>" />
    <div>
        <p>
            <label for="lists_item_title"><?= i18n_r('Lists/FORM_LABEL_TITLE') ?></label>
            <input type="text" class="text" name="lists_item_title"<?= value($title) ?> />
        </p>
    </div>
    <div class="clear"></div>
    <div class="leftsec">
        <p>
            <label for="page-url"><?= i18n_r('Lists/Show list on frontend page') ?></label>
            <select id="post-parent" name="post-parent" class="text" style="width:250px">
              <option value=""></option>
              <?php /* foreach ($pages as $slug => $title) { ?>
              <option value="<?php echo htmlspecialchars($slug); ?>" <?php if ($slug == @$def['parent']) echo 'selected="selected"'; ?> ><?php echo htmlspecialchars($title).' ('.htmlspecialchars($slug).')'; ?></option>  
              <?php } */ ?>
            </select>
        </p>
    </div>
    <div class="rightsec">
        <p>
            <label for="lists_item_name"><?= i18n_r('Lists/Create new items on frontend page') ?></label>
            <select id="post-parent" name="post-parent" class="text" style="width:250px">
              <option value=""></option>
              <?php /* foreach ($pages as $slug => $title) { ?>
              <option value="<?php echo htmlspecialchars($slug); ?>" <?php if ($slug == @$def['parent']) echo 'selected="selected"'; ?> ><?php echo htmlspecialchars($title).' ('.htmlspecialchars($slug).')'; ?></option>  
              <?php } */ ?>
            </select>
        </p>
    </div>
    <?php if (!empty($field_editable)) : ?>
    <div class="leftsec">
        <p>
            <label for="page-url"><?= i18n_r('Editable fields in the frontend:') ?></label>
            <input type="checkbox" name="List_editable_fields[<?= 'abcd' ?>]"<?= $field_editable['abcd'] ? ' checked="checked"' : ''; ?> />
        </p>
    </div>
    <?php endif; ?>
    <div class="clear"></div>
    <p>
        <span>
            <input class="submit" type="submit" name="save" value="<?= i18n_r('Lists/FORM_LABEL_SAVE') ?>" />
        </span>
    </p>
</form>
</form>
