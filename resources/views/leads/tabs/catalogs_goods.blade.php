{{--@can('index', App\CatalogsGoods::class)--}}
{{--    @if($catalogs_goods_data['catalogsGoods']->isNotEmpty())--}}
        <catalog-goods-component
            :outlet="{{ $outlet }}"
        ></catalog-goods-component>
{{--:catalogs-goods-data='@json($catalogs_goods_data)'--}}
{{--    @endif--}}
{{--@endcan--}}
