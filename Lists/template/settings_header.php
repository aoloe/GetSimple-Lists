<div style="width:100%;margin:0 -15px -15px -10px;padding:0px;">
    <h3 class="floated"><?= i18n_r('Lists/TITLE_LISTS_SETTINGS') ?></h3>
    <div class="edit-nav clearfix" style="">
        <?php foreach ($navigation as $item) : ?>
        <a href="load.php?id=<?= $plugin_id ?>&<?= $plugin_id ?>_settings=<?= $item['action'] ?>"<?= $navigation_current == $item['action'] ? ' class="current"' : '' ?>><?= $item['label'] ?></a>
        <?php endforeach; ?>
    </div> 
</div>
</div>

<script type="text/javascript">
$(function() {
<?php if (!empty($message)) : ?>
    <?php foreach ($message as $key => $value) : // TODO: use a php function to show this block ?>
        <?php if (!empty($value)) : ?>
        <?php $box_css = array(LISTSMESSAGE_MESSAGE => 'notify', LISTSMESSAGE_SUCCESS => 'updated', LISTSMESSAGE_WARNING => 'notify', LISTSMESSAGE_ERROR => 'error'); ?>
        $('div.bodycontent').before('<div class="<?= $box_css[$key] ?>" style="display:block;">'+<?= json_encode(implode("<br />\n", $value)) ?>+'</div>');
        <?php endif; ?>
    <?php endforeach; ?>
    $(".updated, .error").fadeOut(500).fadeIn(500);
<?php endif; ?>
});
</script>

<div class="main" style="margin-top:-10px;">

