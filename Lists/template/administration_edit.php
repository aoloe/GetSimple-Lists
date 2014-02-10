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

<form class="largeform" action="load.php?id=<?= $plugin_id ?>" method="post" accept-charset="utf-8">
    <input type="hidden" name="Lists_administration" value="edit" />
    <input type="hidden" name="Lists_id" value="<?= $id ?>" />
    <div>
        <p>
            <label for="lists_list_title"><?= i18n_r('Lists/FORM_LABEL_TITLE') ?></label>
            <input type="text" class="text" name="lists_list_title"<?= value($title) ?> />
        </p>
    </div>
    <div class="clear"></div>
    <div class="leftsec">
        <p>
            <label for="lists_list_page"><?= i18n_r('Lists/SETTINGS_FORM_ONPAGE') ?></label>
            <select id="lists_list_page" name="lists_list_page" class="text" style="width:250px">
              <option value=""></option>
              <?php foreach ($page_list as $key => $value) : ?>
              <option value="<?= htmlspecialchars($key) ?>"<?= $key == $page ? ' selected="selected"' : '' ?> ><?= htmlspecialchars($value['title']).' ('.htmlspecialchars($value['slug']).')' ?></option>  
              <?php endforeach; ?>
            </select>
        </p>
    </div>
    <div class="rightsec">
        <p>
            <label for="lists_list_label"><?= i18n_r('Lists/SETTINGS_FORM_FIELD_AS_LABEL') ?></label>
            <select id="lists_list_label" name="lists_list_label" class="text" style="width:250px">
              <option value=""></option>
              <?php foreach ($field_list as $key => $value) : ?>
              <option value="<?= htmlspecialchars($key) ?>"<?= $key == $field_as_label ? ' selected="selected"' : '' ?> ><?= htmlspecialchars($value) ?></option>  
              <?php endforeach; ?>
            </select>
        </p>
    </div>
    <div class="clear"></div>
    <div class="leftsec">
        <p>
            <label for="lists_list_order"><?= i18n_r('Lists/SETTINGS_FORM_FIELD_FOR_ORDER') ?></label>
            <select id="lists_list_order" name="lists_list_order" class="text" style="width:250px">
              <option value=""></option>
              <?php foreach ($field_list as $key => $value) : ?>
              <option value="<?= htmlspecialchars($key) ?>"<?= $key == $field_for_order ? ' selected="selected"' : '' ?> ><?= htmlspecialchars($value) ?></option>  
              <?php endforeach; ?>
            </select>
        </p>
    </div>
    <div class="rightsec">
        <p>
            <label for="lists_list_multipagetag"><?= i18n_r('Lists/SETTINGS_FORM_FIELD_AS_MULTIPAGETAG') ?></label>
            <select id="lists_list_multipagetag" name="lists_list_multipagelabel" class="text" style="width:250px">
              <option value=""></option>
              <?php foreach ($field_list as $key => $value) : ?>
              <option value="<?= htmlspecialchars($key) ?>"<?= $key == $field_for_multipagetag ? ' selected="selected"' : '' ?> ><?= htmlspecialchars($value) ?></option>  
              <?php endforeach; ?>
            </select>
        </p>
    </div>
    <div class="clear"></div>
    <div class="leftsec">
        <p class="inline">
            <input type="checkbox" id="lists_list_frontend_create" name="lists_list_frontend_create" class="text"<?= $frontend_create ? ' checked="checked"' : '' ?> />
            <label for="lists_list_frontend_create"><?= i18n_r('Lists/SETTINGS_FORM_FRONTENDCREATE') ?></label>
            <?php /*
            <p class="inline" ><input name="show_htmleditor" id="show_htmleditor" type="checkbox" value="1" <?= $editorchck ?> /> &nbsp;<label for="show_htmleditor" ><?= i18n_r('ENABLE_HTML_ED')?></label></p>
            */ ?>
        </p>
        <p class="inline">
            <input type="checkbox" id="lists_list_frontend_append" name="lists_list_frontend_append" class="text"<?= $frontend_append ? ' checked="checked"' : '' ?> />
            <label for="lists_list_frontend_append"><?= i18n_r('Lists/SETTINGS_FORM_FRONTENDAPPEND') ?></label>
        </p>
    </div>
    <?php if (!empty($field_editable)) : // TODO: always show it, but hide it if it's not editable? ?>
    <div class="rightsec">
        <p>
            <label for="page-url"><?= i18n_r('Editable fields in the frontend:') ?></label>
            <input type="checkbox" name="List_editable_fields[<?= 'abcd' ?>]"<?= $field_editable['abcd'] ? ' checked="checked"' : '' ?> />
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
