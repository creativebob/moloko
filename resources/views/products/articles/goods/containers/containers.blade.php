<cmv-simple-compositions-component
    :categories='@json($containersCategories)'
    :items='@json($containers->setAppends([
        'weight',
        'cost_unit'
    ]))'
    :item-items='@json($article->containers->setAppends([
        'weight',
        'cost_unit'
    ]))'
    name="containers"
    @if($article->draft == 0)
        :disabled="true"
    @endif
></cmv-simple-compositions-component>