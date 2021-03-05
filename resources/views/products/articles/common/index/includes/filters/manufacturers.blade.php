<checkboxer-component
    name="manufacturers"
    title="Производитель"
    relation="company"
    :items='@json($manufacturers)'
    :checkeds='@json(request()->manufacturers)'
></checkboxer-component>

