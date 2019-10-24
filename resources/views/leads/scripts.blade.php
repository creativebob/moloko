<script type="application/javascript">

    var estimate_id = 0;
    @isset($lead->estimate)
        estimate_id = '{{ $lead->estimate->id }}';
    @endisset

    $(document).ready(function () {

        $('.view-list').each(function(index) {
            $(this).hide();
        });

        $(document).on('click', '.get-prices', function() {
            let type = $(this).data('type');
            $('#block-prices_' + type + ' .view-list').each(function(index) {
                $(this).hide();
            });

            let id = $(this).data('id');
            $('#' + id).show();
        });

        estimate_amount();

        function estimate_amount() {
            // alert('считаем');
            var amount = 0;
            $('#section-goods tr').each(function( index ) {
                // alert($(this).data('amount'));
                amount += ($(this).data('count') * $(this).data('price'));
            });
            let total = amount - (amount * 10) / 100;
            $('#estimate-amount').text(amount.toLocaleString());
            $('#estimate-total').text(total.toLocaleString());

            return total;
        };

        function check_badget(total) {

            if (estimate_id > 0) {
                $('#digitfield-badget').attr('readonly', true);
                $('#digitfield-badget').val(total);
            }
        };

        $(document).on('dblclick', '#digitfield-badget', function(event) {
            $('#digitfield-badget').attr('readonly', false);
        });

         function estimate_item (object) {
            let price_id = object.data('price_id'),
                serial = object.data('serial'),
                type = object.data('type');

            // alert(entity + ', id: ' + id + ', serial: ' + serial);

            if (serial === 1) {
                $.post('/admin/estimates_' + type + '_items', {
                    estimate_id: estimate_id,
                    price_id: price_id,
                }, function(html){
                    $('#section-' + type).append(html);

                    //$(document).foundation('_handleTabChange', $('#content-panel-order'), historyHandled);
                }).done(function() {
                    let total = estimate_amount();
                    check_badget(total);
                });
            } else {

                if ($('#section-' + type + ' [data-price_id=' + price_id +']').length > 0) {

                    let estimate_item_id = $('#section-' + type + ' [data-price_id=' + price_id +']').attr('id').split('-')[1];

                    $.ajax({
                        url: '/admin/estimates_' + type + '_items/' + estimate_item_id,
                        type: 'PATCH',
                        success: function (html) {
                            $('#section-' + type + ' [data-price_id="' + price_id +'"]').replaceWith(html);

                        },
                    }).done(function() {
                        let total = estimate_amount();
                        check_badget(total);
                    });
                } else {
                    $.post('/admin/estimates_' + type + '_items', {
                        estimate_id: estimate_id,
                        price_id: price_id,
                    }, function(html){
                        $('#section-' + type).append(html);

                        //$(document).foundation('_handleTabChange', $('#content-panel-order'), historyHandled);
                    }).done(function() {
                        let total = estimate_amount();
                        check_badget(total);
                    });
                }
            }
        };

        // console.log('Смета = ' + estimate_id);

        $(document).on('click', '.add-to-estimate', function(event) {
            event.preventDefault();

            let object = $(this);

            if (estimate_id === 0) {
                $.post("/admin/create_estimate", {
                    lead_id: lead_id,
                }, function(id){
                    estimate_id = id;
                    // console.log('Создана смета с id: ' + estimate_id);
                }).done(function() {
                    estimate_item(object);

                });
            } else {
                estimate_item(object);
            }
        });

        $(document).on('click', '[data-open="delete-estimates_item"]', function() {

            // Находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var entity_alias = parent.attr('id').split('-')[0];
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');
            $('.title-estimates_item').text(name);
            $('.button-delete-estimates_item').attr('id', entity_alias + '-' + id);
        });

        $(document).on('click', '.button-delete-estimates_item', function(event) {
            event.preventDefault();

            var entity = $(this).attr('id').split('-')[0];
            var id = $(this).attr('id').split('-')[1];

            var buttons = $('.button');

            $.ajax({
                url: '/admin/' + entity + '/' + id,
                type: 'DELETE',
                success: function (data) {
                    if (data > 0) {
                        $('#' + entity + '-' + id).remove();
                        $('#delete-estimates_item').foundation('close');
                        $('.button-delete-estimates_item').removeAttr('id');
                        buttons.prop('disabled', false);
                    }
                }
            }).done(function() {
                let total = estimate_amount();
                check_badget(total);
            });
        });

    });

</script>




