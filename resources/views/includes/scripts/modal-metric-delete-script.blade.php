<script type="text/javascript">

// Мягкое удаление с refresh
$(document).on('click', '[data-open="delete-metric"]', function() {
    // находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.item');
    var type = parent.attr('id').split('-')[0];
    var id = parent.attr('id').split('-')[1];
    var set_status = parent.attr('id').split('-')[2];
    var name = parent.data('name');
    $('.title-metric').text(name);
    // $('.delete-button').attr('id', 'del-' + type + '-' + id);
    $('.metric-delete-button').attr('id', 'delete_metric-' + id + '-' + set_status);
});

// При клике на удаление метрики со страницы
$(document).on('click', '.metric-delete-button', function() {

    // Находим id элемента в родителе
    var id = $(this).attr('id').split('-')[1];
    var set_status = $(this).attr('id').split('-')[2];

    // alert(id);

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/ajax_delete_relation_metric',
        type: 'POST',
        data: {id: id, entity: 'goods_categories', entity_id: goods_category_id},
        success: function(date){

            var result = $.parseJSON(date);
            // alert(result);

            if (result['error_status'] == 0) {

                // Удаляем элемент со страницы
                $('#metrics-' + id + '-' + set_status).remove();

                // В случае успеха обновляем список метрик
                // $.ajax({
                //   headers: {
                //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //   },
                //   url: '/products/' + product_id + '/edit',
                //   type: 'GET',
                //   data: $('#product-form').serialize(),
                //   success: function(html){
                //     // alert(html);
                //     $('#properties-dropdown').html(html);
                //   }
                // })

                // Убираем отмеченный чекбокс в списке метрик
                $('#' + set_status + '-add-metric-' + id).prop('checked', false);

            } else {
                alert(result['error_message']);
            };
        }
    })
});

</script>