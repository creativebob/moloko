<div class="cell small-12 medium-6 large-6">
	<legend>Фильтры:</legend>

	<div class="grid-x grid-margin-x">



 		<div class="cell small-12 medium-6">
            <label>Статус
			    {!! Form::select('is_active', [true => 'Действующий', false => 'Не действующий'], request()->is_active, ['placeholder' => 'Все']) !!}
            </label>
 		</div>

        <div class="cell small-12 medium-6">
            <label>Отправка
                {!! Form::select('deny', [true => 'Разрешена', false => 'Запрещена'], request()->deny, ['placeholder' => 'Все']) !!}
            </label>
        </div>

        <div class="cell small-12 medium-6">
            <label>Клиент
                {!! Form::select('client', [true => 'Идентифицирован', false => 'Не идентифицирован'], request()->client, ['placeholder' => 'Все']) !!}
            </label>
        </div>

        <div class="cell small-12 medium-6">
            <label>Ошибки
                {!! Form::select('is_valid', [false => 'Есть', true => 'Нет'], request()->is_valid, ['placeholder' => 'Все']) !!}
            </label>
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'dispatches_count', 'title' => 'Кол-во писем'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max_date', ['name' => 'created_at', 'title' => 'Дата создания'])
        </div>

    </div>
</div>
<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
	<legend>Мои списки:</legend>
	<div id="booklists">
{{--		@include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])--}}
	</div>
</div>
