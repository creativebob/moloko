@extends('vkusnyashka.layouts.app')

@section('title')
<title>Ошибка | Воротная компания "Марс"</title>
<meta name="description" content="Ошибка">
@endsection

@section('content')
<div class="wrap-main grid-x">
	
	<main class="cell small-12 medium-9 large-9 main-cont">

      <h2>{{ $error_message }}</h2>

  </main>
	<h2></h2>
</div>
@endsection