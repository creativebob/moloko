<script type="text/javascript">
    $(function() {

        function checkFilials() {
            if ($('.filial-checkbox:checked').length >= 1) {
                // Если 1 или более, разблокируем кнопку
                $('.site-button').prop('disabled', false);
                $('#filial-error').hide();
            } else {
                $('.site-button').prop('disabled', true);
                $('#filial-error').show();
            };
        };

        // Смотрим фмлмалы при загрузке
        checkFilials();

        // Смотрим филиалы при клике
        $(document).on('click', '.filial-checkbox', function () {
            checkFilials();
        });
    });
</script>

