<li class="medium-item item @if (isset($category['children'])) parent @endif">
  <a class="medium-link @if ($id == $category['id']) active @endif" href="/services/{{ $category['id'] }}" data-link="{{ $category['id'] }}">{{ $category['name'] }}</a>

  @if (isset($category['children']))
  <ul class="menu vertical medium-list nested @if (isset($category['item_id'])) is-active @endif">
    @foreach ($category['children'] as $category)
    @include('project.services.categories-list', ['category' => $category, 'id' => $id])
    @endforeach
  </ul>
  @endif
</li>

















