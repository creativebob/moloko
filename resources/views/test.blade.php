@extends('layouts.app')

@section('content')
<div class="grid-x">
    <div class="small-4 cell">


        <citysearch-component :city="{{ $city }}"></citysearch-component>
    </div>

</div>

@endsection
