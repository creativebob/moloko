<tr class="item @if($cur_prices_goods->moderation == 1)no-moderation @endif" id="prices_goods-{{ $cur_prices_goods->id }}" data-name="{{ $cur_prices_goods->catalogs_item->name }}">
    <td class="td-drop">
        <div class="sprite icon-drop"></div>
    </td>

    <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="prices_service_id" id="check-{{ $cur_prices_goods->id }}"

               {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
               @if(!empty($filter['booklist']['booklists']['default']))
{{--               Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked--}}
               @if (in_array($cur_prices_goods->id, $filter['booklist']['booklists']['default'])) checked
                @endif
                @endif
        ><label class="label-check" for="check-{{ $cur_prices_goods->id }}"></label>
    </td>

    <td class="td-photo tiny">
        <a href="/admin/{{ $entity }}/{{ $cur_prices_goods->goods->id }}/edit">
            <img src="{{ getPhotoPathPlugEntity($cur_prices_goods->goods, 'small') }}" alt="{{ isset($cur_prices_goods->goods->article->photo_id) ? $cur_prices_goods->goods->article->name : 'Нет фото' }}">
        </a>
    </td>

    <td class="td-name">
        {{ $cur_prices_goods->goods->article->name }}
        {{-- @can('update', $cur_prices_goods)
        {{ link_to_route('prices_services.edit', $cur_prices_goods->name, $parameters = ['id' => $cur_prices_goods->id], $attributes = []) }}
        @endcan

        @cannot('update', $cur_prices_goods)
        {{ $cur_prices_goods->name }}
        @endcannot

        %5B%5D
        ({{ link_to_route('goods.index', $cur_prices_goods->articles_count, $parameters = ['prices_service_id' => $cur_prices_goods->id], $attributes = ['class' => 'filter_link light-text', 'title' => 'Перейти на список артикулов']) }}) --}}

        <br><span class="tiny-text">{{ $cur_prices_goods->goods->category->name }}</span>

    </td>
    
    <td class="td-unit">
        {{ $cur_prices_goods->goods->article->group->unit->abbreviation }}
    </td>
    <td class="td-weight">
        @if($cur_prices_goods->goods->article->group->unit_id != 8)
            {{ num_format($cur_prices_goods->goods->article->weight / $cur_prices_goods->goods->article->unit_weight->ratio, 0) }} {{ $cur_prices_goods->goods->article->unit_weight->abbreviation }}
        @endif
    </td>

    <td class="td-catalogs_item">{{ $cur_prices_goods->catalogs_item->name }}</td>
    <td class="td-price">
        @include('prices_goods.price_span')



        {{-- <div class="grid-x" id="sync-{{ $cur_prices_goods->id }}">

            @template ($cur_prices_goods)

            <div class="small-6 cell sync-price">
                <label>Цена
                    {!! Form::number('price', $cur_prices_goods->price, []) !!}
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

    {{-- Элементы управления --}}
    @include('includes.control.table_td', ['item' => $cur_prices_goods])

    <td class="td-delete">

        @can('delete', $cur_prices_goods)
            <a class="icon-delete sprite" data-open="delete-price"></a>
        @endcan

    </td>
</tr>