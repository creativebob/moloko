<script type="application/javascript">
    $(function() {

        function checkFilials() {
            if ($('.checkbox-filial:checked').length >= 1) {
                // Если 1 или более, разблокируем кнопку
                $('.domain-button').prop('disabled', false);
                $('#filial-error').hide();
            } else {
                $('.domain-button').prop('disabled', true);
                $('#filial-error').show();
            };
        };

        // Смотрим фмлмалы при загрузке
        checkFilials();

        // Смотрим филиалы при клике
        $(document).on('click', '.checkbox-filial', function () {
            checkFilials();
        });
    });
</script>

