<div class="grid-x">
    <div class="cell small-12 medium-7 large-5">
        <div class="grid-x grid-margin-x">
            <div class="cell small-12">
                <fieldset class="fieldset-access">
                    <legend>Оборудование</legend>
                    <ul>
                        @foreach ($workplace->outlet->tools as $tool)
                            <li class="checkbox">
                                {!! Form::checkbox('tools[]', $tool->id, null, ['id' => 'checkbox-tools-'.$tool->id]) !!}
                                <label for="checkbox-tools-{{ $tool->id }}">
                                    <span>{{ $tool->article->name }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </fieldset>
            </div>
        </div>
    </div>

    <div class="cell small-12 medium-5 large-7">
    </div>
</div>
