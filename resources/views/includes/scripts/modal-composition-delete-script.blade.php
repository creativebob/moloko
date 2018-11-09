<script type="text/javascript">
// Мягкое удаление с refresh
$(document).on('click', '[data-open="delete-composition"]', function() {
  // находим описание сущности, id и название удаляемого элемента в родителе
  var parent = $(this).closest('.item');
  var type = parent.attr('id').split('-')[0];
  var id = parent.attr('id').split('-')[1];
  var name = parent.data('name');
  $('.title-composition').text(name);
  // $('.delete-button').attr('id', 'del-' + type + '-' + id);
  $('.composition-delete-button').attr('id', 'delete_metric-' + id);
});

// При клике на удаление метрики со страницы
  $(document).on('click', '.composition-delete-button', function() {

    // Находим id элемента в родителе
    var id = $(this).attr('id').split('-')[1];

    // alert(id);

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/ajax_delete_relation_composition',
      type: 'POST',
      data: {id: id, goods_category_id: goods_category_id},
      success: function(date){

        var result = $.parseJSON(date);
        // alert(result);

        if (result['error_status'] == 0) {

            // Удаляем элемент со страницы
            $('#compositions-' + id).remove();

            // Убираем отмеченный чекбокс в списке метрик
            $('#add-composition-' + id).prop('checked', false);
            
          } else {
            alert(result['error_message']);
          }; 
        }
      })
  });


</script> 