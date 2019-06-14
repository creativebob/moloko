<script type="text/javascript">

    $(function() {
        // Мульти Select
        // $(".chosen-select").chosen({
        //     width: "95%"
        // });

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

        // Добавление в прайс
        $(document).on('click', '#button-store-prices_service', function(event) {
            event.preventDefault();
            $.post('/admin/prices_service', $('#form-prices_service :input').serialize(), function(html) {
                $('#table-prices').append(html);
            });
        });

        // Мягкое удаление с refresh
        $(document).on('click', '[data-open="delete-item"]', function() {

            // находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var entity = parent.attr('id').split('-')[0];
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');
            $('.title-item').text(name);
            $('.item-delete-button').attr('id', entity + '-' + id);
        });

        $(document).on('click', '.item-delete-button', function(event) {
            event.preventDefault();

            var buttons = $('.button');
            var entity = $(this).attr('id').split('-')[0];
            var id = $(this).attr('id').split('-')[1];
            
            $.ajax({
                // headers: {
                //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                // },
                url: '/admin/' + entity,
                type: 'DELETE',
                data: {
                    id: id
                },
                success: function (data) {
                    if (data == true) {

                        $('#' + entity + '-' + id).remove();
                        // $('#item-delete-ajax').foundation('close');
                        $('.delete-button').removeAttr('id');
                        buttons.prop('disabled', false);
                    } else {
                        // Выводим ошибку на страницу
                        alert(data);
                    };


                }
            });  
        });



        $(document).on('change', '#select-catalogs', function() {
            $.post('/admin/catalogs_services/' + $(this).val() + '/get_catalogs_services_items', function(html) {
                $('#select-catalogs_items').html(html);
                checkPrice();
            });
        });

        $(document).on('change', '#select-catalogs_items', function() {
            checkPrice();
        });


        function checkPrice() {
            var catalogs_item_id = $('#select-catalogs_items').val();
            var filial_id = $('#select-filials').val();

            // alert(catalogs_item_id + ' ' + filial_id);

            var disabled = false;

            $("#table-prices tr").each(function(index) {

                if ($(this).data('filial_id') == filial_id && $(this).data('catalogs_item_id') == catalogs_item_id) {
                    disabled = true;
                } else {
                    if ($('#select-filials option[value=' + $(this).data('filial_id') + ']').prop('disabled') == true) {
                        $('#select-filials option[value=' + $(this).data('filial_id') + ']').prop('disabled', false);
                    }
                }
            });

            if (disabled == true) {
                // alert('ставим');
                $('#select-filials option[value=' + filial_id + ']').prop('disabled', true);
            } else {
                // alert('убираем');
                $('#select-filials option[value=' + filial_id + ']').prop('disabled', false);
            }


            $('#select-filials option:not([disabled]):first').prop('selected', true);

        };

    });

</script>


