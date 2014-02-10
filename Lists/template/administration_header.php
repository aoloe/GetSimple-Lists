<div style="width:100%;margin:0 -15px -15px -10px;padding:0px;">
    <h3 class="floated"><?= i18n_r('Lists/TITLE_LISTS_SETTINGS') ?></h3>
    <div class="edit-nav clearfix" style="">
        <?php foreach ($navigation as $item) : ?>
        <a href="load.php?id=<?= $plugin_id ?>&<?= $plugin_id ?>_administration=<?= $item['action'] ?>"<?= $navigation_current == $item['action'] ? ' class="current"' : '' ?>><?= $item['label'] ?></a>
        <?php endforeach; ?>
    </div> 
</div>
</div>

<?= $message ?>

<div class="main" style="margin-top:-10px;">

