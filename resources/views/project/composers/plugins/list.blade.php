{{-- ============= Подключение плагинов =============== --}}
@foreach($plugins as $plugin)
    {!! $plugin->code !!}
@endforeach
