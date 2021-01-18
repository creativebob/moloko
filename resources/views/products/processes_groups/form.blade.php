<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="grid-x grid-padding-x">

                <div class="medium-6 cell">
                    <label>Название группы товара
                        @include('includes.inputs.name', ['required' => true])
                    </label>
                </div>
                <div class="medium-6 cell">
                    <label>Описание
                        @include('includes.inputs.varchar', ['name' => 'description'])
                    </label>
                </div>

                <div class="small-12 medium-6 cell">
                    @include('includes.selects.units_categories', ['default' => $processesGroup->units_category_id, 'type'=>'process'])
                </div>

                {{--                <div class="small-12 medium-6 cell">--}}
                {{--                    @include('includes.selects.units', ['default' => isset($processesGroup->unit_id) ? $processesGroup->unit_id : 26, 'units_category_id' => isset($processesGroup->unit_id) ? $processesGroup->unit->category_id : 6])--}}
                {{--                </div>--}}

                <div class="small-12 cell">
                    <fieldset>
                        <legend>Список процессов в группе:</legend>
                        <ul>
                            @foreach($processesGroup->processes as $process)

                                @foreach($process->in_services as $item)
                                    <li>
                                        Услуга: <a
                                            href="{{ route('services.edit', $item->id) }}">{{ $item->process->name }}</a>
                                    </li>
                                @endforeach
                                @foreach($process->in_workflows as $item)
                                    <li>
                                        Рабочий процесс: <a
                                            href="{{ route('workflows.edit', $item->id) }}">{{ $item->process->name }}</a>
                                    </li>
                                @endforeach

                            @endforeach
                        </ul>
                    </fieldset>
                </div>

            </div>

        </div>
    </div>

    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
    </div>

    @if ($processesGroup->processes->count() == 0)
        <div class="small-12 cell checkbox set-status">
            {{-- <input type="checkbox" name="set_status" id="set-status" value="1"> --}}
            {{ Form::checkbox('set_status', 1, null, ['id' => 'set-status']) }}
            <label for="set-status"><span>Набор</span></label>
        </div>
    @endif

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $processesGroup])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class' => 'button']) }}
    </div>
</div>

@push('scripts')
    @include('includes.scripts.inputs-mask')

    <script type="application/javascript">
        // При смене категории единиц измерения меняем список единиц измерения
        $(document).on('change', '#select-units_categories', function () {
            $.post('/admin/get_units_list', {units_category_id: $(this).val()}, function (html) {
                $('#select-units').html(html);
            });
        });
    </script>

@endpush

