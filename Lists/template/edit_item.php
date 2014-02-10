    <tr<?= ($sortable == '' ? ' class="sortable"' : '') ?>">
        <td>
            <a href="load.php?id=<?= $plugin_id ?>&Lists_edit=edit&Lists_id=<?= $list_id ?>&Lists_list_id=<?= $list_list_id ?>" title="Edit <?= $lists_title ?>: <?= $label ?>">
            <?= $label ?>
            </a>
        </td>
        <td style="text-align: right;">
            <span><?= $date_change ?></span>
        </td>
        <td class="switch_visible">
            <a href="load.php?id=<?= $plugin_id ?>&Lists_edit=visible&Lists_id<?= $id ?>" class="switch_visible" style="text-decoration:none; color:<?= $visible? '#333333' : '#acacac'?>;" title="<?= i18n('Lists/Switch item visibility:') ?> <?= $visible ? i18n('Lists/YES') : i18n('Lists/NO') ?>">V</a>
        </td>
        <td class="switch_promo">
            <a href="load.php?id=<?= $plugin_id ?>&Lists_edit=promotion&Lists_id<?= $id ?>" class="switch_promo" style="text-decoration:none; color:<?= $promotion? '#333333' : '#acacac'?>;" title="<?= i18n('Lists/Switch item promotion:') ?> <?= $promotion ? i18n('Lists/YES') : i18n('Lists/NO') ?>">P</a>
        </td>
        <td class="delete">
            <a href="load.php?id=item_manager&delete=<?php echo $id; ?>" class="delete" title="<?= i18n('Lists/Delete item') ?>: <?= $label; ?>">
            X
            </a>
        </td>
    </tr>

