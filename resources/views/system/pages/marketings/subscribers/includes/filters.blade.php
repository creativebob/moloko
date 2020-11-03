<div class="cell small-12 medium-6 large-6">
	<legend>Фильтры:</legend>

	<div class="grid-x grid-margin-x">



 		<div class="cell small-12 medium-6">
            <label>Статус
			    {!! Form::select('is_active', [1 => 'Действующий', 0 => 'Не действующий'], request()->is_active, ['placeholder' => 'Все']) !!}
            </label>
 		</div>

        <div class="cell small-12 medium-6">
            <label>Отправка
                {!! Form::select('deny', [1 => 'Разрешена', 0 => 'Запрещена'], request()->deny, ['placeholder' => 'Все']) !!}
            </label>
        </div>

        <div class="cell small-12 medium-6">
            <label>Клиент
                {!! Form::select('client', [1 => 'Идентифицирован', 0 => 'Не идентифицирован'], request()->client, ['placeholder' => 'Все']) !!}
            </label>
        </div>

        <div class="cell small-12 medium-6">
            <label>Ошибки
                {!! Form::select('is_valid', [0 => 'Есть', 1 => 'Нет'], request()->is_valid, ['placeholder' => 'Все']) !!}
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
