<div class="grid-x tabs-wrap">
    <div class="cell small-12">
        <ul class="tabs-list" data-tabs id="tabs">

            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Учетные данные</a>
            </li>

            @isset($employee)
                <li class="tabs-title">
                    <a data-tabs-target="tab-employee" href="#tab-employee">Должность</a>
                </li>
            @endisset

            @isset($client)
                <li class="tabs-title">
                    <a data-tabs-target="tab-client" href="#tab-client">О клиенте</a>
                </li>
            @endisset

            @isset($dealer)
                <li class="tabs-title">
                    <a data-tabs-target="content-panel-dealer" href="#content-panel-dealer">Информация о дилере</a>
                </li>
            @endisset

            <li class="tabs-title">
                <a data-tabs-target="tab-personal" href="#tab-personal">Персональные данные</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-education" href="#tab-education">Образование и опыт</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-options" href="#tab-options">Настройки</a>
            </li>
            {{-- <li class="tabs-title"><a data-tabs-target="content-panel-3" href="#content-panel-3">Представитель компании</a></li> --}}

        </ul>
    </div>
</div>

<div class="tabs-wrap tabs-margin-top inputs">
    <div class="tabs-content" data-tabs-content="tabs">
        {{-- Учетные данные --}}
        <div class="tabs-panel is-active" id="tab-general">
            @include('system.pages.marketings.users.tabs.general')
        </div>

        {{-- Персональные данные --}}
        <div class="tabs-panel" id="tab-personal">
            @include('system.pages.marketings.users.tabs.personal')
        </div>

        {{-- Образование и опыт --}}
        <div class="tabs-panel" id="tab-education">
            @include('system.pages.marketings.users.tabs.education')
        </div>

        {{-- Настройки --}}
        <div class="tabs-panel" id="tab-options">
            @include('system.pages.marketings.users.tabs.options')
        </div>

        {{-- Блок сотрудника --}}
        @isset($employee)
            <div class="tabs-panel" id="tab-employee">
                @include('system.pages.hr.employees.form')
            </div>
        @endisset

        {{-- Блок клиента --}}
        @isset($client)
            <div class="tabs-panel" id="tab-client">
                @include('system.pages.clients.form')
            </div>
        @endisset

        {{-- Блок дилера --}}
        @if(!empty($dealer))

            <div class="tabs-panel" id="content-panel-dealer">
                <div class="grid-x grid-padding-x tabs-margin-top">
                    <div class="small-12 medium-6 cell">
                        <label>Комментарий к дилеру
                            @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$dealer->description])
                        </label>
                    </div>
                    <div class="small-6 medium-3 cell">
                        <label>Скидка
                            @include('includes.inputs.digit', ['name'=>'discount', 'value'=>$dealer->discount])
                        </label>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid-x grid-padding-x">
            <div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
                {{ Form::submit($submitButtonText, ['class'=>'button']) }}
            </div>
        </div>

    </div>
</div>
