<script type="text/javascript">

    $(document).ready(function() {
        // Мульти Select
        $(".chosen-select").chosen({
            width: "95%"
        });

        $(function() {
            $('.checkboxer-title .form-error').hide();
        });

        // Валидация группы чекбоксов
        $(document).on('click', '.checkbox-group :checkbox', function() {
            let id = $(this).closest('.dropdown-pane').attr('id');
            if ($(this).closest('.checkbox-group').find(":checked").length == 0) {
                $('div[data-toggle=' + id + ']').find('.form-error').show();
            } else {
                $('div[data-toggle=' + id + ']').find('.form-error').hide();
            };
        });

        // Валидация при клике на кнопку
        $(document).on('click', '#add-item', function(event) {

            $('#form-edit').foundation('validateForm');

            // Проверка выбора значения чекбокса списка метрик
            let metricError = 0;
            $(".checkbox-group").each(function(i) {
                if ($(this).find("input:checkbox:checked").length == 0) {
                    let id = $(this).closest('.dropdown-pane').attr('id');
                    $('div[data-toggle=' + id + ']').find('.metric-list-error').show();
                    metricError = metricError + 1;
                };
            });

            if (metricError > 0) {
                event.preventDefault();
                // alert('метрики');
            }

            // Проверка заполнения составов
            let compositionError = 0;
            $("#table-compositions .composition_value").each(function(i) {
                if ($(this).val() == '') {
                    // $(this).siblings('.form-error').show();
                    compositionError = compositionError + 1;
                }
            });

            if (compositionError > 0) {
                event.preventDefault();
                $('#composition-error').text('Заполните все значения состава').show();
                // alert('состав');
            } else {
                $('#composition-error').hide();
            }

            // $(this).trigger('click');
        });
    });

</script>


