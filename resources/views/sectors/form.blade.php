{{-- <label>Название сектора
    @include('includes.inputs.name', ['required' => true, 'check' => true])
    <div class="sprite-input-right find-status"></div>
    <div class="item-error">Такой сектор уже существует!</div>
</label> --}}

<label>Тег
    @include('includes.inputs.text-en', ['name' => 'tag', 'check' => true])
    <div class="sprite-input-right find-status"></div>
    <div class="item-error">Такой тег уже существует!</div>
</label>
