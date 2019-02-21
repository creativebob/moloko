{{ Form::open(['url' => '/sending', 'data-abide', 'class' => 'measure cls']) }}
<fieldset>
	<div class="grid-x">
		<div class="small-6 cell clean-pad-left">
			<label>Дата замера:
				@php
				$today = date('Y-m-d');
				$date = explode("-", $today);
				$today= $date[2].".".$date[1].".".$date[0];
				@endphp
				{{ Form::text('date', $today, ['id' => 'date', 'class' => 'datezamer']) }}
			</label>
		</div>
		<div class="small-6 cell clean-pad-left">
			<label>Удобное время:
				{{ Form::text('time', null, ['id' => 'tz-begin', 'class' => 'time-field', 'pattern' => '([0-1][0-9]|[2][0-3]):[0-5][0-9]', 'placeholder' => '10:00', 'maxlength' => '5', 'onkeyup' => 'proTime(this);']) }}
			</label>
		</div>
	</div>
	<div class="grid-x">
		<div class="small-12 medium-12 large-12 cell clean-pad-left">
			<label>Адрес где будет производиться замер:
				{{ Form::text('address', null, ['maxlength'=>'50', 'required']) }}
			</label>
		</div>
		<div class="small-12 medium-12 large-12 cell clean-pad-left">
			<label>Ваше имя:
				{{ Form::text('name', null, ['maxlength'=>'24', 'required']) }}
			</label>
		</div>
		<div class="small-12 medium-12 large-12 cell clean-pad-left">
			<label>Телефон:
				{{ Form::text('main_phone', null, ['required', 'class'=>'phone-field', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}']) }}
			</label>
		</div>
	</div>
</fieldset>
<fieldset>
	<div class="small-12 cell clean-pad-left">
		{{ Form::hidden('category_id', $category_id) }}
		{{ Form::hidden('form', 'form-measurement') }}
		{{ Form::submit('Отправить заявку на замер!', ['class'=>'button small right']) }}
	</div>
</fieldset>
{{ Form::close() }}

