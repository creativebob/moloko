<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Информация</a>
            </li>

            @isset($workplace->outlet)
            <li class="tabs-title">
                <a data-tabs-target="tab-staff" href="#tab-staff">Сотрудники</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-tools" href="#tab-tools">Оборудование</a>
            </li>
            @endisset
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div data-tabs-content="tabs">

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-general">
                @include('system.pages.workplaces.tabs.general')
            </div>

            @isset($workplace->outlet)
            <div class="tabs-panel" id="tab-staff">
                @include('system.pages.workplaces.tabs.staff')
            </div>

            <div class="tabs-panel" id="tab-tools">
                @include('system.pages.workplaces.tabs.tools')
            </div>
            @endisset

            <div class="grid-x grid-padding-x">

                {{-- Чекбоксы управления --}}
                @include('includes.control.checkboxes', ['item' => $workplace])

                <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submit_text, ['class' => 'button']) }}
                </div>
            </div>


        </div>
    </div>
</div>
