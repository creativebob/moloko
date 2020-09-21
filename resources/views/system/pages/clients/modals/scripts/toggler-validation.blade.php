<script type="application/javascript">

    $('#wrap-company-private').on('on.zf.toggler', function() {
        $('#title-switch-company-private').text("Компания");
        $('#private_status').val(1);

        // Включаем обязательное заполнение
        $('[name=company_name]').attr('required', 'required');
        $('[name=inn]').attr('required', 'required');

        $('[name=passport_number]').removeAttr('required');
        $('[name=passport_date]').removeAttr('required');
        $('[name=passport_released]').removeAttr('required');
        // alert('На компанию');
        // $('.passport_address').removeAttr('required');

    });

    $('#wrap-company-private').on('off.zf.toggler', function() {
    
        $('#title-switch-company-private').text("Физическое лицо");
        $('#private_status').val(null);

        // Включаем обязательное заполнение
        $('[name=passport_number]').attr('required', 'required');
        $('[name=passport_date]').attr('required', 'required');
        $('[name=passport_released]').attr('required', 'required');
        // $('.passport_address').attr('required', 'required');

        // Выключаем обязательное заполнение
        $('[name=company_name]').removeAttr('required');
        $('[name=inn]').removeAttr('required');
        // alert('На юзера');

    });
    
</script>