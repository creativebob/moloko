<script>

    // Добавление альбомов
    $(document).on('click', '[data-open="album-add"]', function(event) {
        event.preventDefault();
        /* Act on the event */
        $.post("/admin/album_add", function(html){
            $('#modal').html(html).foundation();
            $('#album-add').foundation('open');
        });
    });

    $(document).on('change', '#select-albums_categories', function() {
        var id = $(this).val();
        // alert(id);

        if (id == 0) {
            $('#select-albums').html('');
            $('#select-albums').prop('disabled', true);
        } else {
            $.post("/admin/albums_select", {albums_category_id: id}, function(html){
                $('#select-albums').replaceWith(html);
            });
        }
    });

    // Добавление альбома
    $(document).on('click', '#submit-album-add', function(event) {
        // Блочим отправку формы
        event.preventDefault();
        $(this).prop('disabled', true);
        var form = $(this).closest('form');

        $.post('/admin/album_get', form.serialize(), function(html){
            $('#table-albums').append(html);
            form.closest('.reveal-overlay').remove();
        });
    });

    // Удаление альбома
    $(document).on('click', '.delete-button-ajax', function() {
        $('#item-delete-ajax').foundation('close');
    });

    // ------------------- Проверка на совпадение имени --------------------------------------

    // Берем алиас сайта
    var cur_news_id = '{{ $cur_news->id }}';

    function checkField (check) {

        var item = check;
        var value = item.val();
        var id = item.closest('form').find('#item-id').val();
        var submit = item.closest('form').find('.button');

        // Если символов больше 3 - делаем запрос
        if (value.length > 3) {
            // alert(value + ', ' + field + ', ' + entity_alias + ', ' + id);

            // Сам ajax запрос
            $.ajax({
                url: '/admin/news_check',
                type: "POST",
                data: {alias: value, id: cur_news_id},
                beforeSend: function () {
                    item.siblings('.find-status').addClass('icon-load');
                },
                success: function(count){
                    item.siblings('.find-status').removeClass('icon-load');

                    // Состояние ошибки
                    if (count > 0) {
                        item.siblings('.item-error').show();
                        $(submit).prop('disabled', true);
                    } else {
                        item.siblings('.item-error').hide();
                        $(submit).prop('disabled', false);
                    };
                }
            });
        } else {
            item.siblings('.item-error').hide();
            $(submit).prop('disabled', false);
        };
    };

    // Проверка существования
    $(document).on('keyup', '[name="alias"]', function() {
        var check = $(this);

        let timerId;
        clearTimeout(timerId);
        timerId = setTimeout(function() {
            checkField(check);
        }, 300);
    });

    // ---------------------------------- Закрытие модалки -----------------------------------
    $(document).on('click', '.icon-close-modal', function() {
        $(this).closest('.reveal-overlay').remove();
    });
</script>







