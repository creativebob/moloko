<?php
use App\Challenge;
use Carbon\Carbon;

    function challenges() {

        $user = Auth::user();
        $user_id = $user->id;
        $answer_challenge = operator_right('challenges', false, 'index');

        $challenges = Challenge::with(
            'author',
            'appointed',
            'finisher',
            'challenges',
            'challenge_type'
        )
        ->where(function($query) use ($user_id){
            $query->where('appointed_id', $user_id)->orWhere('author_id', $user_id);
        })
        ->moderatorLimit($answer_challenge)
        ->companiesLimit($answer_challenge)
        ->authors($answer_challenge)
        ->systemItem($answer_challenge) // Фильтр по системным записям
        ->where('status', null)
        // ->whereDate('deadline_date', '<=', Carbon::now()->format('Y-m-d'))
        ->orderBy('deadline_date', 'asc')
        ->get();

        // $challenges->transform(function ($item, $key) {
        //     return $item->challenges->name . ' Добавка';
        // });


        // dd($challenges);

        // dd($list_challenges);
        // 
        $list_challenges = [];
        
        $list_challenges['for_me'] = $challenges->where('appointed_id', $user_id)->groupBy(function($challenges) {
            return Carbon::parse($challenges->deadline_date)->format('d.m.Y'); // А это то-же поле по нему мы и будем группировать
        });

        $list_challenges['from_me'] = $challenges->where('author_id', $user_id)->where('appointed_id','!=', $user_id)->groupBy(function($challenges) {
            return Carbon::parse($challenges->deadline_date)->format('d.m.Y'); // А это то-же поле по нему мы и будем группировать
        });

        // dd($list_challenges);
        return $list_challenges;

    };
?>