<script>

    goods = new Goods();

    // Чекбоксы
    $(document).on('click', "#dropdown-goods :checkbox", function() {
        goods.change(this);
    });

    // Удаление состав со страницы
    // Открываем модалку
    $(document).on('click', "#table-goods a[data-open=\"delete-item\"]", function() {
        goods.openModal(this);
    });

    // Удаляем
    $(document).on('click', '.item-delete-button', function() {
        let id = $(this).attr('id').split('-')[1];
        goods.delete(id);
    });

    // При клике на свойство отображаем или скрываем его состав
    $(document).on('click', '.parent', function() {
        // Скрываем все состав
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });

    // $(document).on('change', ".goods-value", function() {
    //     goods.fill(this);
    // });

</script>