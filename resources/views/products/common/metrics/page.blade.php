<metrics-categories-component
    :category='@json($category)'
    :properties='@json($properties)'
    entity="{{ $category->getTable() }}"
    :entity-id="{{ $page_info->entity->id }}"
    :category-id="{{ $category->id }}"
></metrics-categories-component>
