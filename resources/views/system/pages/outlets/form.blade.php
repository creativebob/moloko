<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Информация</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-taxation_types" href="#tab-taxation_types">Системы налогообложения</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-staff" href="#tab-staff">Сотрудники</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-catalogs_goods" href="#tab-catalogs_goods">Каталоги товаров</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-settings" href="#tab-settings">Настройки</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div data-tabs-content="tabs">

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-general">
                @include('system.pages.outlets.tabs.general')
            </div>

            <div class="tabs-panel" id="tab-taxation_types">
                @include('system.pages.outlets.tabs.taxation_types')
            </div>

            <div class="tabs-panel" id="tab-staff">
                @include('system.pages.outlets.tabs.staff', ['filialId' => $outlet->filial_id])
            </div>

            <div class="tabs-panel" id="tab-catalogs_goods">
                @include('system.pages.outlets.tabs.catalogs_goods', ['filialId' => $outlet->filial_id])
            </div>

            <div class="tabs-panel" id="tab-settings">
                @include('system.pages.outlets.tabs.settings')
            </div>

            <div class="grid-x grid-padding-x">

                {!! Form::hidden('is_main', 0) !!}
                    <div class="cell small-12 checkbox">
                        {!! Form::checkbox('is_main', 1, $outlet->is_main, ['id' => 'checkbox-is_main']) !!}
                        <label for="checkbox-is_main"><span>Главная</span></label>
                    </div>

                {{-- Чекбоксы управления --}}
                @include('includes.control.checkboxes', ['item' => $outlet])

                <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submit_text, ['class' => 'button']) }}
                </div>
            </div>


        </div>
    </div>
</div>
