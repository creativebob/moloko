<div class="grid-x grid-padding-x">

    {{-- ВЫВОД ПУНКТОВ КАТАЛОГА --}}
    <div class="shrink cell catalog-bar">
        <div class="grid-x grid-padding-x">

            {{-- ПОИСК ПО ТОВАРАМ --}}
            <div class="small-12 cell search-in-catalog-panel">
                <label class="label-icon">
                    <input type="text" name="search" placeholder="Поиск" maxlength="25" autocomplete="off">
                    <div class="sprite-input-left icon-search"></div>
                    <span class="form-error">Обязательно нужно логиниться!</span>
                </label>
            </div>

            {{-- СПИСОК ПУНКТОВ КАТАЛОГА --}}

            <div class="small-12 cell search-in-catalog-panel">

                @include('leads.catalogs.catalogs_items', ['catalog' => $сatalog_goods, 'type' => 'goods'])

            </div>
        </div>
    </div>

    {{-- ВЫВОД ПРОЦЕССОВ (ТОВАРОВ) --}}
    <div class="auto cell">
        <div class="grid-x grid-padding-x">

            {{-- ПАНЕЛЬ УПРАВЛЕНИЯ ОТОБРАЖЕНИЕМ --}}
            <div class="small-12 cell view-settings-panel">
                <div class="one-icon-16 icon-view-list icon-button active" id="toggler-view-list"></div>
                <div class="one-icon-16 icon-view-block icon-button" id="toggler-view-block"></div>
                <div class="one-icon-16 icon-view-card icon-button" id="toggler-view-card"></div>
                <div class="one-icon-16 icon-view-setting icon-button" id="open-setting-view" data-open="modal-catalogs-goods"></div>
            </div>

            {{-- ВЫВОД ТОВАРОВ --}}
            <div id="block-prices_goods">
                @foreach ($сatalog_goods->items as $item)
                    <ul class="small-12 cell products-list view-list" id="block-catalog_goods_item-{{ $item->id }}">
                        @foreach($item->prices as $cur_prices_goods)
                            <li>
                                <a class="add-to-estimate" data-price_id="{{ $cur_prices_goods->id }}" data-serial="{{ $cur_prices_goods->goods->serial }}" data-type="goods">

                                    <div class="media-object stack-for-small">
                                        <div class="media-object-section items-product-img" >
                                            <div class="thumbnail">
                                                <img src="{{ getPhotoPath($cur_prices_goods->goods->article, 'small') }}">
                                            </div>
                                        </div>

                                        <div class="media-object-section cell">

                                            <div class="grid-x grid-margin-x">
                                                <div class="cell auto">
                                                    <h4>
                                                        <span class="items-product-name">{{ $cur_prices_goods->goods->article->name }}</span>
                                                        @if($cur_prices_goods->goods->article->manufacturer)
                                                            <span class="items-product-manufacturer"> ({{ $cur_prices_goods->goods->article->manufacturer->name ?? '' }})</span>
                                                        @endif
                                                    </h4>
                                                </div>

                                                <div class="cell shrink wrap-product-price">

                                                    <span class="items-product-price">{{ num_format($cur_prices_goods->price, 0) }}</span>
                                                </div>
                                            </div>
                                            <p class="items-product-description">{{ $cur_prices_goods->goods->description }}</p>
                                        </div>
                                    </div>

                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>

        </div>
    </div>
</div>