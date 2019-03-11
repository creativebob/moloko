{{ Form::open(['url' => '/sending', 'data-abide']) }}
<fieldset>
	<label>Ваше имя:
		{{ Form::text('name', null, ['maxlength'=>'30', 'required']) }}
	</label>
	<label>Телефон:
		{{ Form::text('main_phone', null, ['required', 'class'=>'phone-field', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}']) }}
	</label>
</fieldset>
<fieldset>
	{{ Form::hidden('remark', $remark) }}
	{{ Form::hidden('category_id', $category_id) }}
	{{ Form::hidden('form', 'form-call') }}
	{{ Form::submit('Отправить', ['class'=>'button small right']) }}
</fieldset>
{{ Form::close() }}

