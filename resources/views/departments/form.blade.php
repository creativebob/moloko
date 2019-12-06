<div class="grid-x tabs-wrap tabs-margin-top align-center">
    <div class="small-8 cell">

        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#department" aria-selected="true">{{ isset($parent_id) ? 'Отдел' : 'Филиал' }}</a>
            </li>

            @can('edit', App\Schedule::class)
            <li class="tabs-title">
                <a data-tabs-target="worktime" href="#worktime">График работы</a>
            </li>
            @endcan

            @can('index', App\Site::class)
            @empty($parent_id)
            <li class="tabs-title">
                <a data-tabs-target="site" href="#site">Настройки дял сайта</a>
            </li>
            @endempty
            @endcan
        </ul>

    </div>
</div>

<div class="tabs-wrap inputs">
    <div class="tabs-content" data-tabs-content="tabs">

        {{-- Основные --}}
        <div class="tabs-panel is-active" id="department">
            <div class="grid-x grid-padding-x align-center modal-content inputs">
                <div class="small-10 cell">

                    {{-- Добавление города --}}
{{--                    <citysearch></citysearch>--}}
                    @include('includes.inputs.city_search', ['city' => isset($department->location->city->name) ? $department->location->city : null, 'id' => 'cityForm', 'required' => isset($parent_id) ? null : true])

                    @isset($parent_id)
                    <label>Расположение
                        @include('includes.selects.categories_select', ['id' => $department->id, 'parent_id' => $parent_id])
                    </label>
                    @endisset

                    <label>Название
                        @include('includes.inputs.name', ['value' => $department->name, 'required' => true])
                            <div class="item-error">Такой {{ isset($parent_id) ? 'отдел' : 'филиал' }} уже существует в {{ isset($parent_id) ? 'филиале' : 'организации' }}!</div>
                    </label>

                    <label>Адресс
                        @include('includes.inputs.address', ['value' => isset($department->location->address) ? $department->location->address : null, 'name'=>'address'])
                    </label>

                    <label>Телефон
                        @include('includes.inputs.phone', ['value' => isset($department->main_phone->phone) ? $department->main_phone->phone : null, 'name' => 'main_phone'])
                    </label>

                    <label>Почта
                        @include('includes.inputs.email', ['value' => $department->email, 'name' => 'email'])
                    </label>

                    {{ Form::hidden('id', null, ['id' => 'item-id']) }}
                    {{ Form::hidden('filial_id', null, ['id' => 'filial-id']) }}

                    @include('includes.control.checkboxes', ['item' => $department])

                </div>
            </div>
        </div>

        {{-- Схема работы --}}
        @can('edit', App\Schedule::class)
        <div class="tabs-panel" id="worktime">
            <div class="grid-x grid-padding-x align-center">
                <div class="small-8 cell">
                    @include('includes.inputs.schedule', ['worktime' => $department->worktime])
                </div>
            </div>
        </div>
        @endcan

        @can('index', App\Site::class)
        @empty($parent_id)
        {{-- Сайт --}}
        <div class="tabs-panel" id="site">
            <div class="grid-x grid-padding-x align-center">
                <div class="small-8 cell">
                    <label>Код для карты
                    {!! Form::textarea('code_map', null, []) !!}
                    </label>
                </div>
            </div>
        </div>
        @endempty
        @endcan

    </div>
</div>

<div class="grid-x align-center">
    <div class="small-6 medium-4 cell text-center">
        {{ Form::submit($submit_text, ['class' => 'button modal-button ' . $class]) }}
    </div>
</div>

<script type="application/javascript">
    $.getScript("/js/system/jquery.maskedinput.js");
    $.getScript("/js/system/inputs_mask.js");
</script>



