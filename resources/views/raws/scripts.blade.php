<script type="text/javascript">

    $(document).on('change', '#units-categories-list', function() {
        var id = $(this).val();
        // alert(id);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/get_units_list',
            type: "POST",
            data: {id: id, entity: 'raws_categories'},
            success: function(html){
                $('#units-list').html(html);
                $('#units-list').prop('disabled', false);
            }
        }); 
    });

    $(document).on('change', '.mode-default', function() {

        // var id = $(this).val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/ajax_raws_modes',
            type: "POST",
            data: {mode: 'mode-default', raws_category_id: $('#raws-categories-list').val()},
            success: function(html){
                // alert(html);
                $('#mode').html(html);
                Foundation.reInit($('#form-raw-add'));
            }
        }); 
    });

    $(document).on('change', '.mode-select', function() {

        var id = $(this).val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/ajax_raws_count',
            type: "POST",
            data: {id: id},
            success: function(html){
                // alert(html);
                $('#mode').html(html);

            }
        }); 
    });

    $(document).on('click', '.modes', function(event) {
        event.preventDefault();

        var mode = $(this).attr('id');
        // alert(id);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/ajax_raws_modes',
            type: "POST",
            data: {mode: mode, raws_category_id: $('#raws-categories-list').val()},
            success: function(html){
                $('#raws-categories-list').removeAttr('class');
                $('#raws-categories-list').addClass(mode);
                // alert(html);
                $('#mode').html(html);
                Foundation.reInit($('#form-raw-add'));

            }
        }); 
    });

    $(document).on('keyup', 'input[name=raws_product_name], input[name=name]', function(event) {
        event.preventDefault();

        if ($('input[name=raws_product_name]').val() == $('input[name=name]').val()) {
            $('.item-error').css('display', 'block');
            $('.modal-button').attr('disabled', true);
        } else {
            $('.item-error').css('display', 'none');
            $('.modal-button').attr('disabled', false);
        }
    });


</script>


