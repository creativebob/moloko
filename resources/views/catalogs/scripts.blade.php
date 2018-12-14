<script type="text/javascript">
    $(function () {

        // Берем алиас сайта
        var site_alias = '{{ $site->alias }}';

        // ------------------- Проверка на совпадение --------------------------------------
        function checkField (check, field = null) {

            var item = check;
            var value = item.val();
            var id = item.closest('form').find('#item-id').val();
            var submit = item.closest('form').find('.button');
            field = field != null ? field : item.attr('name');

            // Если символов больше 3 - делаем запрос
            if (value.length > 3) {
                // alert(value + ', ' + field + ', ' + entity_alias + ', ' + id);

                // Сам ajax запрос
                $.ajax({
                    url: '/admin/sites/' + site_alias + '/catalog_check',
                    type: "POST",
                    data: {value: value, field: field, id: id},
                    beforeSend: function () {
                        item.siblings('.find-status').addClass('icon-load');
                    },
                    success: function(count){
                        item.siblings('.find-status').removeClass('icon-load');

                    // Состояние ошибки
                    if (count > 0) {
                        item.siblings('.item-error').show();
                    } else {
                        item.siblings('.item-error').hide();
                    };

                    // Состояние кнопки
                    $(submit).prop('disabled', item.closest('form').find($(".item-error:visible")).length > 0);
                }
            });
            } else {
                item.siblings('.item-error').hide();
                $(submit).prop('disabled', item.closest('form').find($(".item-error:visible")).length > 0);
            };
        };

        // Проверка существования
        $(document).on('keyup', '.check-field', function() {
            var check = $(this);

            let timerId;
            clearTimeout(timerId);
            timerId = setTimeout(function() {
                checkField(check);
            }, 300);
        });

    });
</script>


