<presets-component
    :categories='@json($relatedCategories)'
    :items='@json($related)'
    :item-items='@json($category->related)'
    name="related"
></presets-component>
