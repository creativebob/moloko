<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-options" aria-selected="true">Общая информация</a>
            </li>

            @if($domain->exists)
            <li class="tabs-title">
                <a data-tabs-target="tab-plugins" href="#tab-plugins">Плагины</a>
            </li>
@endif

        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="tabs-panel is-active" id="tab-options">

                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-5 cell">

                        <label>Домен
                            @include('includes.inputs.varchar', ['value' => $domain->domain, 'name' => 'domain', 'required' => true, 'check' => true])
                            <div class="sprite-input-right find-status" id="name-check"></div>
                            <div class="item-error">Такой сайт уже существует!</div>
                        </label>

                        <label>Сайт
                            @include('includes.selects.sites')
                        </label>

                        <label>Стартовая ссылка
                            {{ Form::text(('start_url'), $domain->start_url,
                                [
                                    'class' => 'varchar-field name-field',
                                    'maxlength' => '255',
                                    'autocomplete' => 'off',
                                    'pattern'=>'[A-Za-zА-Яа-яЁё0-9\W\s]{3,255}',
                                ]
                                ) }}
{{--                            @include('includes.inputs.name', ['name' => 'start_url', 'value' => $domain->start_url])--}}
                        </label>

                    </div>

                    <div class="small-12 medium-7 cell">

                        <fieldset class="fieldset-access">
                            <legend>Филиалы</legend>
                            @include('includes.lists.filials')
                        </fieldset>
                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $domain])

                    <div class="small-12 cell">
                        <div class="item-error" id="filial-error">Выберите минимум 1 филиал!</div>
                    </div>

                    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                        {{ Form::submit($submit_text, ['class'=>'button domain-button']) }}
                    </div>
                </div>
            </div>

            @if($domain->exists)
            <div class="tabs-panel" id="tab-plugins">

                @include('system.pages.domains.plugins')

            </div>
            @endif

        </div>
    </div>
</div>


@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.check', ['entity' => 'domains'])

@include('system.pages.domains.scripts')
@endpush

