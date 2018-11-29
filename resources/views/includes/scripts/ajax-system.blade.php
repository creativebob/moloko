<script type="text/javascript">

    // Меняем режим отображения
    $(document).on('click', '[data-open="item-system"]', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];
        let entity_alias = item.closest('.item').attr('id').split('-')[0];

        var parent = item.closest('.controls-list');
        var nested = item.data('nested');

        action = item.hasClass("icon-system-unlock") ? 'lock' : 'unlock';

        // Ajax
        $.post('/admin/system_item', {id: id, action: action, entity_alias: entity_alias}, function (system) {
            // Если нет ошибки
            if (system == true) {
                if (action == 'lock') {
                    item.removeClass('icon-system-unlock');
                    item.addClass('icon-system-lock');
                    parent.siblings('.actions-list').find('.del').html('');
                } else {
                    item.removeClass('icon-system-lock');
                    item.addClass('icon-system-unlock');
                    if (nested == 0) {
                        parent.siblings('.actions-list').find('.del').html('<div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>');
                    };
                }
            } else {
                // Выводим ошибку на страницу
                alert(system);
            };
        });
    });
</script>