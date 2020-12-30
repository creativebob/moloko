<div class="grid-x tabs-wrap">
    <div class="cell small-12">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Основные настройки</a>
            </li>
            @if($catalogServices->exists)
                <li class="tabs-title">
                    <a href="#tab-agency_schemes" aria-selected="true">Агентские схемы</a>
                </li>
            @endif
        </ul>
    </div>
</div>

<div class="tabs-wrap tabs-margin-top inputs">
    <div class="tabs-content" data-tabs-content="tabs">

        <div class="tabs-panel is-active" id="tab-general">
            @include('system.pages.catalogs.services.catalogs_services.tabs.general')
        </div>
        @if($catalogServices->exists)
            <div class="tabs-panel" id="tab-agency_schemes">
                @include('system.pages.catalogs.services.catalogs_services.tabs.agency_schemes')
            </div>
        @endif

        <div class="grid-x grid-padding-x">
            <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                {{ Form::submit($submitText, ['class' => 'button']) }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.upload-file')
@endpush
