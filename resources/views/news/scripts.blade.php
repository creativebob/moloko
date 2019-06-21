<script>



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







