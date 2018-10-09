<script type="text/javascript">
    $(function() {
        // Смотрим при загрузке количество выделенных чекбоксов
        if ($('.access-checkbox:checked').length >= 1) {
            // Если 1 или более, разблокируем кнопку
            $('.position-button').prop('disabled', false);
        };
        $(document).on('click', '.access-checkbox', function () {
            // Смотрим при клике
            if ($('.access-checkbox:checked').length >= 1) {
                $('.position-button').prop('disabled', false);
            } else {
                $('.position-button').prop('disabled', true);
            };
        });
    });

    $(document).on('change', '#entities-list', function(event) {
        event.preventDefault();

        // alert($(this).val());

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/fields_list',
            type: 'POST',
            data: {entity_id: $(this).val()},
            success: function(html){
                $('#fields-list').html(html);
            }
        });
    });


    $(document).on('click', '.rule-add', function(event) {
        event.preventDefault();
        
        // alert($(this).closest('.fieldset-access').serialize());

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/rule_add',
            type: 'POST',
            data: $(this).closest('.fieldset-access').serialize(),
            success: function(html){

                if (html == '') {
                    alert('Правило с таким именем существует!');
                } else {
                    $('.inputs-rules input').val('');
                    $('#rules-table').append(html);
                }
            }
        });

    });
</script>

