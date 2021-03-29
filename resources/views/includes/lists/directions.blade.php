<ul>
    @foreach ($directions as $direction)
        @isset($direction)
            <li>
                <div class="small-12 cell checkbox">
                    {{ Form::checkbox('directions[]', $direction->id, null, ['id'=>'direction-'.$direction->id, 'class'=>'access-checkbox']) }}
                    <label for="direction-{{ $direction->id }}"><span>{{ $direction->category->name }}</span></label>
                </div>
            </li>
        @endisset
    @endforeach
</ul>
