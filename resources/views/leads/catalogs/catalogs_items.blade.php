<ul class="vertical menu drilldown" data-drilldown data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'>

    @foreach ($catalog->items as $item)
    @if(is_null($item->parent_id))

    {{-- Если категория --}}
    <li class="item-catalog">
        <a class="get-prices" id="{{ $item->getTable() }}-{{ $item->id }}">{{ $item->name }}</a>

        @if($item->childs->isNotEmpty())

        <ul class="menu vertical nested">

            @include('leads.catalogs.catalogs_items_childs', ['items' => $item->childs])

        </ul>

        @endif

    </li>

    @endif
    @endforeach

</ul>

@push('scripts')
<script>

    var type = '{{ $type }}';

    $(document).on('click', '.get-prices', function(event) {
        // event.preventDefault();

        var entity = $(this).attr('id').split('-')[0];
        var id = $(this).attr('id').split('-')[1];

        // alert(entity + ' ' + id);

        $.post("/admin/" + entity + "/prices", {
            id: id
        }, function(html){
            // alert(html);
            $('#list-prices_' + type).html(html);
        });
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
                if ($('#prices_' + type + '-section [data-price=' + id +']').length == 1) {
                    $('#prices_' + type + '-section [data-price="' + id +'"]').replaceWith(html);
                } else {
                    $('#' + entity + '-section').append(html);
                }
            });
        }
    });
</script>
@endpush
