{{-- Если вложенный --}}

@foreach ($items as $item)
<li>
@if(isset($group_goods_categories[$item->id]))

<a href="#">{{ $item->name }}</a>
<ul class="menu vertical nested">
    @include('leads.items-drilldown', ['items' => $group_goods_categories[$item->id]])
</ul>
@else
<a href="#">{{ $item->name }}</a>
@endif
</li>
@endforeach
















