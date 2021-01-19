<presets-component
    :categories='@json($impactsCategories)'
    :items='@json($impacts)'
    :item-items='@json($process->impacts)'
    name="impacts"
></presets-component>
