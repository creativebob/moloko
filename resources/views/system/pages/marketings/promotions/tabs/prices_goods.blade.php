<div class="grid-x grid-padding-x">

    <div class="cell small-12">
        <promotion-catalog-goods-component
            :catalogs-goods-data='@json($catalogs_goods_data)'
            :prices-goods='@json($promotion->prices_goods)'
        ></promotion-catalog-goods-component>
    </div>

    {!! Form::hidden('is_recommend', 0) !!}
    <div class="cell small-12 checkbox">
        {!! Form::checkbox('is_recommend', 1, $promotion->is_recommend, ['id' => 'checkbox-is_recommend']) !!}
        <label for="checkbox-is_recommend"><span>Отображать в рекомендациях</span></label>
    </div>

    {!! Form::hidden('is_upsale', 0) !!}
    <div class="cell small-12 checkbox">
        {!! Form::checkbox('is_upsale', 1, $promotion->is_upsale, ['id' => 'checkbox-is_upsale']) !!}
        <label for="checkbox-is_upsale"><span>Отображать в корзине</span></label>
    </div>
</div>
