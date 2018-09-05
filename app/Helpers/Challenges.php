<?php
use App\Challenge;
use Carbon\Carbon;

    function challenges($request) {

        $user = $request->user();
        $answer_challenge = operator_right('challenges', false, 'index');

        $challenges = Challenge::with(
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
        ->whereDay('deadline_date', Carbon::now()->format('d'))
        ->orderBy('deadline_date', 'asc')
        ->orderBy('moderation', 'desc')
        ->get()
        ->groupBy(function($challenges) {
            return Carbon::parse($challenges->deadline_date)->format('d.m.Y'); // А это то-же поле по нему мы и будем группировать
        });

        return $challenges;
    };
?>