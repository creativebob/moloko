<categorier-component
    name="categories"
    title="Категории"
    :tree='@json($categoriesTree)'
    :checkeds='@json(request()->categories)'
></categorier-component>
