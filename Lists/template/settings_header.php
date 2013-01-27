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
    <?php // TODO: use a php function to show this block ?>
    $('div.bodycontent').before('<div class="<?= $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?= json_encode(implode("<br />\n", $message)) ?>+'</div>');
    $(".updated, .error").fadeOut(500).fadeIn(500);
<?php endif; ?>
});
</script>

<div class="main" style="margin-top:-10px;">

