<lead-init-component
    :lead="{{ $lead }}"
    :outlet-id="{{ $lead->outlet_id }}"
></lead-init-component>

<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ЛИД №:<input id="show-case-number" name="show_case_number" readonly
                                                class="case_number_field" value="{{ $lead->case_number }}"></h2>
    </div>
    <div class="top-bar-right wrap_lead_badget">

        <lead-errors-component></lead-errors-component>
        {{-- @include('includes.inputs.digit', ['name' => 'badget', 'value' => $lead->badget, 'decimal_place'=>2]) --}}
    </div>
</div>

<div class="grid-x tabs-wrap inputs">

    <!-- Левый блок -->
    <div class="small-12 medium-5 large-7 cell">

    {{--       Персональная информация--}}
    @include('leads.personal', ['item' => $lead ?? auth()->user(), 'manual' => true])


    <!-- ЗАКАЗ -->
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 large-12 cell margin-left-15 wrap-tabs-lead">

                <lead-extra-tabs-component
                    @can ('index', App\Client::class)
                    :can-index-client="true"
                    @endcan

                    @if(extra_right('lead-history'))
                    :can-lead-history="true"
                    @endif
                ></lead-extra-tabs-component>

                {{-- Контент доп таба --}}
                <div data-tabs-content="tabs-extra-leads">

                    {{-- Смета --}}
                    <div class="tabs-panel is-active" id="tab-estimate">
                        @include('leads.tabs.estimate')
                    </div>

                    {{-- КЛИЕНТ --}}
                    <div class="tabs-panel" id="content-panel-client">
                        @include('leads.tabs.client')
                    </div>
                    {{-- КОНЕЦ КЛИЕНТ --}}

                    {{-- АДРЕСА --}}
                    <div class="tabs-panel" id="content-panel-address">
                        <div class="grid-x grid-padding-x">
                            <!-- <div id="port-address" class="small-12 cell">
                            </div>  -->
                        </div>
                    </div>

                    {{-- ИСТОРИЯ --}}
                    @if(extra_right('lead-history'))
                        <div class="tabs-panel" id="tab-history">
                            @include('leads.tabs.history')
                        </div>
                    @endif

                    <div class="tabs-panel" id="tab-options">
                        @include('leads.tabs.options')
                    </div>

                    {{-- Конец контента доп таба --}}
                </div>

            </div>
        </div>
    </div>


    <!-- Правый блок -->
    <div class="small-12 medium-7 large-5 cell">
        {{ Form::model($lead, [
    'route' => ['leads.update', $lead->id],
    'data-abide', 'novalidate',
    'id' => 'form-lead',
    'files' => 'true'
]) }}
        {{ method_field('PATCH') }}
        {!! Form::hidden('previous_url', url()->previous()) !!}
        <div class="grid-x tabs-right">

            <cashbox-component></cashbox-component>

            <div class="cell small-12">

                {{-- <li class="tabs-title"><a href="#content-panel-documents" aria-selected="true">Документы</a></li> --}}

                {{-- @can ('index', App\Claim::class)
                <li class="tabs-title">
                    <a data-tabs-target="content-panel-claims" href="#content-panel-claims">Рекламации</a>
                </li>
                @endcan --}}

                <lead-tabs-component
                    @if(extra_right('lead-attribution-show'))
                    :attribution="{{ extra_right('lead-attribution-show') }}"
                    @endif
                ></lead-tabs-component>

                {{-- <li class="tabs-title"><a href="#content-panel-measurements" aria-selected="true">Замеры</a></li> --}}

            </div>

            <div class="small-12 cell">

                <div class="tabs-content tabs-leads" data-tabs-content="tabs-leads">
                    {{-- Взаимодействия: задачи и события --}}
                    <div class="tabs-panel is-active" id="tab-events">
                        @include('leads.tabs.events')
                    </div>

                    {{-- КАТАЛОГ ТОВАРОВ --}}
                    <div class="tabs-panel" id="tab-catalog_goods">
                        @include('leads.tabs.catalogs_goods')
                    </div>

                    {{-- КАТАЛОГ УСЛУГ --}}
                    <div class="tabs-panel" id="tab-catalog_services">
                        @include('leads.tabs.catalogs_services')
                    </div>

                    {{-- ДОКУМЕНТЫ
                    <div class="tabs-panel" id="content-panel-documents">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 large-6 cell">
                            </div>
                        </div>
                    </div> --}}

                    {{-- РЕКЛАМАЦИИ --}}
                    <div class="tabs-panel" id="content-panel-claims">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 cell">

                                @can ('index', App\Claim::class)
                                    <fieldset class="fieldset-challenge">
                                        <legend>Рекламации:</legend>
                                        <div class="grid-x grid-padding-x">
                                            <table class="table-challenges" id="table-challenges">
                                                <thead>
                                                <tr>
                                                    <th>Дата</th>
                                                    <th>Номер</th>
                                                    <th>Обращение</th>
                                                    <th>Описание проблемы</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody id="claims-list">

                                                @if (count($lead->claims) > 0)
                                                    @include('leads.claim', ['claims' => $lead->claims])
                                                @endif

                                                </tbody>
                                            </table>
                                        </div>
                                        @can ('create', App\Claim::class)
                                            <div class="grid-x grid-padding-x align-left">
                                                <div class="small-4 cell">
                                                    @can('update', $lead)
                                                        <a class="button green-button claim-add" data-open="add-claim">Добавить</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        @endcan
                                    </fieldset>
                                @endcan


                            </div>
                        </div>
                    </div>
                    {{-- КОНЕЦ РЕКЛАМАЦИИ --}}

                    {{-- ЗАМЕРЫ
                    <div class="tabs-panel" id="content-panel-measurements">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 large-6 cell">
                            </div>
                        </div>
                    </div> --}}

                    {{-- ОПЛАТА --}}
                    <div class="tabs-panel" id="tab-payments">
                        @include('leads.tabs.payments')
                    </div>

                    {{-- Агенты --}}
                    <div class="tabs-panel" id="tab-agents">
                        @include('leads.tabs.agents')
                    </div>

                    {{-- Аттрибуция --}}
                    @if(extra_right('lead-attribution-show'))
                        <div class="tabs-panel" id="tab-attribution">
                            @include('leads.tabs.attribution')
                        </div>
                    @endif

                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
    {{-- КОНЕЦ ПРАВОГО БЛОКА --}}


    {{--    <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">--}}
    {{--        @can('update', $lead)--}}
    {{--            {{ Form::submit($submitButtonText, ['class'=>'button']) }}--}}
    {{--        @else--}}
    {{--            {{ Form::submit($submitButtonText, ['class'=>'button', $disabled_leadbot]) }}--}}
    {{--        @endcan--}}
    {{--    </div>--}}
</div>

{{-- Подключаем ПОИСК обращений и заказов по номеру телефона --}}
@include('leads.autofind-lead-script')
@include('includes.scripts.product-to-estimate-script')

@push('scripts')
    <script>
        $(document).on('click', '.item-catalog', function () {
            $('.item-catalog').each(function () {
                $(this).find('a').removeClass('is-active');
            })
            $(this).find('a').addClass('is-active');
        });

        $(document).on('click', '.is-drilldown-submenu-item', function () {
            $('.item-catalog').each(function () {
                $(this).find('a').removeClass('is-active');
            })
            $(this).find('a').addClass('is-active');
        });
    </script>
@endpush
