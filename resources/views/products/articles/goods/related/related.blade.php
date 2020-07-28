<presets-component
    :categories='@json($relatedCategories)'
    :items='@json($related)'
    :item-items='@json($item->related)'
    name="related"
></presets-component>
