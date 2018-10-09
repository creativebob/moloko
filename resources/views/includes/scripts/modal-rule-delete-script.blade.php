<script type="text/javascript">

// Мягкое удаление
$(document).on('click', '[data-open="delete-rule"]', function() {
    // находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.item');
    var type = parent.attr('id').split('-')[0];
    var id = parent.attr('id').split('-')[1];
    var name = parent.data('name');
    $('.title-rule').text(name);
    // $('.delete-button').attr('id', 'del-' + type + '-' + id);
    $('.rule-delete-button').attr('id', 'delete_rule-' + id);
});

// При клике на удаление метрики со страницы
$(document).on('click', '.rule-delete-button', function() {

    // Находим id элемента в родителе
    var id = $(this).attr('id').split('-')[1];

    // alert(id);

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/rule_delete',
        type: 'POST',
        data: {id: id},
        success: function(date){

            var result = $.parseJSON(date);
            // alert(result);

            if (result['error_status'] == 0) {

                // Удаляем элемент со страницы
                $('#rules-' + id).remove();

            } else {
                alert(result['error_message']);
            }; 
        }
    })
});
</script> 