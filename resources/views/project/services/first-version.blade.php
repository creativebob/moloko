@if (isset($services_categories_tree))
<section>

  @foreach ($services_categories_tree as $service_category)
  @if($service_category['category_status'] == 1)
  <div class="head-item-nav">- {{ $service_category['name'] }} -</div>
  @if (count($service_category['children']) > 0)
  <ul class="vertical menu">
    @foreach ($service_category['children'] as $children)
    <li><a href="/services/{{ $children['id'] }}" @if ($id == $children['id']) class="active" @endif>{{ $children['name'] }}</a></li>
    @endforeach
    @endif
    @endif
  </ul>
  @endforeach
</section>
@endif




