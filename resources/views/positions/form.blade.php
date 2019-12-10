<div class="grid-x grid-padding-x inputs">

    <div class="small-12 medium-7 large-5 cell tabs-margin-top">
        @if ($errors->any())
        <div class="alert callout" data-closable>
            <h5>Неправильный формат данных:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <!-- Должность -->
        <label>Название должности
            @include('includes.inputs.name', ['value'=>$position->name, 'name'=>'name', 'required' => true])
            <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>

        @if (auth()->user()->god)
        <label>Страница должности:
            @include('includes.selects.pages', ['site_id' => 1])
        </label>
        @endif

        {{-- Чекбоксы управления --}}
        @include('includes.control.checkboxes', ['item' => $position])

        <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
            {{ Form::submit($submitButtonText, ['class'=>'button position-button']) }}
        </div>

    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
        <div class="grid-x tabs-wrap align-center tabs-margin-top">

            <div class="small-12 cell">
                <ul class="tabs-list" data-tabs id="tabs">
                    @can('index', App\Role::class)
                    <li class="tabs-title is-active">
                        <a href="#tab-roles" aria-selected="true">Роли</a>
                    </li>
                    @endcan

                    <li class="tabs-title">
                        <a data-tabs-target="tab-notifications" href="#tab-notifications">Оповещения</a>
                    </li>

                    <li class="tabs-title">
                        <a data-tabs-target="tab-charges" href="#tab-charges">Обязанности</a>
                    </li>

                    <li class="tabs-title">
                        <a data-tabs-target="tab-widgets" href="#wtab-idgets">Виджеты</a>
                    </li>
                </ul>
            </div>
            <div class="small-12 cell">
                <div class="tabs-content" data-tabs-content="tabs">

                    <!-- Роли -->
                    @can('index', App\Role::class)
                    <div class="tabs-panel is-active" id="tab-roles">
                        <fieldset class="fieldset-access">
                            <legend>Настройка доступа</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 cell">
                                    @include('includes.lists.roles')
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    @endcan

                    <!-- Оповещения -->
                    <div class="tabs-panel" id="tab-notifications">
                        <fieldset class="fieldset-access">
                            <legend>Настройка оповещений</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 cell">

                                    @include('includes.lists.notifications', ['site_id' => 1])

                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <!-- Оповещения -->
                    <div class="tabs-panel" id="tab-charges">
                        <fieldset class="fieldset-access">
                            <legend>Настройка обязанностей</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 cell">

                                    @include('includes.lists.charges')

                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <!-- Виджеты -->
                    <div class="tabs-panel" id="tab-widgets">
                        <fieldset class="fieldset-access">
                            <legend>Настройка виджетов</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 cell">

                                    @include('includes.lists.widgets')

                                </div>
                            </div>
                        </fieldset>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

