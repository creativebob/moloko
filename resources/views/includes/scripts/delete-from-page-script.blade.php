<script type="application/javascript">
// Мягкое удаление с ajax
$(document).on('click', '[data-open="item-delete-ajax"]', function() {

  // Находим описание сущности, id и название удаляемого элемента в родителе
  var parent = $(this).closest('.item');
  var entity_alias = parent.attr('id').split('-')[0];
  var id = parent.attr('id').split('-')[1];
  var name = parent.data('name');
  $('.title-delete').text(name);
  $('.delete-button-ajax').attr('id', 'del-' + entity_alias + '-' + id);
});

// Подтверждение удаления и само удаление
$(document).on('click', '.delete-button-ajax', function(event) {

  // Блочим отправку формы
  event.preventDefault();
  var entity_alias = $(this).attr('id').split('-')[1];
  var id = $(this).attr('id').split('-')[2];

  $('#' + entity_alias + '-' + id).remove();
});
</script> 