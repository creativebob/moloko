<div class="reveal rev-small" id="modal-goods-{{ $item->id }}-archive" data-reveal>
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Архивация "{{ $item->article->name }}"</h5>
        </div>
    </div>
    <div class="grid-x align-center modal-content ">
        <ul>
            @if($item->stocks->sum('count') > 0)
                <li class="small-10 cell">
                    <h6>На складе присутствуют остатки товара: {{ $item->stocks->sum('count') }} </h6>
                    <ol>
                        @foreach($item->stocks as $stock)
                            <li>
                                {{--                        <a href="{{ route('goods_stocks.edit', $stock->id) }}" target="_blank">--}}
                                {{ $stock->stock->name }}
                                : {{ num_format($stock->count, 2) }} {{ $item->article->unit->abbreviation }}
                                {{--                        </a>--}}
                            </li>
                        @endforeach
                    </ol>
                </li>
            @endif

            @if($item->in_kits->isNotEmpty())
                <li class="small-10 cell">
                    <h6>Товар находится в наборах:</h6>
                    <ol>
                        @foreach($item->in_kits as $kit)
                            <li>
                                <a href="{{ route('goods.edit', $kit->cur_goods->id) }}"
                                   target="_blank">{{ $kit->name }}</a>
                            </li>
                        @endforeach
                    </ol>
                </li>
            @endif

            @if($item->related->isNotEmpty())
                <li class="small-10 cell">
                    <h6>Товар является сопутствующим для товаров:</h6>
                    <ol>
                        @foreach($item->related as $related)
                            <li>
                                <a href="{{ route('goods.edit', $related->id) }}"
                                   target="_blank">{{ $related->article->name }}</a>
                            </li>
                        @endforeach
                    </ol>
                </li>
            @endif

            @if($item->prices->isNotEmpty())
                <li class="small-10 cell">
                    <h6>Товар находится в прайсах:</h6>
                    <ol>
                        @foreach($item->prices as $price)
                            <li>{{ $price->catalog->name }} - {{ $price->catalogs_item->name }}</li>

                            @if($price->promotions->isNotEmpty())
                                <ol>
                                    @foreach($price->promotions as $promotion)
                                        <li>{{ $promotion->name }}</li>
                                    @endforeach
                                </ol>
                            @endif
                        @endforeach
                    </ol>
                </li>
            @endif
        </ul>
    </div>
    @can('delete', $item)
        <div class="grid-x align-center grid-padding-x">
            @if($item->stocks->sum('count') == 0)
                <div class="cell small-12">
                    При архивации все связи будут удалены!
                </div>
                <div class="small-4 cell">
                    <a href="{{ route('goods.archive', $item->id) }}" class="button modal-button">В архив</a>
                </div>
                <div class="small-4 cell">
                    <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
                </div>
            @else
                <div class="cell small-12">
                    <p>Архивация невозможна!</p><br>
                </div>
            @endif
        </div>

    @endcan
    <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>
</div>
