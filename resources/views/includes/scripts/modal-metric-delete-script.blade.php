<script type="text/javascript">

// При клике на удаление метрики со страницы
$(document).on('click', '.metric-delete-button', function() {

    // Находим id элемента в родителе
    var id = $(this).attr('id').split('-')[1];
    var set_status = $(this).attr('id').split('-')[2];
    // alert(id + ' ' + set_status);

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/ajax_delete_relation_metric',
        type: 'POST',
        data: {id: id, entity: 'goods_categories', entity_id: goods_category_id, set_status: set_status},
        success: function(date){
            var result = $.parseJSON(date);
            // alert(result['error_status']);
            if (result['error_status'] == 0) {
                // Удаляем элемент со страницы
                $('#' + set_status + '-table-metrics-' + id).remove();
                // Убираем отмеченный чекбокс в списке метрик
                $('#' + set_status + '-metric-' + id).prop('checked', false);
            } else {
                alert(result['error_message']);
            };
        }
    })
});

</script>