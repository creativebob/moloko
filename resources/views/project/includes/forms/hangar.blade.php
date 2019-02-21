{{ Form::open(['url' => '/sending', 'data-abide']) }}
<fieldset>
	<div class="grid-x grid-margin-x">
		<div class="small-12 medium-4 large-3 cell clean-pad-left">
			<label>Ширина ангара:
				{{ Form::text('width', null, ['maxlength' => '50', 'required']) }}
			</label>
		</div>

		<div class="small-12 medium-4 large-3 cell clean-pad-left">
			<label>Высота ангара:
				{{ Form::text('height', null, ['maxlength' => '50', 'required']) }}
			</label>
		</div>

		<div class="small-12 medium-4 large-3 cell clean-pad-left">
			<label>Длина ангара:
				{{ Form::text('length', null, ['maxlength' => '50', 'required']) }}
			</label>
		</div>
		<div class="small-12 medium-12 large-3 cell clean-pad-left">
			<label>Ваш телефон:
				{{ Form::text('main_phone', null, ['required', 'class'=>'phone-field', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}']) }}
			</label>
		</div>

		<div class="small-12 medium-12 large-12 cell clean-pad-left">
			{{ Form::hidden('category_id', $category_id) }}
			{{ Form::hidden('form', 'form-hangar') }}
			{{ Form::submit('Узнать стоимость', ['class'=>'button small right']) }}
		</div>
		<div id="result-send"></div>
	</div>
</fieldset>
{{ Form::close() }}

