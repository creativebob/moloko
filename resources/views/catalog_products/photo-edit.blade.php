<div class="text-center">
@if (isset($photo->name))

<img src="/storage/{{ $photo->company_id }}/media/albums/{{ $photo->album_id }}/img/medium/{{ $photo->name }}" alt="{{ $photo->name }}">

@else
<span>Выберите фото для редактирования</span>
@endif
</div>

<label class="tabs-margin-top">Заголовок фото
	@include('includes.inputs.name', ['name'=>'title', 'value'=>$photo->title, 'required' => true])
</label>
<label>Описание фото
	@include('includes.inputs.textarea', ['name'=>'description', 'value'=>$photo->description])
</label>

{{-- Чекбокс отображения на сайте --}}
@can ('publisher', $photo)
<div class="small-12 cell checkbox">
	{{ Form::checkbox('display', 1, $photo->display, ['id' => 'photo-display']) }}
	<label for="photo-display"><span>Отображать на сайте</span></label>
</div>
@endcan

{{-- Чекбокс модерации --}}
@can ('moderator', $photo)
@if ($photo->moderation == 1)
<div class="small-12 cell checkbox">
	{{ Form::checkbox('moderation', 1, $photo->moderation, ['id'=>'photo-moderation-checkbox']) }}
<label for="photo-moderation-checkbox"><span>Временная запись.</span></label>
</div>
@endif
@endcan

{{-- Чекбокс системной записи --}}
@can ('god', $photo)
<div class="small-12 cell checkbox">
{{ Form::checkbox('system_item', 1, $photo->system_item, ['id'=>'photo-system-item-checkbox']) }}
<label for="photo-system-item-checkbox"><span>Сделать запись системной.</span></label>
</div>
@endcan

{{ Form::hidden('id', $photo->id) }}
{{ Form::hidden('entity', 'products') }}

{{ Form::submit('Редактировать фотографию', ['class'=>'button tabs-margin-top']) }}