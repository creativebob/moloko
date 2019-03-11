{{ Form::open(['url' => '/sending', 'data-abide']) }}
	<fieldset>
		<div class="grid-x grid-margin-x">
			<div class="small-12 medium-4 large-3 cell">
				<label>Ширина проема:
					{{ Form::text('width', null, ['id' => 'width-gate', 'maxlength'=>'50', 'required']) }}
				</label>
			</div>

			<div class="small-12 medium-4 large-3 cell">
				<label>Высота проема:
					{{ Form::text('height', null, ['id' => 'height-gate', 'maxlength'=>'50', 'required']) }}
				</label>
			</div>

			<div class="small-12 medium-4 large-3 cell">
				<label>Тип ворот:
					{{ Form::select('type', ['Откатные' => 'Откатные', 'Распашные' => 'Распашные'], 'Откатные', ['id' => 'type-gate']) }}
				</label>
			</div>
			<div class="small-12 medium-4 large-3 cell">
				<label>Ваш телефон:
					{{ Form::text('main_phone', null, ['required', 'class'=>'phone-field', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'id' => 'user_phone']) }}
				</label>
			</div>
		</div>
		<div class="grid-x">
			<div class="small-12 medium-4 large-3 cell">
				{{ Form::hidden('category_id', $category_id) }}
				{{ Form::hidden('form', 'form-street_gates') }}
				{{ Form::submit('Узнать стоимость', ['class'=>'button small right']) }}
			</div>
			<div id="result-send"></div>
		</div>
	</fieldset>
{{ Form::close() }}

