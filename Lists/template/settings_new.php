<h3><?= i18n_r('Lists/SETTINGS_TITLE_NEWLIST') ?></h3>

<form class="largeform" action="load.php?id=<?= $plugin_id ?>&settings&<?= $plugin_id ?>_settings" method="post" accept-charset="utf-8">
    <div class="leftsec">
        <p>
            <label for="page-url"><?= i18n_r('Lists/SETTINGS_LABEL_ID') ?></label>
            <input type="text" class="text" name="item-id" value="<?php echo $id; ?>" />
        </p>
    </div>
    <div class="rightsec">
        <p>
            <label for="lists_item_name"><?= i18n_r('Lists/SETTINGS_LABEL_NAME') ?></label>
            <input type="text" class="text" name="lists_item_name" value="<?php echo $name; ?>" />
        </p>
    </div>
    <div class="clear"></div>
    <p>
        <span>
            <input class="submit" type="submit" name="settings_edit" value="Submit Settings" />
        </span>
    </p>
</form>
</form>
