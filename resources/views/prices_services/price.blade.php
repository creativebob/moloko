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

    </td>
    <td class="td-catalogs_item">{{ $prices_service->catalogs_item->name }}</td>
    <td class="td-price">
        @include('prices_services.price_span')



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

    {{-- Элементы управления --}}
    {{-- @include('includes.control.table_td', ['item' => $prices_service]) --}}

    <td class="td-delete">

        @can('delete', $prices_service)
            <a class="icon-delete sprite" data-open="delete-price"></a>
        @endcan

    </td>
</tr>