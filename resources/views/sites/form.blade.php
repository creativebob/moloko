<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#options" aria-selected="true">Общая информация</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="plugins" href="#plugins">Плагины</a>
            </li>

            {{-- Табы для сущности --}}
            @includeIf($page_info->entity->view_path . '.tabs')

        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="tabs-panel is-active" id="options">

                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-5 cell">

                        {{-- Сайт --}}
                        <label>Название сайта
                            @include('includes.inputs.name', ['value' => $site->name, 'required' => true])
                        </label>

                        <label>Домен сайта
                            @include('includes.inputs.varchar', ['value' => $site->domain, 'name' => 'domain', 'required' => true, 'check' => true])
                            <div class="sprite-input-right find-status" id="name-check"></div>
                            <div class="item-error">Такой сайт уже существует!</div>
                        </label>
                    </div>

                    <div class="small-12 medium-7 cell">

                        <fieldset class="fieldset-access">
                            <legend>Филиалы</legend>
                            @include('sites.filials_list')
                        </fieldset>
                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $site])

                    <div class="small-12 cell">
                        <div class="item-error" id="filial-error">Выберите минимум 1 филиал!</div>
                    </div>

                    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                        {{ Form::submit($submit_text, ['class'=>'button site-button']) }}
                    </div>
                </div>
            </div>

            <div class="tabs-panel" id="plugins">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-5 cell">
                        кек
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.check', ['entity' => 'sites'])

@include('sites.scripts')
{{-- <script type="text/javascript" src="/crm/js/vendor/what-input.js"></script> --}}
@endpush

