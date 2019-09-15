<?php
use App\Challenge;
use App\Lead;
use Carbon\Carbon;

    function challenges() {

        $user = Auth::user();
        $user_id = $user->id;
        $answer_challenge = operator_right('challenges', false, 'index');

        $list_challenges = Challenge::with(
            'author',
            'appointed',
            'finisher',
            'subject',
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
        ->orderBy('priority_id', 'desc')
        ->orderBy('deadline_date', 'asc')
        ->get()
        ->groupBy([
            function($challenges) use ($user_id) {

                if($challenges->appointed_id == $user_id){
                    return 'for_me';
                }

                if(($challenges->author_id == $user_id) && ($challenges->appointed_id != $user_id)){
                    return 'from_me';
                }

            },
            function($challenges) {

                if($challenges->deadline_date < Carbon::today()){
                    return 'last'; // А это то-же поле по нему мы и будем группировать
                }

                if($challenges->deadline_date->format('d.m.Y') == Carbon::today()->format('d.m.Y')){
                    return 'today'; // А это то-же поле по нему мы и будем группировать
                }

                if($challenges->deadline_date > Carbon::today()){
                    return 'future'; // А это то-же поле по нему мы и будем группировать
                }

            },
            function($challenges) {
                return Carbon::parse($challenges->deadline_date)->format('d.m.Y');
            }
        ]);


        // dd($list_challenges);
        return $list_challenges;

    };
?>