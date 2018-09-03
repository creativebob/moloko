<?php

namespace App\Http\Controllers;

use App\OldLead;
use App\Lead;
use App\Note;

use App\Location;

use Illuminate\Http\Request;

class ParserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        OldLead::with(['comments.user', 'claims', 'task', 'stage', 'user', 'city', 'service', 'challenges' => function ($query) {
            $query->with('author', 'appointed', 'finisher', 'stage', 'task');
        }])->chunk(50, function ($leads) {
            foreach ($leads as $old_lead) {

                // Пишем локацию
                $location = new Location;
                $location->country_id = 1;
                $location->address = $old_lead->address_company;
                $location->city_id = $old_lead->city->new_city_id;
                $location->author_id = 1;
                $location->save();

                if ($location) {
                    $location_id = $location->id;
                } else {
                    dd('Ошибка записи адреса для лида: '.$old_lead->name_contact);
                }

                //Определяем тип лида
                $lead_type_id = null;
                if ($old_lead->status_site == 1) {
                    $lead_type_id = 2;
                } else {
                    $lead_type_id = 1;
                }

                // Начинаем писать лида
                $lead = new Lead;
                $lead->company_id = 1;
                $lead->filial_id = 1;
                $lead->name = $old_lead->name_contact;
                $lead->description = null;
                $lead->badget = $old_lead->badget;
                $lead->case_number = $old_lead->num_order;
                $lead->serial_number = $old_lead->serial_number;
                $lead->phone = $old_lead->phone_contact;
                $lead->email = $old_lead->email_contact;
                $lead->location_id = $location_id;
                $lead->site_id = 2;
                $lead->lead_type_id = $lead_type_id;
                $lead->manager_id = $old_lead->manager->new_user_id;
                $lead->stage_id = $old_lead->id_stage;
                $lead->old_lead_id = $old_lead->id;
                $lead->display = 1;
                $lead->author_id = $old_lead->author->new_user_id;
                $lead->save();

                if ($lead) {
                    $lead_id = $lead->id;
                } else {
                    dd('Ошибка записи лида: '.$old_lead->name_contact);
                }

                // Пишем комменты
                $lead_comments = [];

                if (isset($old_lead->comment)) {
                    $lead_comments[] = [
                        'body' => $old_lead->comment,
                        'author_id' => $old_lead->author->new_user_id,
                    ];
                }

                if (isset($old_lead->comment2)) {
                    $lead_comments[] = [
                        'body' => $old_lead->comment2,
                        'author_id' => $old_lead->author->new_user_id,
                    ];
                }

                if (count($old_lead->comments) > 0) {
                    foreach ($old_lead->comments as $comment) {
                        $lead_comments[] = [
                            'body' => $comment->body_note,
                            'author_id' => $comment->author->new_user_id,
                        ];
                    }
                }

                if ($old_lead->fact_pay != 0) {
                    $lead_comments[] = [
                        'body' => 'Фактически оплачено: '.$old_lead->fact_pay,
                        'author_id' => $old_lead->author->new_user_id,
                    ];
                }

                $lead->comments()->saveMany($lead_comments);

                // Пишем задачи
                if (count($old_lead->challenges) > 0) {
                    $lead_challenges = [];

                    foreach ($old_lead->challenges as $challenge) {

                        $status = null;
                        if ($challenge->status_challenge == 2) {
                            $status = 1;
                        }

                        if ($challenge->id_model_challenge == 2) {
                            $challenge_type_id = 2;
                        } elseif ($challenge->id_model_challenge == 4) {
                            $challenge_type_id = 3;
                        } else {
                            $challenge_type_id = 1;
                        }

                        $lead_challenges[] = [
                            'company_id' => 1,
                            'description' => $challenge->comment_challenge,
                            'appointed_id' => $challenge->appointed->new_user_id,
                            'finisher_id' => $challenge->finisher->new_user_id,
                            'author_id' => $challenge->author->new_user_id,
                            'deadline_date' => $challenge->deadline_challenge,
                            'status' => $status,
                            'completed_date' => $challenge->date_completed,
                            'challenges_type_id' => $challenge_type_id,
                        ];
                    }

                    $lead->challenges()->saveMany($lead_challenges);
                }

                // Пишем рекламации
                if (count($old_lead->claims) > 0) {
                    $lead_claims = [];

                    foreach ($old_lead->claims as $claim) {

                        $lead_claims = [
                            'company_id' => 1,
                            'body' => $claim->body_claim,
                            'lead_id' => $lead_id,
                            'old_claim_id' => $claim->id,
                            'author_id' => $claim->author->new_user_id,

                        ];

                    }

                    $lead->challenges()->createMany($lead_claims);
                }

                $lead->parse_status = 1;
                $lead->save();

            }

            echo '50 записей, id:'.$lead->id;	
        });

}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
