<div class="cell small-12 medium-6 large-6">
	<legend>Фильтры:</legend>

	<div class="grid-x grid-margin-x">

        <div class="cell small-12 medium-6 checkbox checkboxer">
            <checkboxer-component
                name="cities"
                title="Город"
                :items='@json($cities)'
                :checkeds='@json(request()->cities)'
            ></checkboxer-component>
        </div>
        
        <div class="cell small-12 medium-6 checkbox checkboxer">
            <checkboxer-component
                name="sources"
                title="Источник"
                :items='@json($sources)'
                :checkeds='@json(request()->sources)'
            ></checkboxer-component>
        </div>

        <div class="cell small-12 medium-6">
            <label>Статус по списанию
                {!! Form::select('dismissed', [false => 'Действующие', true => 'Списанные'], request()->dismissed, ['placeholder' => 'Все']) !!}
            </label>
        </div>
        <div class="cell small-12 medium-6">
            <label>Статус по продаже
                {!! Form::select('saled', [false => 'Не проданные', true => 'Проданные'], request()->saled, ['placeholder' => 'Все']) !!}
            </label>
        </div>

        {{-- <div class="cell small-12 medium-6">
            <label>Пол
                {!! Form::select('gender', [false => 'Женский', true => 'Мужской'], request()->gender, ['placeholder' => 'Все']) !!}
            </label>
        </div> --}}

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'total', 'title' => 'Сумма чека, руб.'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'margin_currency', 'title' => 'Размер маржи, руб.'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'discount_currency', 'title' => 'Размер скидки, руб.'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'total_points', 'title' => 'Внутренняя валюта, кол-во'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max_date', ['name' => 'registered_date', 'title' => 'Дата оформления заказа'])
        </div>

    </div>
</div>
<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
	<legend>Мои списки:</legend>
	<div id="booklists">
{{--		@include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])--}}
	</div>
</div>
