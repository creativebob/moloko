@isset($worktimes)
<table class="worktime">
	<caption>Время работы</caption>
	<tbody>
	@for ($x = 1; $x <= 7; $x++)
		<tr @if($x == date('N')) class="today" @endif>
			<td>{{ $days[$x] }}</td>
			<td>{{ isset($worktimes[$x]) ? $worktimes[$x]['worktime_begin'] . ' - ' . $worktimes[$x]['worktime_end'] : 'Выходной' }}</td>
		</tr>
	@endfor
	</tbody>
</table>
@endisset