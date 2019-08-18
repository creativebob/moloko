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

    $(document).on('click', '.add-to-estimate', function(event) {
        event.preventDefault();

        var entity = $(this).attr('id').split('-')[0];
        var id = $(this).attr('id').split('-')[1];
        var serial = $(this).data('serial');

        // alert(entity + ', id: ' + id + ', serial: ' + serial);

        if (serial == 1) {
            $.post("/admin/create_estimates_item", {
                lead_id: lead_id,
                id: id,
                entity: entity
            }, function(html){
                $('#' + entity + '-section').append(html);

                //$(document).foundation('_handleTabChange', $('#content-panel-order'), historyHandled);
            });
        } else {

            $.post("/admin/update_estimates_item", {
                lead_id: lead_id,
                id: id,
                entity: entity
            }, function(html) {
                // alert(html);
                // alert($('#prices_services-section [data-price=' + id +']').length);
                if ($('#' + entity + '-section [data-price=' + id +']').length == 1) {
                    $('#' + entity + '-section [data-price="' + id +'"]').replaceWith(html);
                } else {
                    $('#' + entity + '-section').append(html);
                }
            });
        }
    });


</script>




