<script type="text/javascript">

// При клике на удаление метрики со страницы
$(document).on('click', '.metric-delete-button', function() {

    // Находим id элемента в родителе
    let id = $(this).attr('id').split('-')[1];
    // alert(id);
    //
    // Удаляем элемент со страницы
    $('#table-metrics-' + id).remove();
    // Убираем отмеченный чекбокс в списке метрик
    $('#metric-' + id).prop('checked', false);

    // $.post('/admin/ajax_delete_relation_metric', {
    //     id: id,
    //     entity: 'goods_categories',
    //     entity_id:
    //     goods_category_id
    // }, function(data){
    //     if (data == true) {
    //         // Удаляем элемент со страницы
    //         $('#table-metrics-' + id).remove();
    //         // Убираем отмеченный чекбокс в списке метрик
    //         $('#metric-' + id).prop('checked', false);
    //     } else {
    //         alert(data);
    //     };
    // })
});

</script>