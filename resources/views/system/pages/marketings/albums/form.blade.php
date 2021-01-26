<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Информация об альбоме</a>
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
        @include('system.pages.marketings.albums.tabs.general')
    </div>
    {{-- Конец первого таба --}}

    {{-- Настройки фотографий --}}
    <div class="tabs-panel" id="tab-settings">
        @include('includes.photos_settings.tab', ['item' => $album])
    </div>

    <div class="grid-x grid-padding-x">
        {{-- Чекбоксы управления --}}
        @include('includes.control.checkboxes', ['item' => $album])

        <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
            {{ Form::submit($submitText, ['class' => 'button']) }}
        </div>
    </div>

</div>
{{-- Конец контейнера для разграничения --}}


@push('scripts')
    @include('includes.scripts.inputs-mask')
    @include('system.pages.marketings.albums.scripts')
    {{-- Проверка поля на существование --}}
    @include('includes.scripts.check', ['entity' => 'albums'])
@endpush
