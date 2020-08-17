<metrics-categories-component
    :category='@json($category)'
    :properties='@json($properties)'
    entity="{{ $category->getTable() }}"
    :entity-id="{{ $pageInfo->entity->id }}"
    :category-id="{{ $category->id }}"
></metrics-categories-component>
