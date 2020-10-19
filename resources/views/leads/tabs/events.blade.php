<div class="grid-x grid-padding-x">
    <div class="cell small-12 large-12">

        <lead-events-component
            :lead="{{ $lead }}"
            :stages='@json($stages)'
        ></lead-events-component>

        {{-- Подключаем задачи --}}
        @include('includes.challenges.fieldset', ['item' => $lead])

        {{-- Подключаем комментарии --}}
        @include('includes.notes.fieldset', ['item' => $lead])
    </div>
</div>
