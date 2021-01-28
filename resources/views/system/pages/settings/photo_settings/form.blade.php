<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Информация</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-settings" href="#tab-settings">Настройка</a>
            </li>
        </ul>
    </div>
</div>

{{-- Контейнер для разграничения --}}

<div data-tabs-content="tabs" class="inputs tabs-margin-top">

    {{-- Первый таб --}}
    <div class="tabs-panel is-active" id="tab-general">
        @include('system.pages.settings.photo_settings.tabs.general')
    </div>
    {{-- Конец первого таба --}}

    {{-- Настройки фотографий --}}
    <div class="tabs-panel" id="tab-settings">
        @include('system.pages.settings.photo_settings.tabs.settings')
    </div>

    <div class="grid-x grid-padding-x">
        {{-- Чекбоксы управления --}}
        @include('includes.control.checkboxes', ['item' => $photoSetting])

        <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
            {{ Form::submit($submitText, ['class' => 'button']) }}
        </div>
    </div>

</div>
{{-- Конец контейнера для разграничения --}}


@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
