@if($catalogs_goods_data['catalogsGoods']->isNotEmpty())
    <catalog-goods-component
        :catalogs-goods-data='@json($catalogs_goods_data)'
    ></catalog-goods-component>
@endif
