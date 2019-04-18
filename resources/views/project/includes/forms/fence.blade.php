{{ Form::open(['url' => '/sending', 'data-abide', 'class' => 'measure cls']) }}
<fieldset>
	<div class="grid-x grid-margin-x">
		<div class="small-12 medium-4 large-3 cell clean-pad-left">
			<label>Длина забора:
				{{ Form::text('width', null, ['id' => 'width-fence', 'maxlength' => '50', 'required']) }}
			</label>
		</div>
		<div class="small-12 medium-4 large-3 cell clean-pad-left">
			<label>Высота забора:
				{{ Form::text('height', null, ['id' => 'height-fence', 'maxlength' => '50', 'required']) }}
			</label>
		</div>
		<div class="small-12 medium-4 large-3 cell clean-pad-left">
			<label>Тип забора:
				{{ Form::select('type', ['Эко' => 'Эко', 'Комфорт' => 'Комфорт', 'Премиум' => 'Премиум', '3D' => '3D'], 'Эко') }}
			</label>
		</div>
		<div class="small-12 medium-4 large-3 cell clean-pad-left">
			<label>Тип фундамента:
				{{ Form::select('foundation', ['Ямочный' => 'Ямочный', 'Ленточный' => 'Ленточный', 'Винтовые сваи' => 'Винтовые сваи'], 'Ямочный') }}
			</label>
		</div>
		<div class="small-12 medium-12 large-6 cell clean-pad-left">
			<label>Ваше имя:
				{{ Form::text('name', null, ['maxlength'=>'30', 'required']) }}
			</label>
		</div>
		<div class="small-12 medium-12 large-6 cell clean-pad-left">
			<label>Ваш телефон:
				{{ Form::text('main_phone', null, ['required', 'class'=>'phone-field', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}']) }}
			</label>
		</div>
		<div class="small-12 medium-12 large-12 cell clean-pad-left">
			{{ Form::hidden('category_id', $category_id) }}
			{{ Form::hidden('form', 'form-fence') }}
			{{ Form::submit('Узнать стоимость', ['class'=>'button small right']) }}
		</div>
		<div id="result-send"></div>
	</div>
</fieldset>
{{ Form::close() }}

