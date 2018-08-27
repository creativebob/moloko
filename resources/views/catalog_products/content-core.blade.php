@if(!empty($catalog))

@if (count($catalog->goods) > 0)
@foreach ($catalog->goods as $cur_goods)
<tr class="item @if($cur_goods->moderation == 1)no-moderation @endif" id="catalog_products-{{ $cur_goods->pivot->id }}" data-name="{{ $cur_goods->goods_article->name }}">
    <td class="td-drop"><div class="sprite icon-drop"></div></td>
    <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="cur_goods_id" id="check-{{ $cur_goods->id }}"
        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($cur_goods->id, $filter['booklist']['booklists']['default'])) checked 
        @endif
        @endif
        >
        <label class="label-check" for="check-{{ $cur_goods->id }}"></label>
    </td>

    <td class="td-name"><a href="/admin/goods/{{ $cur_goods->id }}/edit">{{ $cur_goods->goods_article->name }}</a></td>
    <td class="td-type">Товар</td>

    <td class="td-price">{{ num_format($cur_goods->price, 0) }}</td>

    @if(Auth::user()->god == 1) 
    <td class="td-company-id">@if(!empty($cur_goods->company->name)) {{ $cur_goods->company->name }} @else @if($cur_goods->system_item == null) Шаблон @else Системная @endif @endif</td>
    @endif

    <td class="td-author">@if(isset($cur_goods->author->first_name)) {{ $cur_goods->author->first_name . ' ' . $cur_goods->author->second_name }} @endif</td>

    {{-- Элементы управления --}}
    <td class="td-control">

        {{-- Отображение на сайте --}}
        @can ('display', App\CatalogProduct::class)
        @display ($cur_goods->pivot)
        <div class="icon-display-show black sprite" data-open="item-display"></div>
        @else
        <div class="icon-display-hide black sprite" data-open="item-display"></div>
        @enddisplay
        @endcan

    </td>

    <td class="td-delete">
        @if ($cur_goods->system_item != 1)
        @can('delete', $cur_goods)
        <a class="icon-delete sprite" data-open="item-delete-ajax"></a>
        @endcan
        @endif
    </td>       
</tr>
@endforeach
@endif

@if (count($catalog->services) > 0)
@foreach ($catalog->services as $service)
<tr class="item @if($service->moderation == 1)no-moderation @endif" id="catalog_products-{{ $service->pivot->id }}" data-name="{{ $service->services_article->name }}">
    <td class="td-drop"><div class="sprite icon-drop"></div></td>
    <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="service_id" id="check-{{ $service->id }}"
        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($service->id, $filter['booklist']['booklists']['default'])) checked 
        @endif
        @endif
        >
        <label class="label-check" for="check-{{ $service->id }}"></label>
    </td>

    <td class="td-name"><a href="/admin/services/{{ $service->id }}/edit">{{ $service->services_article->name }}</a></td>
    <td class="td-type">Услуга</td>

    <td class="td-price">{{ num_format($service->price, 0) }}</td>

    @if(Auth::user()->god == 1) 
    <td class="td-company-id">@if(!empty($service->company->name)) {{ $service->company->name }} @else @if($service->system_item == null) Шаблон @else Системная @endif @endif</td>
    @endif

    <td class="td-author">@if(isset($service->author->first_name)) {{ $service->author->first_name . ' ' . $service->author->second_name }} @endif</td>

    {{-- Элементы управления --}}
    <td class="td-control">

        {{-- Отображение на сайте --}}
        @can ('display', App\CatalogProduct::class)
        @display ($service->pivot)
        <div class="icon-display-show black sprite" data-open="item-display"></div>
        @else
        <div class="icon-display-hide black sprite" data-open="item-display"></div>
        @enddisplay
        @endcan

    </td>

    <td class="td-delete">
        @if ($service->system_item != 1)
        @can('delete', $service)
        <a class="icon-delete sprite" data-open="item-delete-ajax"></a>
        @endcan
        @endif
    </td>       
</tr>
@endforeach
@endif

@if (count($catalog->raws) > 0)
@foreach ($catalog->raws as $raw)
<tr class="item @if($raw->moderation == 1)no-moderation @endif" id="catalog_products-{{ $raw->pivot->id }}" data-name="{{ $raw->raws_article->name }}">
    <td class="td-drop"><div class="sprite icon-drop"></div></td>
    <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="raw_id" id="check-{{ $raw->id }}"
        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($raw->id, $filter['booklist']['booklists']['default'])) checked 
        @endif
        @endif
        >
        <label class="label-check" for="check-{{ $raw->id }}"></label>
    </td>

    <td class="td-name"><a href="/admin/raws/{{ $raw->id }}/edit">{{ $raw->raws_article->name }}</a></td>
    <td class="td-type">Сырье</td>

    <td class="td-price">{{ num_format($raw->price, 0) }}</td>

    @if(Auth::user()->god == 1) 
    <td class="td-company-id">@if(!empty($raw->company->name)) {{ $raw->company->name }} @else @if($raw->system_item == null) Шаблон @else Системная @endif @endif</td>
    @endif

    <td class="td-author">@if(isset($raw->author->first_name)) {{ $raw->author->first_name . ' ' . $raw->author->second_name }} @endif</td>

    {{-- Элементы управления --}}
    <td class="td-control">

        {{-- Отображение на сайте --}}
        @can ('display', App\CatalogProduct::class)
        @display ($raw->pivot)
        <div class="icon-display-show black sprite" data-open="item-display"></div>
        @else
        <div class="icon-display-hide black sprite" data-open="item-display"></div>
        @enddisplay
        @endcan

    </td>

    <td class="td-delete">
        @if ($raw->system_item != 1)
        @can('delete', $raw)
        <a class="icon-delete sprite" data-open="item-delete-ajax"></a>
        @endcan
        @endif
    </td>       
</tr>
@endforeach
@endif

@endif
