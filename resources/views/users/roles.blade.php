@if (!empty($role_user))

@php
if ($role_user->position == null) {
 $position = (object) [
 	'id' => 'null',
 	'position_name' => 'Спецправо',
 ];
} else {
 $position = $role_user->position;
}
@endphp

<tr class="item" id="access-{{ $role_user->role->id }}-{{ $role_user->department->id }}" data-name="{{ $role_user->role->role_name }}">
	<td>{{ $role_user->role->role_name }}</td>
	<td>{{ $role_user->department->department_name }}</td>
	<td>{{ $position->position_name }}</td>
	<td>Инфа</td>
	<td class="td-delete"><a class="icon-delete sprite" data-open="item-delete-ajax"></a></td>
	{{ Form::hidden('access[]', $role_user->role->id .','. $role_user->department->id .','. $position->id) }}
</tr>

@endif