<h3><?= i18n_r('Lists/SETTINGS_TITLE_ALLLISTS') ?></h3>
<?php if (empty($list)) : ?>
<p><?= i18n_r('Lists/SETTINGS_TITLE_NOLISTS') ?>.</p>
<?php else : ?>
<table class="highlight">
    <tr>
        <th><?= i18n_r('Lists/SETTINGS_LISTHEADTITLE') ?></th>
        <th style="text-align:right;" ><span title="<?= i18n_r('Lists/SETTINGS_LISTHEADENTRIES') ?>">#</span></th>
        <th></th>
        <th></th>
    </tr>
<?php foreach ($list as $key => $value) : ?> 
    <tr>

        <td><a href="<?= $url_self ?>&Lists_id=<?= $key ?>"><?= $value ?></a></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
