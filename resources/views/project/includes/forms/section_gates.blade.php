{{ Form::open(['url' => '/sending', 'data-abide']) }}
<fieldset>
	<!-- 	<legend>Укажите основные параметры гаража:</legend>	 -->
	<div class="grid-x grid-margin-x">
		<div class="small-6 medium-6 large-6 cell clean-pad-left">
			<label>Ширина проема, мм:
				{{ Form::text('width', null, ['id' => 'width-gate', 'placeholder' => 'W', 'onkeyup' => 'proCount(this, 0, 10000)', 'onFocus' => 'proFocus(1)', 'required']) }}
			</label>
		</div>
		<div class="small-6 medium-6 large-6 cell clean-pad-left">
			<label>Высота проема, мм:
				{{ Form::text('height', null, ['id' => 'height-gate', 'placeholder' => 'H', 'onkeyup' => 'proCount(this, 0, 6000)', 'onFocus' => 'proFocus(2)', 'required']) }}
			</label>
		</div>
	</div>
	<div class="grid-x grid-margin-x">
		<div class="small-6 medium-6 large-6 cell clean-pad-left">
			<label>Длина левого пристенка, мм:
				{{ Form::text('left_wall', null, ['id' => 'lprist-gate', 'placeholder' => 'w1', 'onkeyup' => 'proCount(this, 0, 10000)', 'onFocus' => 'proFocus(3)', 'required']) }}
			</label>
		</div>

		<div class="small-6 medium-6 large-6 cell clean-pad-left">
			<label>Длина правого пристенка, мм:
				{{ Form::text('right_wall', null, ['id' => 'rprist-gate', 'placeholder' => 'w2', 'onkeyup' => 'proCount(this, 0, 10000)', 'onFocus' => 'proFocus(4)', 'required']) }}
			</label>
		</div>
	</div>
	<div class="grid-x grid-margin-x">
		<div class="small-6 medium-6 large-6 cell clean-pad-left">
			<label>Длина притолоки, мм:
				{{ Form::text('lintel', null, ['id' => 'pritolok-gate', 'placeholder' => 'h1', 'onkeyup' => 'proCount(this, 0, 5000)', 'onFocus' => 'proFocus(5)', 'required']) }}
			</label>
		</div>
		<div class="small-6 medium-6 large-6 cell clean-pad-left">
			<label>Длина гаража, мм:
				{{ Form::text('length', null, ['id' => 'dlina-gate', 'placeholder' => 'l', 'onkeyup' => 'proCount(this, 0, 40000)', 'onFocus' => 'proFocus(6)', 'required']) }}
			</label>
		</div>
	</div>
</fieldset>
<fieldset>
	<div class="grid-x grid-margin-x">
		<div class="small-6 cell wrap-row mycheckbox clean-pad-left">
			<label>Опции:
				{{ Form::select('option', ['Нет' => 'Нет', 'Автоматика' => 'Автоматика', 'Замок' => 'Замок'], 'Нет') }}
			</label>
		</div>
		<div class="small-6 cell wrap-row mycheckbox clean-pad-right">
			<label>Калитка:
				{{ Form::select('gate', ['Нет' => 'Нет', 'Есть' => 'Есть'], 'Нет') }}
			</label>
		</div>
	</div>
</fieldset>
<fieldset>
	<legend>Контакты:</legend>
	<div class="grid-x grid-margin-x">
		<div class="small-12 medium-6 cell clean-pad-left">
			<label>Ваше имя:
				{{ Form::text('name', null, ['maxlength'=>'30', 'required']) }}
			</label>
		</div>
		<div class="small-12 medium-6 cell clean-pad-left">
			<label>Телефон:
				{{ Form::text('main_phone', null, ['required', 'class'=>'phone-field', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}']) }}
			</label>
		</div>
	</div>
</fieldset>
<fieldset>
	<div class="small-12 cell clean-pad-left">
		{{ Form::hidden('category_id', $category_id) }}
		{{ Form::hidden('form', 'form-section_gates') }}
		{{ Form::submit('Узнать стоимость', ['class'=>'button small right']) }}
	</div>
</fieldset>
{{ Form::close() }}

