<div class="reveal rev-large" id="modal-sync-price" data-reveal data-close-on-click="false">

    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Синхронизация услуг</h5>
        </div>
    </div>

    {{ Form::open(['route' => ['prices_services.sync', $catalog_id],'id' => 'form-sync']) }}

    <div class="grid-x modal-content inputs">

        <div class="small-4 cell">
            <ul class="vertical menu">
                @forelse ($catalogs_items as $catalogs_item)

                <li class="item-catalog" id="catalogs_services_items-{{ $catalogs_item->id }}">
                    <a class="get-prices">{{ $catalogs_item->name }}</a>
                </li>

                @empty
                {{-- empty expr --}}
                @endforelse
            </ul>

            {{-- @if ($catalogs_items->isNotEmpty())
            <ul class="vertical menu drilldown" data-drilldown data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'>

                @foreach ($catalogs_items as $catalogs_item)
                @if(is_null($catalogs_item->parent_id))

                {{-- Если категория
                <li class="item-catalog" id="catalogs_services_items-{{ $catalogs_item->id }}">
                    <a class="get-prices">{{ $catalogs_item->name }}</a>

                    @if($catalogs_item->childs->isNotEmpty())
                    <ul class="menu vertical nested">
                        @include('prices_services.sync.catalogs_items_childs', ['catalogs_items' => $catalogs_item->childs])
                    </ul>
                    @endif

                </li>

                @endif
                @endforeach

            </ul>
            @endif --}}

        </div>


        <div class="small-8 cell">
            <table id="table-prices" class="table-compositions">

                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Цена</th>
                    </tr>
                </thead>

                @forelse ($grouped_prices_services as $catalogs_item_id => $prices_services)

                <tbody class="catalogs_services_items-{{ $catalogs_item_id }}">
                    @foreach ($prices_services as $prices_service)
                    <tr>
                        <td>{{ $prices_service->service->process->name }}</td>
                        <td>{!! Form::number('prices['.$prices_service->id.']price', !is_null($prices_service->follower) ? $prices_service->follower->price : null, ['placeholder' => $prices_service->price]) !!}</td>
                    </tr>
                    @endforeach
                </tbody>

                @empty
                {{-- empty expr --}}
                @endforelse

            </table>
        </div>
        {!! Form::hidden('filial_id', $filial_id, []) !!}
    </div>

    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Сохранить', ['class' => 'button modal-button', 'id' => 'submit-add']) }}
        </div>
    </div>

    {{ Form::close() }}

    <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>
</div>
