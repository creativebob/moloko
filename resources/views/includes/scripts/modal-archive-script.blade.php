<script type="text/javascript">
// Мягкое удаление с refresh
$(document).on('click', '[data-open="item-archive"]', function() {
  // находим описание сущности, id и название удаляемого элемента в родителе
  var parent = $(this).closest('.item');
  var type = parent.attr('id').split('-')[0];
  var id = parent.attr('id').split('-')[1];
  var name = parent.data('name');
  $('.title-archive').text(name);
  // $('.delete-button').attr('id', 'del-' + type + '-' + id);
  $('#form-item-del').attr('action', '/admin/' + type + '/archive/' + id);
});
</script> 