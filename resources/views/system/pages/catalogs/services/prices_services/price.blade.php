<tr class="item @if($priceService->moderation == 1)no-moderation @endif" id="prices_services-{{ $priceService->id }}" data-name="{{ $priceService->catalogs_item->name }}">
    <td class="td-drop">
        <div class="sprite icon-drop"></div>
    </td>

    <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="prices_service_id" id="check-{{ $priceService->id }}"

               {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
               @if(!empty($filter['booklist']['booklists']['default']))
{{--               Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked--}}
               @if (in_array($priceService->id, $filter['booklist']['booklists']['default'])) checked
                @endif
                @endif
        ><label class="label-check" for="check-{{ $priceService->id }}"></label>
    </td>

    <td class="td-photo tiny">
        <img src="{{ getPhotoPathPlugEntity($priceService->goods, 'small') }}" alt="{{ isset($priceService->service->process->photo_id) ? $priceService->service->process->name : 'Нет фото' }}">
    </td>

    <td class="td-name">
        {{ $priceService->service->process->name }}
        {{-- @can('update', $priceService)
        {{ link_to_route('prices_services.edit', $priceService->name, $parameters = ['id' => $priceService->id], $attributes = []) }}
        @endcan

        @cannot('update', $priceService)
        {{ $priceService->name }}
        @endcannot

        %5B%5D
        ({{ link_to_route('goods.index', $priceService->articles_count, $parameters = ['prices_service_id' => $priceService->id], $attributes = ['class' => 'filter_link light-text', 'title' => 'Перейти на список артикулов']) }}) --}}

        <br><span class="tiny-text">{{ $priceService->service->category->name }}</span>

    </td>

    <td class="td-unit">
        {{ $priceService->service->process->unit->abbreviation }}
    </td>

    <td class="td-length">
{{--        @if($priceService->service->process->unit_id != 12)--}}
{{--            {{ num_format($priceService->service->process->length / $cur_prices_goods->goods->article->unit_weight->ratio, 0) }} {{ $cur_prices_goods->goods->article->unit_weight->abbreviation }}--}}
{{--        @endif--}}
    </td>

    <td class="td-catalogs_item">{{ $priceService->catalogs_item->name_with_parent }}</td>

    <td class="td-price">
        @include('system.pages.catalogs.services.prices_services.price_span')



        {{-- <div class="grid-x" id="sync-{{ $priceService->id }}">

            @template ($priceService)

            <div class="small-6 cell sync-price">
                <label>Цена
                    {!! Form::number('price', $priceService->price, []) !!}
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
            @if($priceService->status == 1) show @else hide @endif
            ">
            @if($priceService->status == 1) Не оказывается @else Доступна @endif</button>
    </td>

    <td class="td-hit">
        <button type="button" class="hollow tiny button price_services-hit
            @if($priceService->is_hit == 1) hit @endif
            ">
            @if($priceService->is_hit == 1) Хит продаж @else Обычный @endif</button>
    </td>

    <td class="td-new">
        <button type="button" class="hollow tiny button price_services-new
            @if($priceService->is_new == 1) new @endif
            ">
            @if($priceService->is_new == 1) Новинка @else Обычный @endif</button>
    </td>

    {{-- Элементы управления --}}
     @include('includes.control.table_td', ['item' => $priceService])

    <td class="td-delete">

        @can('delete', $priceService)
            <a class="icon-delete sprite" data-open="delete-price"></a>
        @endcan

    </td>
</tr>
