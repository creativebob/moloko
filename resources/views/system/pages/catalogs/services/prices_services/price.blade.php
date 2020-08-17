<tr class="item @if($prices_service->moderation == 1)no-moderation @endif" id="prices_services-{{ $prices_service->id }}" data-name="{{ $prices_service->catalogs_item->name }}">
    <td class="td-drop">
        <div class="sprite icon-drop"></div>
    </td>

    <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="prices_service_id" id="check-{{ $prices_service->id }}"

               {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
               @if(!empty($filter['booklist']['booklists']['default']))
               Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked
               @if (in_array($prices_service->id, $filter['booklist']['booklists']['default'])) checked
                @endif
                @endif
        ><label class="label-check" for="check-{{ $prices_service->id }}"></label>
    </td>

    <td class="td-photo tiny">
        <img src="{{ getPhotoPathPlugEntity($prices_service->goods, 'small') }}" alt="{{ isset($prices_service->service->process->photo_id) ? $prices_service->service->process->name : 'Нет фото' }}">
    </td>

    <td class="td-name">
        {{ $prices_service->service->process->name }}
        {{-- @can('update', $prices_service)
        {{ link_to_route('prices_services.edit', $prices_service->name, $parameters = ['id' => $prices_service->id], $attributes = []) }}
        @endcan

        @cannot('update', $prices_service)
        {{ $prices_service->name }}
        @endcannot

        %5B%5D
        ({{ link_to_route('goods.index', $prices_service->articles_count, $parameters = ['prices_service_id' => $prices_service->id], $attributes = ['class' => 'filter_link light-text', 'title' => 'Перейти на список артикулов']) }}) --}}

        <br><span class="tiny-text">{{ $prices_service->service->category->name }}</span>

    </td>

    <td class="td-unit">
        {{ $prices_service->service->process->unit->abbreviation }}
    </td>

    <td class="td-length">
{{--        @if($prices_service->service->process->unit_id != 12)--}}
{{--            {{ num_format($prices_service->service->process->length / $cur_prices_goods->goods->article->unit_weight->ratio, 0) }} {{ $cur_prices_goods->goods->article->unit_weight->abbreviation }}--}}
{{--        @endif--}}
    </td>

    <td class="td-catalogs_item">{{ $prices_service->catalogs_item->name_with_parent }}</td>

    <td class="td-price">
        @include('system.pages.catalogs.services.prices_services.price_span')



        {{-- <div class="grid-x" id="sync-{{ $prices_service->id }}">

            @template ($prices_service)

            <div class="small-6 cell sync-price">
                <label>Цена
                    {!! Form::number('price', $prices_service->price, []) !!}
                </label>
            </div>
            <div class="small-6 cell sync-button">
                <button class="button button-sync">Синхронизировать</button>
            </div>

            @else

            @include('prices_services.sync')

            @endtemplate

        </div> --}}


    </td>

    <td class="td-points">
        @include('system.pages.catalogs.services.prices_services.price_points')
    </td>

    <td class="td-price-status">
        <button type="button" class="hollow tiny button price_services-status
            @if($prices_service->status == 1) show @else hide @endif
            ">
            @if($prices_service->status == 1) Не оказывается @else Доступна @endif</button>
    </td>

    <td class="td-hit">
        <button type="button" class="hollow tiny button price_services-hit
            @if($prices_service->is_hit == 1) hit @endif
            ">
            @if($prices_service->is_hit == 1) Хит продаж @else Обычный @endif</button>
    </td>

    <td class="td-new">
        <button type="button" class="hollow tiny button price_services-new
            @if($prices_service->is_new == 1) new @endif
            ">
            @if($prices_service->is_new == 1) Новинка @else Обычный @endif</button>
    </td>

    {{-- Элементы управления --}}
     @include('includes.control.table_td', ['item' => $prices_service])

    <td class="td-delete">

        @can('delete', $prices_service)
            <a class="icon-delete sprite" data-open="delete-price"></a>
        @endcan

    </td>
</tr>
