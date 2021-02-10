<lead-agents-component
    @if($outlet->catalogs_goods->isNotEmpty())
    :catalog-goods-id="{{ $outlet->catalogs_goods->first()->id }}"
    @endif
    @if($outlet->catalogs_services->isNotEmpty())
    :catalog-services-id="{{ $outlet->catalogs_services->first()->id }}"
    @endif
></lead-agents-component>
