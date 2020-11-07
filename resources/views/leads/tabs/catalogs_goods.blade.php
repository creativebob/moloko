@can('index', App\CatalogsGoods::class)
    @if($catalogs_goods_data['catalogsGoods']->isNotEmpty())
        <catalog-goods-component
            :catalogs-goods-data='@json($catalogs_goods_data)'
            :outlet="{{ $outlet }}"
        ></catalog-goods-component>
    @endif
@endcan
