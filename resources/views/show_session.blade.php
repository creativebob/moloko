@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

                <div class="panel-body">

                        <div class="alert alert-success">
                            {{-- dd(session('conditions')); --}}          
                          @php

                            

                            dd(session('access'));
                            
                            $session_god = session('god'); 
                            $session_access = session('access');
                            $user_filial_id = $session_access['user_info']['filial_id'];
                            $rights_user_filial = collect($session_access['all_rights'])->keys()->implode('\n');


                          @endphp

                            {{  $user_filial_id }} \r {{!!  $rights_user_filial  !!}}






                        </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
