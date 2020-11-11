<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Общая информация</a>
            </li>

            {{-- Подключаемые специфические разделы --}}
            @isset($manufacturer)
                <li class="tabs-title">
                    <a data-tabs-target="tab-manufacturer" href="#tab-manufacturer">Рабочая информация</a>
                </li>
            @endisset
            @if(!empty($dealer))
                <li class="tabs-title">
                    <a data-tabs-target="content-panel-dealer" href="#content-panel-dealer">Рабочая информация</a>
                </li>
            @endif
            @isset($supplier)
                <li class="tabs-title">
                    <a data-tabs-target="tab-supplier" href="#tab-supplier">Информация о поставщике</a>
                </li>
            @endisset
            @isset($vendor)
                <li class="tabs-title">
                    <a data-tabs-target="tab-vendor" href="#tab-vendor">Информация о вендоре</a>
                </li>
            @endisset
            @isset($agent)
                <li class="tabs-title">
                    <a data-tabs-target="tab-agent" href="#tab-agent">Информация об агенте</a>
                </li>
            @endisset            
            @isset($client)
                <li class="tabs-title">
                    <a data-tabs-target="tab-client" href="#tab-client">О клиенте</a>
                </li>
            @endisset

            <li class="tabs-title">
                <a data-tabs-target="tab-details" href="#tab-details">Реквизиты</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-about" href="#tab-about">Описание</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-worktime" href="#tab-worktime">График работы</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-brand" href="#tab-brand">Брендирование</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-options" href="#tab-options">Настройка</a>
            </li>

            @if($company->id == auth()->user()->company_id)
                @can('index', App\CompaniesSetting::class)
                    <li class="tabs-title">
                        <a data-tabs-target="tab-settings" href="#tab-settings">Настройки работы</a>
                    </li>
                @endcan
            @endif

        </ul>
    </div>
</div>

<div class="tabs-wrap inputs tabs-margin-top">
    <div class="tabs-content" data-tabs-content="tabs">

        {{-- Общая информация --}}
        <div class="tabs-panel is-active" id="tab-general">
            @include('system.pages.companies.tabs.general')
        </div>

        {{-- Реквизиты --}}
        <div class="tabs-panel" id="tab-details">
            @include('system.pages.companies.tabs.details')
        </div>

        {{-- Описание компании --}}
        <div class="tabs-panel" id="tab-about">
            @include('system.pages.companies.tabs.about')
        </div>

        {{-- Брендирование --}}
        <div class="tabs-panel" id="tab-brand">
            @include('system.pages.companies.tabs.brand')
        </div>

        {{-- Настройки --}}
        @if($company->id == auth()->user()->company_id)
            @can('index', App\CompaniesSetting::class)
                <div class="tabs-panel" id="tab-settings">
                    @include('system.pages.companies.tabs.settings')
                </div>
            @endcan
        @endif

        {{-- График работы --}}
        <div class="tabs-panel" id="tab-worktime">
            @include('system.pages.companies.tabs.worktime')
        </div>

        {{-- Настройки --}}
        <div class="tabs-panel" id="tab-options">
            @include('system.pages.companies.tabs.options')

        </div>

        {{-- Блок производителя --}}
        @isset($manufacturer)
            <div class="tabs-panel" id="tab-manufacturer">
                @include('system.pages.erp.manufacturers.form')
            </div>
        @endisset

        @if(!empty($dealer))
        <!-- Блок дилера -->
            <div class="tabs-panel" id="content-panel-dealer">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">
                        <label>Комментарий к дилеру
                            @include('includes.inputs.textarea', ['name'=>'description_dealer', 'value'=>$dealer->description_dealer])
                        </label>
                    </div>
                    <div class="small-6 medium-3 cell">
                        <label>Скидка
                            @include('includes.inputs.digit', ['name'=>'discount', 'value'=>$dealer->discount])
                        </label>
                    </div>
                </div>
            </div>
            <!-- Конец блока дилера -->
        @endif

        {{-- Блок поставщика --}}
        @isset($supplier)
            <div class="tabs-panel" id="tab-supplier">
                @include('system.pages.erp.suppliers.form')
            </div>
        @endisset

        {{-- Блок вендора --}}
        @isset($vendor)
            <div class="tabs-panel" id="tab-vendor">
                @include('system.pages.erp.vendors.form')
            </div>
        @endisset

        {{-- Блок агента --}}
        @isset($agent)
            <div class="tabs-panel" id="tab-agent">
                @include('system.pages.sales.agents.form')
            </div>
        @endisset

        {{-- Клиент --}}
        @isset($client)
            <div class="tabs-panel" id="tab-client">
                @include('system.pages.clients.form')
            </div>
        @endisset

    </div>

    <div class="grid-x">
        <div class="cell small-12 large-5">
            <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                {{ Form::submit($submitButtonText, ['class' => 'button']) }}
            </div>
        </div>
    </div>
</div>
</div>
