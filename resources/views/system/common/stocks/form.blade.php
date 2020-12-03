<div class="grid-x">
    <div class="auto cell">
        <div class="grid-x tabs-wrap">
            <div class="small-12 cell">
                <ul class="tabs-list" data-tabs id="tabs">

                    <li class="tabs-title is-active">
                        <a href="#tab-general" aria-selected="true">Информация</a>
                    </li>
                    <li class="tabs-title">
                        <a href="#tab-receipt" aria-selected="true">Поступления</a>
                    </li>
                    <li class="tabs-title">
                        <a href="#tab-offs" aria-selected="true">Списания</a>
                    </li>
                    <li class="tabs-title">
                        <a href="#tab-reserve" aria-selected="true">Резервы</a>
                    </li>

                </ul>
            </div>
        </div>
        <div class="grid-x tabs-wrap">
            <div class="cell small-12">
                <ul class="menu list-indicator">
                    <li>
                        <span class="attribute-indicator">Количество: </span><span class="value-indicator">{{ num_format($stock->count, 0) }}</span> 
                    </li>
                    <li>
                        <span class="attribute-indicator">Доступно: </span><span class="value-indicator">{{ num_format($stock->free, 0) }}</span> 
                    </li>
                    <li>
                        <span class="attribute-indicator">Резерв: </span><span class="value-indicator">{{ num_format($stock->reserve, 0) }}</span> 
                    </li>
                    <li>
                        <span class="attribute-indicator">Ценность: </span><span class="value-indicator">{{ num_format($stock->stock_cost, 0) }}</span> 
                    </li>
                    <li>
                        <span class="attribute-indicator">Вес: </span><span class="value-indicator">{{ num_format($stock->weight, 2) }}</span> 
                    </li>
                </ul>
            </div>
        </div>
        <div class="grid-x tabs-wrap inputs">
            <div class="small-12 cell tabs-margin-top">
                <div data-tabs-content="tabs">

                    <div class="tabs-panel is-active" id="tab-general">
                        @include('system.common.stocks.tabs.general')
                    </div>
                    <div class="tabs-panel" id="tab-receipt">
                        @include('system.common.stocks.tabs.receipt')
                    </div>
                    <div class="tabs-panel" id="tab-offs">
                        @include('system.common.stocks.tabs.offs')
                    </div>
                    <div class="tabs-panel" id="tab-reserve">
                        @include('system.common.stocks.tabs.reserve')
                    </div>




                    <div class="grid-x grid-padding-x">
                        {{-- Чекбоксы управления --}}
                        {{-- @include('includes.control.checkboxes', ['item' => $stock]) --}}

                        <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                            {{ Form::submit($submitText, ['class' => 'button']) }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="shrink cell">

    </div>
</div>



