{{-- <label>Название сектора
    @include('includes.inputs.name', ['required' => true, 'check' => true])
    <div class="sprite-input-right find-status"></div>
    <div class="item-error">Такой сектор уже существует!</div>
</label> --}}

<label>Алиас
    @include('includes.inputs.text-en', ['name' => 'alias', 'check' => true])
    <div class="sprite-input-right find-status"></div>
    <div class="item-error">Такой алиас уже существует!</div>
</label>
