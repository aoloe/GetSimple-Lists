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
    <input type="hidden" name="Lists_settings" value="edit" />
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
            <label for="lists_item_page"><?= i18n_r('Lists/SETTINGS_FORM_ONPAGE') ?></label>
            <select id="lists_item_page" name="lists_item_page" class="text" style="width:250px">
              <option value=""></option>
              <?php foreach ($page_list as $key => $value) : ?>
              <option value="<?php echo htmlspecialchars($key); ?>"<?= $key == $page ? ' selected="selected"' : '' ?> ><?php echo htmlspecialchars($value['title']).' ('.htmlspecialchars($value['slug']).')'; ?></option>  
              <?php endforeach; ?>
            </select>
        </p>
    </div>
    <div class="rightsec">
        <p class="inline">
            <input type="checkbox" id="lists_item_frontend_create" name="lists_item_frontend_create" class="text"<?= $frontend_create ? ' checked="checked"' : '' ?> />
            <label for="lists_item_frontend_create"><?= i18n_r('Lists/SETTINGS_FORM_FRONTENDCREATE') ?></label>
            <?php /*
            <p class="inline" ><input name="show_htmleditor" id="show_htmleditor" type="checkbox" value="1" <?php echo $editorchck; ?> /> &nbsp;<label for="show_htmleditor" ><?php i18n('ENABLE_HTML_ED');?></label></p>
            */ ?>
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
    <?php if (!empty($id)) : ?>
        <span>
            <input class="submit" type="submit" name="delete" value="<?= i18n_r('Lists/FORM_LABEL_DELETE') ?>" />
        </span>
    <?php endif; ?>
    </p>
    <?= $content_fields ?>
    <p>
        <span>
            <input class="submit" type="submit" name="save" value="<?= i18n_r('Lists/FORM_LABEL_SAVE') ?>" />
        </span>
    </p>
</form>
