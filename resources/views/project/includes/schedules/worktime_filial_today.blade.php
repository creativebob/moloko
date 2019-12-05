{{-- Расписание / График работы --}}
@isset($worktimes)
    <span>
        {{ $worktimes[date('N')]['begin'] . ' - ' . $worktimes[date('N')]['end'] }}
    </span>
@endisset