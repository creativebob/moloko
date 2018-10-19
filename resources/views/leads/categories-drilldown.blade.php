<ul class="vertical menu drilldown" data-drilldown>

    @foreach($grouped_items as $categories)

    @foreach ($categories as $category)
    @if($category->category_status == 1)
    {{-- Если категория --}}
    <li>

        @if(isset($grouped_items[$category->id]))
        <a href="#">{{ $category->name }}</a>
        <ul class="menu vertical nested">
            @include('includes.drilldown_views.items_drilldown', ['items' => $grouped_items[$category->id], 'grouped_items' => $grouped_items])
        </ul>
        @else
        <a href="#">{{ $category->name }}</a>
        @endif
    </li>
    @endif

    @endforeach

    @endforeach

</ul>