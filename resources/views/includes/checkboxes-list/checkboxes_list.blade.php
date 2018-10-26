<li>
    <div class="small-12 cell checkbox">
        {{ Form::checkbox($mnoj. '[]', $item->id, null, ['id'=>'widget-'.$item->id, 'class'=>'access-checkbox']) }}
        <label for="{{ $name }}-{{ $item->id }}"><span>{{ $item->name }}</span></label>
    </div>
</li>
