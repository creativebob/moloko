<ul class="vertical menu drilldown" data-drilldown data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'>

    @foreach ($categories as $category)
    @if($category->parent_id == null)

    {{-- Если категория --}}
    <li class="item-catalog">
        <a class="get-products" id="{{ $entity }}-{{ $category->id }}">{{ $category->name }}</a>

        @if(isset($category->childrens))

        <ul class="menu vertical nested">

            @foreach ($category->childrens as $item)
                @include('includes.drilldowns.items', ['item' => $item])
            @endforeach

        </ul>

        @endif

    </li>

    @endif
    @endforeach

</ul>