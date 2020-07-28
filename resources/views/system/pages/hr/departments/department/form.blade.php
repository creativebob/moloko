<div class="grid-x tabs-wrap tabs-margin-top align-center">
    <div class="small-8 cell">

        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-department" aria-selected="true">Отдел</a>
            </li>

            @can('index', App\Schedule::class)
                <li class="tabs-title">
                    <a data-tabs-target="tab-worktime" href="#tab-worktime">График работы</a>
                </li>
            @endcan
        </ul>

    </div>
</div>

<div class="tabs-wrap inputs">
    <div class="tabs-content" data-tabs-content="tabs">

        {{-- Основные --}}
        <div class="tabs-panel is-active" id="tab-department">
            <div class="grid-x grid-padding-x align-center modal-content inputs">
                <div class="small-10 cell">

                    {{-- Добавление города --}}
                    {{-- @include('system.common.includes.city_search', ['item' => $department, 'required' => isset($parent_id) ? null : true])--}}
                    @include('includes.scripts.class.city_search')
                    @include('includes.inputs.city_search', ['city' => optional($department->location)->city, 'id' => 'cityForm'])

                    <label>Расположение
                        @include('includes.selects.categories_select', ['id' => $department->id, 'parent_id' => $parent_id])
                    </label>

                    <label>Название
                        @include('includes.inputs.name', ['value' => $department->name, 'required' => true])
                        <div class="item-error">Такой отдел уже существует в филиале}!</div>
                    </label>

                    <label>Адрес
                        @include('includes.inputs.address', ['value' => optional($department->location)->address, 'name'=>'address'])
                    </label>

                    <label>Телефон
                        @include('includes.inputs.phone', ['value' => optional($department->main_phone)->phone, 'name' => 'main_phone'])
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

                    {{ Form::hidden('id', null, ['id' => 'item-id']) }}
                    {{ Form::hidden('filial_id', $filial_id, ['id' => 'filial-id']) }}

                    @include('includes.control.checkboxes', ['item' => $department])

                </div>
            </div>
        </div>

        {{-- Схема работы --}}
        @can('index', App\Schedule::class)
            <div class="tabs-panel" id="tab-worktime">
                <div class="grid-x grid-padding-x align-center">
                    <div class="small-8 cell">
                        @include('includes.inputs.schedule', ['worktime' => $department->worktime])
                    </div>
                </div>
            </div>
        @endcan

    </div>
</div>

<div class="grid-x align-center">
    <div class="small-6 medium-4 cell text-center">
        {{ Form::submit($submitButtonText, ['class' => 'button modal-button ' . $class]) }}
    </div>
</div>

<script type="application/javascript">
    $.getScript("/js/system/jquery.maskedinput.js");
    $.getScript("/js/system/inputs_mask.js");
</script>



