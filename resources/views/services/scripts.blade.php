<script type="text/javascript">

    // $(document).on('change', '#units-categories-list', function() {
    //     var id = $(this).val();
    //     // alert(id);

    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url: '/admin/get_units_list',
    //         type: "POST",
    //         data: {id: id, entity: 'services_categories'},
    //         success: function(html){
    //             $('#units-list').html(html);
    //             $('#units-list').prop('disabled', false);
    //         }
    //     }); 
    // });

    $(document).on('change', '.mode-default', function() {

        // var id = $(this).val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/ajax_services_modes',
            type: "POST",
            data: {mode: 'mode-default', services_category_id: $('#services-categories-list').val()},
            success: function(html){
                // alert(html);
                $('#mode').html(html);
                Foundation.reInit($('#form-service-add'));
            }
        }); 
    });

    $(document).on('change', '.mode-select', function() {

        var id = $(this).val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/ajax_services_count',
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
            url: '/admin/ajax_services_modes',
            type: "POST",
            data: {mode: mode, services_category_id: $('#services-categories-list').val()},
            success: function(html){
                $('#services-categories-list').removeAttr('class');
                $('#services-categories-list').addClass(mode);
                // alert(html);
                $('#mode').html(html);
                Foundation.reInit($('#form-service-add'));

            }
        }); 
    });

    $(document).on('keyup', 'input[name=service_product_name], input[name=name]', function(event) {
        event.preventDefault();

        if ($('input[name=service_product_name]').val() == $('input[name=name]').val()) {
            $('.item-error').css('display', 'block');
            $('.modal-button').attr('disabled', true);
        } else {
            $('.item-error').css('display', 'none');
            $('.modal-button').attr('disabled', false);
        }
    });


</script>


