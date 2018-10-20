<script type="text/javascript">

// Меняем режим отображения
$(document).on('click', '[data-open="item-display"]', function(event) {

    // Блочим отправку формы
    event.preventDefault();
    var entity_alias = $(this).closest('.item').attr('id').split('-')[0];
    var id = $(this).closest('.item').attr('id').split('-')[1];
    var item = $(this);

    if ($(this).hasClass("icon-display-hide")) {
        var action = 'show';
    } else {
        var action = 'hide';
    }

    // Ajax
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/display',
        type: "POST",
        data: {id: id, action: action, entity_alias: entity_alias},
        success: function (date) {
            var result = $.parseJSON(date);
            // Если нет ошибки
            if (result.error_status == 0) {
                if (action == 'show') {
                    item.removeClass('icon-display-hide');
                    item.addClass('icon-display-show')
                } else {
                    item.removeClass('icon-display-show');
                    item.addClass('icon-display-hide')
                }

            } else {
                // Выводим ошибку на страницу
                alert(result.error_message);
            };
        }
    });
});
</script> 