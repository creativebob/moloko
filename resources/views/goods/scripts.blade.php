<script type="text/javascript">

    function setUnitAbbrevation(list) {
        $('#unit-change').text($('#' + list + ' :selected').data('abbreviation'));
    };

    // function setGroupProductAbbrevation($list) {
    //     $('#unit-change').text($('#' + list + ' :selected').data('abbreviation'));
    // };

    $(document).on('click', '.unit-change', function(event) {
        event.preventDefault();
        $('#units-block div').toggle();
    });

    $(document).on('change', '#units-categories-list', function() {
        var id = $(this).val();
        // alert(id);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/get_units_list',
            type: "POST",
            data: {units_category_id: id, entity: 'goods'},
            success: function(html){
                $('#units-list').html(html);
                // $('#units-list').prop('disabled', false);
                setUnitAbbrevation('units-list');
                // $('#unit-change').text($('#units-list option:first').data('abbreviation'));
            }
        });
    });

    $(document).on('change', '#units-list, #goods-products-list', function() {
        $('#unit-change').text($(this).find(':selected').data('abbreviation'));
    });

    $(document).on('change', '#goods-categories-list', function() {

        // var id = $(this).val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/goods_products_create_mode',
            type: "POST",
            data: $('#form-cur-goods-add').serialize(),
            success: function(html){
                // alert(html);
                $('#mode').html(html);
                Foundation.reInit($('#form-cur-goods-add'));
            }
        });
    });

    $(document).on('change', '.mode-select', function() {

        var id = $(this).val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/ajax_goods_count',
            type: "POST",
            data: {id: id},
            success: function(html){
                // alert(html);
                $('#mode').html(html);

            }
        });
    });

    $(document).on('click', '#set-status', function(event) {
        // event.preventDefault();
        var mode = $('input[name=mode]').val();

        if (mode == 'mode-select') {
            var checkbox_status = $(this).prop('checked');

            if (checkbox_status == true) {
                set_status = 'set';
            } else {
                set_status = 'one';
            }
            // alert(set_status + ' ' + mode);

            // alert(id);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/admin/goods_products_create_mode',
                type: "POST",
                data: {mode: mode, goods_category_id: $('#goods-categories-list').val(), set_status: set_status},
                success: function(html){
                    // $('#goods-categories-list').removeAttr('class');
                    // $('#goods-categories-list').addClass(mode);
                    // alert(html);
                    $('#mode').html(html);
                    // $('#unit-change').removeClass('unit-change')
                    // $('#units-block div').hide();
                    setUnitAbbrevation('goods-products-list');
                    // $('#unit-change').text($('#goods-products-list').find(':selected').data('abbreviation'));

                    Foundation.reInit($('#form-cur-goods-add'));

                }
            });
        }
    });

    $(document).on('click', '.modes', function(event) {
        event.preventDefault();

        var mode = $(this).attr('id');
        var checkbox_status = $('#set-status').prop('checked');

        if (checkbox_status == true) {
            set_status = 'set';
        } else {
            set_status = 'one';
        }
        // alert(checkbox_status + ' ' + set_status);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/goods_products_create_mode',
            type: "POST",
            data: {mode: mode, goods_category_id: $('#goods-categories-list').val(), set_status: set_status},
            success: function(html){
                // $('#goods-categories-list').removeAttr('class');
                // $('#goods-categories-list').addClass(mode);
                // alert(html);
                $('#mode').html(html);
                if (mode == 'mode-select') {
                    $('#unit-change').removeClass('unit-change');
                    $('#units-block div').hide();
                    setUnitAbbrevation('goods-products-list');
                    // $('#unit-change').text($('#goods-products-list').find(':selected').data('abbreviation'));
                } else {
                    $('#unit-change').addClass('unit-change');
                    setUnitAbbrevation('units-list');
                    // $('#unit-change').text($('#units-list :selected').data('abbreviation'));
                }
                Foundation.reInit($('#form-cur-goods-add'));

            }
        });
    });

    $(document).on('keyup', 'input[name=goods_product_name], input[name=name]', function(event) {
        event.preventDefault();

        if ($('input[name=goods_product_name]').val() == $('input[name=name]').val()) {
            $('.item-error').css('display', 'block');
            $('.modal-button').attr('disabled', true);
        } else {
            $('.item-error').css('display', 'none');
            $('.modal-button').attr('disabled', false);
        }
    });


</script>


