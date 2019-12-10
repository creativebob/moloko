<ul>
    @foreach ($roles as $role)
        @if ($role->id != 1 || ($role->id == 1) && (auth()->user()->god))
            <li>
                <div class="small-12 cell checkbox">
                    {{ Form::checkbox('roles[]', $role->id, null, ['id'=>'checkbox-role-' . $role->id, 'class'=>'access-checkbox']) }}
                    <label for="checkbox-role-{{ $role->id }}"><span>{{ $role->name }}</span></label>
                    @php
                        $allow = count($role->rights->where('directive', 'allow'));
                        $deny = count($role->rights->where('directive', 'deny'));
                    @endphp
                    (<span class="allow">{{ $allow }}</span> / <span class="deny">{{ $deny }}</span>)
                </div>
            </li>
        @endif

    @endforeach
</ul>
