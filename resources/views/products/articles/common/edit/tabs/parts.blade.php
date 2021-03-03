<parts-component
    :categories='@json($partsCategories)'
    :items='@json($parts)'
    :article-items='@json($article->parts)'
    name="parts"
    alias="{{ $item->getTable() }}"
></parts-component>
