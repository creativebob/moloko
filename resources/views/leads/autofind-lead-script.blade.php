<script type="application/javascript">
    // Обозначаем таймер для проверки
    var timerId;
    var time = 100;

    // Проверка существования
    $(document).on('keyup', '#phone', function() {

        // Получаем фрагмент текста
        var phone = $('#phone').val();

        // Выполняем запрос
        clearTimeout(timerId);   

        timerId = setTimeout(function() {

            autoFindPhone();

        }, time); 
    });

    function autoFindPhone() {

        // Получаем фрагмент текста
        var phone = $('#phone').val();
        var phone = phone.replace(/\D/g, "");

        // Смотрим сколько символов
        var len_phone = phone.length;
        var lead_id = $('#lead_id').data('lead-id');

        // alert(phone + ' ' + lead_id);

        // Если символов больше 3 - делаем запрос
        if(phone.length == 11) {

            $.ajax({

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/admin/leads/autofind/" + phone,
                type: "POST",
                data: {phone: phone, lead_id: lead_id},
                success: function(html){

                    // Выводим пришедшие данные на страницу
                    $('#port-autofind').html(html.view);
                    $('#port-autofind').show();
                    // alert(html.params);
                } 
            });

        };
    };

// 
  $(document).on('click', '#lead-name', function() {

    // Получаем данные на лида из строчки по которой кликнули
    var lead_name = $('#lead-name').text();
    var lead_city = $('#lead-city').text();
    var lead_address = $('#lead-address').text();
    var lead_city_id = $('#lead-city').data('city-id');

    $('input[name=name]').val(lead_name);
    $('input[name=address]').val(lead_address);

    if(lead_city == '- Город не указан -'){
        $('input[name=city_name]').val('');
        $('input[name=city_name]').focus();
    } else {
        $('input[name=city_id]').val(lead_city_id);
        $('input[name=city_name]').val(lead_city);
        $('#city-check').addClass('icon-find-ok sprite-16');        
    };


    $('#port-autofind').hide();

});

// Получение данных об истории обращений по клику на вкладку ИСТОРИЯ
  $(document).on('change.zf.tabs', '#tabs-extra-leads', function() {

        var tabId = $('div[data-tabs-content="'+$(this).attr('id')+'"]').find('.tabs-panel.is-active').attr('id');
        if(tabId == 'content-panel-history'){

            // Получаем фрагмент текста
            var phone = $('#phone').val();
            var phone = phone.replace(/\D/g, "");

            // Смотрим сколько символов
            var len_phone = phone.length;
            var lead_id = $('#lead_id').data('lead-id');

            // Если символов больше 3 - делаем запрос
            if(phone.length == 11) {

                $.ajax({

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/admin/leads/autofind/" + phone,
                    type: "POST",
                    data: {phone: phone, lead_id: lead_id},
                    success: function(html){

                        // Выводим пришедшие данные на страницу
                        $('#port-history').html(html);
                    } 
                });
            };


        };

});

</script>
