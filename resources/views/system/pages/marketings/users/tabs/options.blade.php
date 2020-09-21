<div class="grid-x">
    <div class="cell small-12 medium-6 large-4">
        <div class="grid-x grid-padding-x">
            {{-- Предлагаем добавить компанию в клиенты, если, конечно, создаем ее не из под страницы создания клиентов --}}
            @empty($client)
                {!! Form::hidden('is_client', 0) !!}
                @can('index', App\Client::class)
                    <div class="cell small-12 checkbox">
                        {{ Form::checkbox('is_client', 1, isset($company->client), ['id' => 'checkbox-is_client']) }}
                        <label for="checkbox-is_client"><span>Клиент</span></label>
                    </div>
                @endcan
            @endempty

            {{-- Чекбоксы управления --}}
            @include('includes.control.checkboxes', ['item' => $user])
        </div>
    </div>
</div>
