<div class="small-12 medium-6 large-6 cell">
    <legend>Фильтры:</legend>

    <div class="grid-x grid-padding-x">

        <div class="small-12 medium-6 cell">
            <div class="grid-x">

                <div class="cell small-12 checkbox checkboxer">
                    <checkboxer-component
                        name="cities"
                        title="Выберите город"
                        :items='@json($cities)'
                        :checkeds='@json(request()->cities)'
                    ></checkboxer-component>
{{--                            @include('includes.inputs.checkboxer', ['name'=>'city', 'value' => $filter])--}}
                </div>

                <div class="cell small-12 checkbox checkboxer">
                    <checkboxer-component
                        name="stages"
                        title="Выберите этап"
                        :items='@json($stages)'
                        :checkeds='@json(request()->stages)'
                    ></checkboxer-component>
{{--                            @include('includes.inputs.checkboxer', ['name'=>'stage', 'value' => $filter])--}}
                </div>

                <div class="cell small-12">
                    <label>Задачи
                        {!! Form::select('challenges', [true => 'Только с задачами', false => 'Нет активных задач'], request()->challenges, ['placeholder' => 'Все']) !!}
                    </label>
                </div>

                </div>

                <div class="cell small-12">
                    @include('includes.inputs.min_max_date', ['name' => 'period_date', 'title' => 'Период'])
                </div>

            <div class="cell small-12 checkbox checkboxer">
                <checkboxer-component
                    name="managers"
                    title="Менеджер"
                    :items='@json($managers)'
                    :checkeds='@json(request()->managers)'
                ></checkboxer-component>
                {{--                            @include('includes.inputs.checkboxer', ['name'=>'manager', 'value' => $filter])--}}
            </div>

            <div class="cell small-12">
                <goods-lister-component
                    :goods='@json($goods)'
                    :items='@json(request()->goods)'
                ></goods-lister-component>
            </div>

        </div>

        <div class="small-12 medium-6 cell">
            <div class="grid-x">

                <div class="cell small-12 checkbox checkboxer">
                    <checkboxer-component
                        name="lead_methods"
                        title="Способ обращения"
                        :items='@json($leadMethods)'
                        :checkeds='@json(request()->lead_methods)'
                    ></checkboxer-component>
{{--                        @include('includes.inputs.checkboxer', ['name'=>'lead_method', 'value' => $filter])--}}
                </div>

                <div class="cell small-12 checkbox checkboxer">
                    <checkboxer-component
                        name="lead_types"
                        title="Тип обращения"
                        :items='@json($leadTypes)'
                        :checkeds='@json(request()->lead_types)'
                    ></checkboxer-component>
{{--                        @include('includes.inputs.checkboxer', ['name'=>'lead_type', 'value' => $filter])--}}
                </div>

                <div class="cell small-12">
                    <label>Юридический статус
                        {!! Form::select('status', ['fiz' => 'Физическое лицо', 'ur' => 'Юридическое лицо'], request()->status, ['placeholder' => 'Все']) !!}
                    </label>
                </div>

                <div class="cell small-12">
                    @include('includes.inputs.min_max_date', ['name' => 'shipment_date', 'title' => 'Дата отгрузки'])
                </div>

                <div class="cell small-12 checkbox checkboxer">
                    <checkboxer-component
                        name="sources"
                        title="Источник трафика"
                        :items='@json($sources)'
                        :checkeds='@json(request()->sources)'
                    ></checkboxer-component>
                </div>
        </div>
    </div>

</div>
</div>

<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
    <legend>Мои списки:</legend>
    <div id="booklists">
        @include('includes.inputs.booklister', ['name'=>'booklist', 'value' => $filter])
    </div>
</div>
