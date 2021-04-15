<cmv-simple-compositions-component
    :categories='@json($attachmentsCategories)'
    :items='@json($attachments->setAppends([
        'weight',
        'cost_unit'
    ]))'
    :item-items='@json($article->attachments->setAppends([
        'weight',
        'cost_unit'
    ]))'
    name="attachments"
    @if($article->draft == 0)
        :disabled="true"
    @endif
></cmv-simple-compositions-component>