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

        @if (\Auth::user()->god)
        <label>Страница должности:
            {{ Form::select('page_id', $pages_list, null, ['id'=>'page-select']) }}
        </label>
        @endif

        {{-- Чекбоксы управления --}}
        @include('includes.control.checkboxes', ['item' => $position])

        <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
            {{ Form::submit($submitButtonText, ['class'=>'button position-button', 'disabled']) }}
        </div>

    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
        <div class="grid-x tabs-wrap align-center tabs-margin-top">

            <div class="small-12 cell">
                <ul class="tabs-list" data-tabs id="tabs">
                    @can('index', App\Role::class)
                    <li class="tabs-title is-active">
                        <a href="#roles" aria-selected="true">Роли</a>
                    </li>
                    @endcan

                    <li class="tabs-title">
                        <a data-tabs-target="notifications" href="#notifications">Оповещения</a>
                    </li>

                    <li class="tabs-title">
                        <a data-tabs-target="charges" href="#charges">Обязанности</a>
                    </li>

                    <li class="tabs-title">
                        <a data-tabs-target="widgets" href="#widgets">Виджеты</a>
                    </li>
                </ul>
            </div>
            <div class="small-12 cell">
                <div class="tabs-content" data-tabs-content="tabs">

                    <!-- Роли -->
                    @can('index', App\Role::class)
                    <div class="tabs-panel is-active" id="roles">
                        <fieldset class="fieldset-access">
                            <legend>Настройка доступа</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 cell">

                                    <ul>
                                        @foreach ($roles as $role)
                                            @if ($role->id != 1 || ($role->id == 1) && (\Auth::user()->god))
                                                <li>
                                                    <div class="small-12 cell checkbox">
                                                        {{ Form::checkbox('roles[]', $role->id, null, ['id'=>'role-'.$role->id, 'class'=>'access-checkbox']) }}
                                                        <label for="role-{{ $role->id }}"><span>{{ $role->name }}</span></label>
                                                        @php
                                                            $allow = count($role->rights->where('directive', 'allow'));
                                                            $deny = count($role->rights->where('directive', 'deny'));
                                                        @endphp
                                                        (<span class="allow">{{ $allow }}</span> / <span class="deny">{{ $deny }}</span>)
                                                    </div>
                                                </li>
                                            @endif

                                        @endforeach
                                    </ul>

                                </div>
                            </div>
                        </fieldset>
                    </div>
                    @endcan

                    <!-- Оповещения -->
                    <div class="tabs-panel" id="notifications">
                        <fieldset class="fieldset-access">
                            <legend>Настройка оповещений</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 cell">

                                    <ul>
                                        @foreach ($notifications as $notification)

                                            @php
                                                $model = 'App\\' . $notification->trigger->entity->model;
                                            @endphp
                                        @can('index', $model)
                                        <li>
                                            <div class="small-12 cell checkbox">
                                                {{ Form::checkbox('notifications[]', $notification->id, null, ['id'=>'notification-'.$notification->id, 'class'=>'access-checkbox']) }}
                                                <label for="notification-{{ $notification->id }}"><span>{{ $notification->name }}</span></label>
                                            </div>
                                        </li>
                                            @endcan
                                        @endforeach
                                    </ul>

                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <!-- Оповещения -->
                    <div class="tabs-panel" id="charges">
                        <fieldset class="fieldset-access">
                            <legend>Настройка обязанностей</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 cell">

                                    <ul>
                                        @foreach ($charges as $charge)
                                        <li>
                                            <div class="small-12 cell checkbox">
                                                {{ Form::checkbox('charges[]', $charge->id, null, ['id'=>'charge-'.$charge->id, 'class'=>'access-checkbox']) }}
                                                <label for="charge-{{ $charge->id }}"><span>{{ $charge->name }}</span></label>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>

                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <!-- Виджеты -->
                    <div class="tabs-panel" id="widgets">
                        <fieldset class="fieldset-access">
                            <legend>Настройка виджетов</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 cell">

                                    <ul>
                                        @foreach ($widgets as $widget)
                                        <li>
                                            <div class="small-12 cell checkbox">
                                                {{ Form::checkbox('widgets[]', $widget->id, null, ['id'=>'widget-'.$widget->id, 'class'=>'access-checkbox']) }}
                                                <label for="widget-{{ $widget->id }}"><span>{{ $widget->name }}</span></label>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>

                                </div>
                            </div>
                        </fieldset>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

