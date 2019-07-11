<script type="application/javascript">
    // Мягкое удаление с refresh
    $(document).on('click', '[data-open="delete-composition"]', function() {
        // находим описание сущности, id и название удаляемого элемента в родителе
        var parent = $(this).closest('.item');
        var type = parent.attr('id').split('-')[0];
        $('.title-composition').text(parent.data('name'));
        // $('.delete-button').attr('id', 'del-' + type + '-' + id);
        $('.composition-delete-button').attr('id', 'delete_metric-' + parent.attr('id').split('-')[1]);
    });

    // При клике на удаление метрики со страницы
    $(document).on('click', '.composition-delete-button', function() {

        // Находим id элемента в родителе
        var id = $(this).attr('id').split('-')[1];
        // alert(id);

        $.post('/admin/ajax_delete_relation_composition', {id: id, goods_category_id: goods_category_id}, function(data){
            if (data == true) {
                // Удаляем элемент со страницы
                $('#compositions-' + id).remove();
                // Убираем отмеченный чекбокс в списке метрик
                $('#add-composition-' + id).prop('checked', false);
            } else {
                alert(data);
            };
        })
    });
</script>