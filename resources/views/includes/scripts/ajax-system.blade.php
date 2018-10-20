<script type="text/javascript">

// Меняем режим отображения
$(document).on('click', '[data-open="item-system"]', function(event) {

    // Блочим отправку формы
    event.preventDefault();
    var entity_alias = $(this).closest('.item').attr('id').split('-')[0];
    var id = $(this).closest('.item').attr('id').split('-')[1];
    var item = $(this);

    if ($(this).hasClass("icon-system-unlock")) {
        var action = 'lock';
    } else {
        var action = 'unlock';
    }

    // Ajax
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/system_item',
        type: "POST",
        data: {id: id, action: action, entity_alias: entity_alias},
        success: function (date) {
            var result = $.parseJSON(date);

            // Если нет ошибки
            if (result.error_status == 0) {
                if (action == 'lock') {
                    item.removeClass('icon-system-unlock');
                    item.addClass('icon-system-lock')
                } else {
                    item.removeClass('icon-system-lock');
                    item.addClass('icon-system-unlock')
                }
            } else {
                // Выводим ошибку на страницу
                alert(result.error_message);
            };
        }
    });
});
</script> 