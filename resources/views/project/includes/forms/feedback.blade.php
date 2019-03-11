{{ Form::open(['url' => '/sending', 'data-abide']) }}
<fieldset>
	<div class="small-12 medium-12 large-12 cell clean-pad-left">
		<label>Телефон:
			{{ Form::text('main_phone', null, ['required', 'class'=>'phone-field', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}']) }}
		</label>
	</div>
	<div class="small-12 medium-12 large-12 cell clean-pad-left">
		<label>Сообщение:
			<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
		</label>
	</div>
</fieldset>
<fieldset>
	{{ Form::hidden('remark', $remark) }}
	{{ Form::hidden('form', 'form-feedback') }}
	{{ Form::submit('Отправить', ['class'=>'button small right']) }}
</fieldset>
{{ Form::close() }}
