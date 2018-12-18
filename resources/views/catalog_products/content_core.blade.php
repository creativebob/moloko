@isset($catalog)
@foreach (['goods', 'raws', 'services'] as $type)
@if ($catalog->$type->count())
@foreach ($catalog->$type as $item)

@php
$article = $type . '_article';
@endphp

<tr class="item {{ isset($item->moderation) ? 'no-moderation' : '' }}" id="catalog_products-{{ $item->pivot->id }}" data-name="{{ $item->$article->name }}">

    <td class="td-drop">
        <div class="sprite icon-drop"></div>
    </td>
    <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="cur_goods_id" id="check-{{ $item->id }}"
        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($item->id, $filter['booklist']['booklists']['default'])) checked
        @endif
        @endif
        >
        <label class="label-check" for="check-{{ $item->id }}"></label>
    </td>
    <td class="td-name">
        <a href="/admin/{{ $type }}/{{ $item->id }}/edit">{{ $item->$article->name }}</a>
    </td>
    <td class="td-type">
        @switch($type)
        @case('goods')
        Товар
        @break

        @case('raws')
        Сырье
        @break

        @case('services')
        Услуга
        @break

        @endswitch
    </td>
    <td class="td-price">{{ num_format($item->price, 0) }}</td>

    @isset(Auth::user()->god)
    <td class="td-company-id">

        @if(!empty($item->company->name))
        {{ $item->company->name }}
        @else

        @if($item->system_item == null)
        Шаблон
        @else
        Системная
        @endif

        @endif

    </td>
    @endisset

    <td class="td-author">
        @isset($item->author)
        {{ $item->author->name }}
        @endisset
    </td>

    {{-- Элементы управления --}}
    <td class="td-control">

        {{-- Отображение на сайте --}}
        @can ('display', App\CatalogProduct::class)
        @display ($item->pivot)
        <div class="icon-display-show black sprite" data-open="item-display"></div>
        @else
        <div class="icon-display-hide black sprite" data-open="item-display"></div>
        @enddisplay
        @endcan

    </td>

    <td class="td-delete">

        @can('delete', $item)
        <a class="icon-delete sprite" data-open="item-delete-ajax"></a>
        @endcan

    </td>
</tr>

@endforeach
@endif
@endforeach
@endisset
