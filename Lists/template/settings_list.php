<h3><?= i18n_r('Lists/SETTINGS_TITLE_ALLLISTS') ?></h3>
<?php if (empty($list)) : ?>
<p><?= i18n_r('Lists/SETTINGS_TITLE_NOLISTS') ?>.</p>
<?php else : ?>
<table class="highlight">
</table>
<?php endif; ?>
