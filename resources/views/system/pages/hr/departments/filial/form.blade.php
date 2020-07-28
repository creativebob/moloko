<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">

            <li class="tabs-title is-active">
                <a href="#tab-department" aria-selected="true">Филиал</a>
            </li>

            @can('index', App\Schedule::class)
                <li class="tabs-title">
                    <a data-tabs-target="tab-worktimes" href="#tab-worktimes">График работы</a>
                </li>
            @endcan

            @can('index', App\Site::class)
                <li class="tabs-title">
                    <a data-tabs-target="tab-site" href="#tab-site">Настройки для сайта</a>
                </li>
            @endcan

            <li class="tabs-title">
                <a data-tabs-target="tab-responsibility" href="#tab-responsibility">Зона ответственности</a>
            </li>

            {{--                    @can('index', App\Setting::class)--}}
            {{--                        <li class="tabs-title">--}}
            {{--                            <a data-tabs-target="tab-settings" href="#tab-settings">Настройки для продаж</a>--}}
            {{--                        </li>--}}
            {{--                    @endcan--}}


        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="tabs-panel is-active" id="tab-department">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-4 cell">

                        {{-- Добавление города --}}
                        {{-- Город --}}
                        @include('system.common.includes.city_search', ['item' => $department, 'required' => isset($parent_id) ? null : true])

                        <label>Название
                            @include('includes.inputs.name', ['value' => $department->name, 'required' => true])
                            <div class="item-error">Такой филиал уже существует в организации!</div>
                        </label>

                        <label>Адрес
                            @include('includes.inputs.address', ['value' => optional($department->location)->address, 'name'=>'address'])
                        </label>

                        <label>Телефон
                            @include('includes.inputs.phone', ['value' => optional($department->main_phone)->phone, 'name' => 'main_phone', 'required' => true])
                        </label>

                        @if (count($department->extra_phones) > 0)
                            @foreach ($department->extra_phones as $extra_phone)
                                @include('includes.extra-phone', ['extra_phone' => $extra_phone])
                            @endforeach
                        @else
                            @include('includes.extra-phone')
                        @endif

                        <label>Почта
                            @include('includes.inputs.email', ['value' => $department->email, 'name' => 'email'])
                        </label>

                        @include('includes.control.checkboxes', ['item' => $department])

                        <div class="grid-x">
                            <div class="small-6 medium-4 cell">
                                {{ Form::submit($submitButtonText, ['class' => 'button modal-button']) }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @can('index', App\Schedule::class)
                {{-- Схема работы --}}
                <div class="tabs-panel" id="tab-worktimes">
                    <div class="grid-x grid-padding-x">
                        <div class="small-12 medium-6 cell">
                            @include('includes.inputs.schedule', ['worktime'=>$department->worktime])
                        </div>
                    </div>
                </div>
            @endcan

            @can('index', App\Site::class)
                @empty($parent_id)
                    {{-- Сайт --}}
                    <div class="tabs-panel" id="tab-site">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 medium-5 cell">
                                <label>Код для карты
                                    {!! Form::textarea('code_map', null, []) !!}
                                </label>
                            </div>
                        </div>
                    </div>
                @endempty
            @endcan

            {{-- Сайт --}}
            <div class="tabs-panel" id="tab-responsibility">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-4 cell">
                        <fieldset class="fieldset-access">
                            <legend>Населенные пункты</legend>
                                @include('includes.lists.cities')
                        </fieldset>
                    </div>
                </div>
            </div>

            {{-- Настройки дял продаж --}}
            {{--            @can('index', App\Setting::class)--}}
            {{--                @empty($parent_id)--}}
            {{--                    <div class="tabs-panel" id="tab-settings">--}}
            {{--                        <div class="grid-x grid-padding-x align-center">--}}
            {{--                            <div class="small-8 cell">--}}
            {{--                                @include('system.common.includes.settings.list', ['item' => $department])--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                @endempty--}}
            {{--            @endcan--}}

        </div>
    </div>
</div>
