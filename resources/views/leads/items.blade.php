{{-- Если вложенный --}}

@foreach ($goods_list as $cur_goods)
<li><a class="add-to-order" id="{{ $entity }}-{{ $cur_goods->id }}">{{ $cur_goods->goods_article->name }} ({{ $cur_goods->price }})</a></li>
@endforeach
















