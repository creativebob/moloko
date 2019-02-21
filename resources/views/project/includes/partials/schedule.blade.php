<table class="time-work">
	<tbody>
		@for ($x = 1; $x <= 7; $x++)
		<tr class="@if(empty($worktimes[$x])) weekend @endif @if($x == date('N')) today @endif">
			<td>@switch ($x)
				@case (1) Понедельник @break
				@case (2) Вторник @break
				@case (3) Среда @break
				@case (4) Четверг @break
				@case (5) Пятница @break
				@case (6) Суббота @break
				@case (7) Воскресенье @break
				@endswitch
			</td>
			<td>{{ isset($worktimes[$x]) ? $worktimes[$x]['worktime_begin'] . ' - ' . $worktimes[$x]['worktime_end'] : 'Выходной' }}</td>
		</tr>
		@endfor
	</tbody>
</table>