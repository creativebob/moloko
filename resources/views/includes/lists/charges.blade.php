<ul>
    @foreach ($charges as $charge)
        <li>
            <div class="small-12 cell checkbox">
                {{ Form::checkbox('charges[]', $charge->id, null, ['id'=>'checkbox-charge-' . $charge->id, 'class'=>'access-checkbox']) }}
                <label for="checkbox-charge-{{ $charge->id }}"><span>{{ $charge->name }}</span></label>
            </div>
        </li>
    @endforeach
</ul>
