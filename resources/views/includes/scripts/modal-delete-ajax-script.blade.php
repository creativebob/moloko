<script type="text/javascript">
// Мягкое удаление с ajax
$(document).on('click', '[data-open="item-delete-ajax"]', function() {
  // находим описание сущности, id и название удаляемого элемента в родителе
  var parent = $(this).closest('.item');
  var type = parent.attr('id').split('-')[0];
  var id = parent.attr('id').split('-')[1];
  var name = parent.data('name');
  $('.title-delete').text(name);
  $('.delete-button-ajax').attr('id', 'del-' + type + '-' + id);
});
// Подтверждение удаления и само удаление
$(document).on('click', '.delete-button-ajax', function(event) {
  // Блочим отправку формы
  event.preventDefault();
  var type = $(this).attr('id').split('-')[1];
  var id = $(this).attr('id').split('-')[2];
  // Ajax
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: '/' + type + '/' + id,
    type: "DELETE",
    data: {id: id},
    success: function (data) {
      var result = $.parseJSON(data);
      if (result.status == 0) {
        $('#' + result.type + '-' + result.id).remove();
        $('.delete-button').removeAttr('id');
      } else {
        alert(result.msg);
      };
    }
  });
});
</script> 