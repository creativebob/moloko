<div class="grid-x grid-padding-x inputs tabs-margin-top">
    <div class="cell small-12 medium-12 large-6">

        <div class="grid-x tabs-wrap">
            <div class="cell small-12">
                <ul class="tabs-list" data-tabs id="tabs">
                    <li class="tabs-title is-active">
                        <a href="#tab-general" aria-selected="true">Основные настройки</a>
                    </li>
                    <li class="tabs-title">
                        <a href="#tab-export" aria-selected="true">Выгрузка</a>
                    </li>
                    <li class="tabs-title">
                        <a href="#tab-scheme-agancy" aria-selected="true">Агентские схемы</a>
                    </li>
                </ul>

            </div>
        </div>

        <div class="grid-x tabs-wrap inputs">
            <div class="small-12 cell tabs-margin-top">
                <div data-tabs-content="tabs">

                    <div class="tabs-panel is-active" id="tab-general">
                        @include('catalogs_goods.tabs.general')
                    </div>
                    <div class="tabs-panel" id="tab-export">
                        @include('catalogs_goods.tabs.export')
                    </div>
                    <div class="tabs-panel" id="tab-scheme-agancy">
                        @include('catalogs_goods.tabs.scheme_agancy')
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="small-12 medium-5 large-7 cell">

            <label>Выберите аватар
                {{ Form::file('photo') }}
            </label>

            <div class="text-center">
                <img id="photo" src="{{ getPhotoPath($catalog) }}">
            </div>
        </div> --}}

        <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
            {{ Form::submit($submit_text, ['class' => 'button']) }}
        </div>
    </div>
</div>
