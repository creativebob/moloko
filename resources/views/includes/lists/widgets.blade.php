<ul>
    @foreach ($widgets as $widget)
        <li>
            <div class="small-12 cell checkbox">
                {{ Form::checkbox('widgets[]', $widget->id, null, ['id'=>'checkbox-widget-' . $widget->id, 'class'=>'access-checkbox']) }}
                <label for="checkbox-widget-{{ $widget->id }}"><span>{{ $widget->name }}</span></label>
            </div>
        </li>
    @endforeach
</ul>
