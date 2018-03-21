<script type="text/javascript">
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

  // Ajax
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: '/' + entity_alias + '/' + id,
    type: "DELETE",
    success: function (html) {
      $('#' + entity_alias).html(html);
      Foundation.reInit($('#' + entity_alias));
      $('.delete-button-ajax').removeAttr('id');
    }
  });
});
</script> 