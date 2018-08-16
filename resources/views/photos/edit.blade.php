@extends('layouts.app')

@section('title', 'Редактировать фотографию')

@section('breadcrumbs', Breadcrumbs::render('section-edit', $parent_page_info, $album, $page_info, $photo))

@section('title-content')
<div class="top-bar head-content">
  <div class="top-bar-left">
   <h2 class="header-content">РЕДАКТИРОВАние фотографии "{{ $photo->name }}"</h2>
 </div>
 <div class="top-bar-right">
 </div>
</div>
@endsection

@section('content')
{{ Form::model($photo, ['url'=>'/admin/albums/'.$album->alias.'/photos/'.$photo->id,'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}

@if ($errors->any())
<div class="grid-x">
  <div class="small-12 medium-6 large-5 cell tabs-margin-top">
    <div class="alert callout" data-closable>
      <h5>Неправильный формат данных:</h5>
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
</div>
@endif
<div class="grid-x grid-padding-x inputs tabs-margin-top">
  <div class="small-12 medium-6 cell">
    <div class="grid-x grid-padding-x">
      <div class="small-12 medium-12 cell">
        <label>Заголовок фото
          @include('includes.inputs.name', ['name'=>'title', 'value'=>$photo->title, 'required'=>'required'])
        </label>
        <label>Описание
          @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$photo->description, 'required'=>''])
        </label>
      </div>
      <div class="small-12 medium-6 cell">
        <label>Ссылка на внешний адрес (В случае необходимости)
          @include('includes.inputs.link', ['name'=>'link', 'value'=>$photo->link, 'required'=>''])
        </label>
      </div>

      <div class="small-12 medium-6 cell">
        <label for="head">Цвет (В случае необходимости)
          <input type="color" id="head" name="color" value="#e66465" />
        </label>
      </div>
    </div>
  </div>

  <div class="small-12 medium-6 cell text-center checkbox">
    <img id="photo" src="{{ isset($photo->name) ? '/storage/'.$photo->company->id.'/media/albums/'.$album->id.'/img/original/'.$photo->name : 'lol' }}">
    {{ Form::checkbox('avatar')}}
  </div>

  <div class="small-12 small-text-center cell checkbox">
    {{ Form::checkbox('avatar', 1, null, ['id'=>'avatar-checkbox']) }}
    <label for="avatar-checkbox"><span>Сделать аватаром альбома.</span></label>
  </div>

  {{-- Чекбоксы управления --}}
  @include('includes.control.checkboxes', ['item' => $photo])  

  <div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
    {{ Form::submit('Редактировать фото', ['class'=>'button']) }}
  </div>
</div>

{{ Form::close() }}
@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@endsection


