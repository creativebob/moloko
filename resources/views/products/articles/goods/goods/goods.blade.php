<cmv-simple-compositions-component
    :categories='@json($goodsCategories)'
    :items='@json($goods)'
    :item-items='@json($article->goods)'
    name="goods"
    @if($article->draft == 0)
    :disabled="true"
    @endif
></cmv-simple-compositions-component>