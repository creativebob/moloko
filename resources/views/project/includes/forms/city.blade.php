{{ Form::open(['url' => '/sending', 'data-abide', 'class' => 'measure cls']) }}

	<fieldset>
		<div class="grid-x grid-margin-x">
			<div class="small-12 medium-4 large-3 cell clean-pad-left">
				<label>Ваш город:
					{{ Form::text('city', null, ['maxlength'=>'30', 'required']) }}
				</label>
			</div>
			<div class="small-12 medium-4 large-3 cell clean-pad-left">
				<label>Ваше имя:
					{{ Form::text('name', null, ['maxlength'=>'30', 'required']) }}
				</label>
			</div>
			<div class="small-12 medium-4 large-3 cell clean-pad-left">
				<label>Телефон:
					{{ Form::text('main_phone', null, ['required', 'class'=>'phone-field', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}']) }}
				</label>
			</div>
			<div class="small-12 medium-12 large-3 cell clean-pad-left">
				<label>
					{{ Form::hidden('form', 'form-city') }}
					{{ Form::submit('Отправить запрос', ['class'=>'button small right extra-pad']) }}
				</label>
			</div>
		</div>
	</fieldset>
{{ Form::close() }}

