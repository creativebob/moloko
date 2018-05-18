@if (!empty($role_user))

@php
if ($role_user->position == null) {
 $position = (object) [
 	'id' => 'null',
 	'name' => 'Спецправо',
 ];
} else {
 $position = $role_user->position;
}
@endphp

<tr class="item" id="access-{{ $role_user->role->id }}-{{ $role_user->department->id }}" data-name="{{ $role_user->role->name }}">
	<td>{{ $role_user->role->name }}</td>
	<td>{{ $role_user->department->name }}</td>
	<td>{{ $position->name }}</td>
	<td>Инфа</td>
	<td class="td-delete"><a class="icon-delete sprite" data-open="item-delete-ajax"></a></td>
	{{ Form::hidden('access[]', $role_user->role->id .','. $role_user->department->id .','. $position->id) }}
</tr>

@endif