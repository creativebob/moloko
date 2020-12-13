<lead-agents-component
    @if($outlet->catalogs_goods->isNotEmpty())
    :catalog-id="{{ $outlet->catalogs_goods->first()->id }}"
    @endif
></lead-agents-component>
