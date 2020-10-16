<ul>
    @foreach ($notifications as $notification)

        @php
            $model = $notification->trigger->entity->model;
        @endphp
        @can('index', $model)
            <li>
                <div class="small-12 cell checkbox">
                    {{ Form::checkbox('notifications[]', $notification->id, null, ['id'=>'notification-'.$notification->id, 'class'=>'access-checkbox']) }}
                    <label for="notification-{{ $notification->id }}"><span>{{ $notification->name }}</span></label>
                </div>
            </li>
        @endcan
    @endforeach
</ul>
