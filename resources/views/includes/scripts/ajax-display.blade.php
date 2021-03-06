<script type="application/javascript">

    // Меняем режим отображения
    $(document).on('click', '[data-open="item-display"]', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];
        // let entity_alias = $('#content').data('entity-alias');
        let entity_alias = item.closest('.item').attr('id').split('-')[0];

        let action = item.hasClass("icon-display-hide") ? 1 : 0;

        // alert(id + ' ' + action + ' ' + entity_alias);

        // Ajax
        $.post('/admin/display', {
            id: id,
            action: action,
            entity_alias: entity_alias
        }, function (display) {
            // Если нет ошибки
            if (display == true) {
                if (action == 1) {
                    item.removeClass('icon-display-hide');
                    item.addClass('icon-display-show')
                } else {
                    item.removeClass('icon-display-show');
                    item.addClass('icon-display-hide')
                }
            } else {
                // Выводим ошибку на страницу
                alert(display);
            };
        });
    });
</script>
