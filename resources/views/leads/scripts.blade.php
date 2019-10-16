<script type="application/javascript">

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

    var estimate_id;
    @isset ($lead->estimate)
        estimate_id = '{{ $lead->estimate->id }}';
    @else
        estimate_id = false;
    @endisset

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
                });
            } else {
                $.post('/admin/estimates_' + type + '_items', {
                    estimate_id: estimate_id,
                    price_id: price_id,
                }, function(html){
                    $('#section-' + type).append(html);

                    //$(document).foundation('_handleTabChange', $('#content-panel-order'), historyHandled);
                });
            }
        }
    };

    // console.log('Смета = ' + estimate_id);

    $(document).on('click', '.add-to-estimate', function(event) {
        event.preventDefault();

        let object = $(this);
        
        if (estimate_id === false) {
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

</script>




