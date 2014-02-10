<h3><?= sprintf(i18n_r('Lists/EDIT_LISTITEMS_TITLE'), $title) ?></h3>

<table id="ListsList" class="edittable highlight">
    <tbody>
<?= $rows ?>
  <tr>
    <td colspan="4"><a href="load.php?id=<?= $plugin_id ?>&Lists_id=<?= $list_id ?>&Lists_edit=new" title="<?php i18n('Lists/FORM_ADD'); ?>" class="add"><?php i18n('Lists/FORM_ADD'); ?></a></td>
    <td class="secondarylink"><a href="load.php?id=<?= $plugin_id ?>&Lists_edit=new" class="add" title="<?php i18n('Lists/FORM_ADD'); ?>">+</a></td>
  </tr>
</tbody>
</table>
<p><?= sprintf(i18n_r('Lists/EDIT_LISTITEMS_COUNT'), $rows_count, $title) ?></p>';

<?php
// TODO: put this javascript code at the right place...
?>
<script type="text/javascript">
<?php
// TODO: check if this renumbering does the right thing... or if it's the better way to do this
?>
  function renumberCustomFields() {
    $('#ListsList tbody tr').each(function(i,tr) {
      $(tr).find('input, select, textarea').each(function(k,elem) {
        var name = $(elem).attr('name').replace(/_\d+_/, '_'+(i)+'_');
        $(elem).attr('name', name);
      });
    });
  }
  $(function() {
    $('a.delete').click(function(e) {
      $(e.target).closest('tr').remove();
      renumberCustomFields();
    });
    $('#ContentFieldsList tbody').sortable({
      items:"tr.sortable", handle:'td',
      update:function(e,ui) { renumberCustomFields(); }
    });
    renumberCustomFields();
  });
</script>
