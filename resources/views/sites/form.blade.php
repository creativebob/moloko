<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#options" aria-selected="true">Общая информация</a>
            </li>

            @if($site->exists)
            <li class="tabs-title">
                <a data-tabs-target="plugins" href="#plugins">Плагины</a>
            </li>
@endif

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

                        <label>Стартовая ссылка
                            @include('includes.inputs.name', ['name' => 'start_url', 'value' => $site->start_url])
                        </label>
                    </div>

                    <div class="small-12 medium-7 cell">

                        <fieldset class="fieldset-access">
                            <legend>Филиалы</legend>
                            @include('includes.lists.filials')
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

            @if($site->exists)
            <div class="tabs-panel" id="plugins">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-5 cell">
                        <a class="button" id="button-create-plugin">Добавить плагин</a>

                        <table id="table-plugins">
                            <thead>
                            <tr>
                                <th>Аккаунт</th>
                                <th>Действия</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($site->plugins as $plugin)
                            @include('sites.plugins.plugin')
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.check', ['entity' => 'sites'])

@include('sites.scripts')
@endpush

