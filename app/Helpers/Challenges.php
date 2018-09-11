<?php
use App\Challenge;
use Carbon\Carbon;

    function challenges() {

        $user = Auth::user();
        $answer_challenge = operator_right('challenges', false, 'index');

        $list_challenges = Challenge::with(
            'author',
            'appointed',
            'finisher',
            'challenges',
            'challenge_type'
        )
        ->where('appointed_id', $user->id)
        ->moderatorLimit($answer_challenge)
        ->companiesLimit($answer_challenge)
        ->authors($answer_challenge)
        ->systemItem($answer_challenge) // Фильтр по системным записям
        ->where('status', null)
        ->whereDate('deadline_date', '<=', Carbon::now()->format('Y-m-d'))
        ->orderBy('deadline_date', 'asc')
        ->orderBy('moderation', 'desc')
        ->get();

        $challenges = [];

        // Задачи на сегодня
        $challenges['today'] = $list_challenges
        ->whereDate('deadline_date', '=', Carbon::now()->format('Y-m-d'))
        ->groupBy(function($challenges) {
            return Carbon::parse($challenges->deadline_date)
            ->format('d.m.Y'); // А это то-же поле по нему мы и будем группировать
        });

        // Задачи просроченные
        $challenges['lost'] = $list_challenges
        ->where('deadline_date', '<', Carbon::now())
        ->groupBy(function($challenges) {
            return Carbon::parse($challenges->deadline_date)
            ->format('d.m.Y'); // А это то-же поле по нему мы и будем группировать
        });

        // Задачи на завтра и будущие задачи
        $challenges['tomorrow'] = $list_challenges
        ->whereDate('deadline_date', '>', Carbon::now()->format('Y-m-d'))
        ->groupBy(function($challenges) {
            return Carbon::parse($challenges->deadline_date)
            ->format('d.m.Y'); // А это то-же поле по нему мы и будем группировать
        });

        return $challenges;
    };
?>