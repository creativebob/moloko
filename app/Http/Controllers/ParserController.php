<?php

namespace App\Http\Controllers;

use App\OldLead;
use App\Lead;
use App\Note;
use App\Choice;
use App\City;
use App\Challenge;
use App\Claim;

use App\Company;
use App\Department;
use App\User;
use App\Phone;

use App\Location;

use Carbon\Carbon;

use Illuminate\Http\Request;

class ParserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function locations(Request $request)
    {

        $old_leads = OldLead::where('address_company', '')->update(['address_company' => null]);

        $locations = Location::where('address', '')->update(['address' => null]);

        $users = User::with('location')->get();

        foreach ($users as $user) {

            $location_old = $user->location;
            // dd($location_old);

            $location = Location::firstOrCreate(['address' => $location_old->address, 'city_id' => $location_old->city_id, 'country_id' => $location_old->country_id, 'author_id' => $location_old->author_id]);
                // dd($location);

            if ($location->id != $user->location_id) {
                $user->location()->forceDelete();

                $user->location_id = $location->id;
                $user->save();
            }
        }


        $leads = Lead::with('location')->whereNotNull('old_lead_id')->get();
        // dd($leads);

        foreach ($leads as $lead) {

            $old_lead = OldLead::with('city')->findOrFail($lead->old_lead_id);

            // dd($lead->location);
            if (isset($lead->location->address)) {
                $address = ($lead->location->address != $old_lead->address_company) ? $lead->location->address : $old_lead->address_company;
            } else {
                $address = $old_lead->address_company;
            }
            
            $location = Location::firstOrCreate(['address' => $address, 'city_id' => $old_lead->city->new_city_id], ['country_id' => 1, 'author_id' => 1]);

            if ($location->id != $lead->location_id) {
                $lead->location()->forceDelete();

                $lead->location_id = $location->id;
                $lead->save();
            }
        }

        $locations = Location::whereNull('country_id')->update(['country_id' => 1]);

        dd('Гатова');

    }

    public function city(Request $request)
    {
        $leads = Lead::whereHas('location', function ($q) {
            $q->whereNull('address');
        })
        ->get();

        foreach ($leads as $lead) {
            $location = Location::firstOrCreate(['address' => $lead->location->address, 'city_id' => $lead->location->city_id, 'country_id' => 1], ['author_id' => 1]);

            if ($location->id != $lead->location_id) {
                $lead->location()->forceDelete();

                $lead->location_id = $location->id;
                $lead->save();
            }
        }
        dd('Гатова');

    }

    public function index(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // $cities = City::get('id', 'name');

        set_time_limit(0);

        $old_leads = OldLead::with(['comments.author', 'claims.author', 'task', 'stage', 'manager', 'city', 'service', 'challenges' => function ($query) {
            $query->with('author', 'appointed', 'finisher', 'stage', 'task');
        }])->whereNull('parse_status')->where('phone_contact', '!=', '')->get();

        // dd($old)

        // OldLead::with(['comments.author', 'claims.author', 'task', 'stage', 'manager', 'city', 'service', 'challenges' => function ($query) {
        //     $query->with('author', 'appointed', 'finisher', 'stage', 'task');
        // }])->whereNull('parse_status')->where('phone_contact', '!=', '')->chunk(100, function ($leads) {

        $mass = [];

        foreach ($old_leads as $old_lead) {

            $mass['get_lead'] = $old_lead->id;

                // Пишем локацию
            $location = new Location;
            $location->country_id = 1;
            $location->address = $old_lead->address_company;

            if ($old_lead->id_city == 0) {
                $location->city_id = 2;
            } else {
                $location->city_id = $old_lead->city->new_city_id;
            }

            $location->author_id = 1;
            $location->save();

            if ($location) {
                $location_id = $location->id;

                $mass['location'] = $location;
            } else {
                dd('Ошибка записи адреса для лида: '.$old_lead->name_contact);
            }

                // Определяем тип лида
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
            $lead->serial_number = $old_lead->serial_order;
            $lead->phone = cleanPhone($old_lead->phone_contact);
            $lead->email = $old_lead->email_contact;
            $lead->location_id = $location_id;

                // Определяем тип лида
            $lead_type_id = null;
            if ($old_lead->status_site == 1) {
                $lead->lead_type_id = 2;
                $lead->site_id = 2;
            } else {
                $lead->lead_type_id = 1; 
            }

            $lead->manager_id = $old_lead->manager->new_user_id;
            $lead->stage_id = $old_lead->stage->new_stage_id;
            $lead->old_lead_id = $old_lead->id;
            $lead->display = 1;
            $lead->author_id = $old_lead->manager->new_user_id;
            $lead->created_at = $old_lead->date_order;
            $lead->save();

            $mass['save_lead'] = $lead->id;

            if ($lead) {
                $lead_id = $lead->id;

                $mass['lead'] = $lead;
            } else {
                dd('Ошибка записи лида: '.$old_lead->name_contact);
            }

                // dd($old_lead);
                // Пишем комменты
            $lead_comments = [];

            if ($old_lead->comment != '') {
                $lead_comments[] = [
                    'body' => $old_lead->comment,
                    'company_id' => 1,
                    'author_id' => $old_lead->manager->new_user_id,
                    'created_at' => $old_lead->date_order,
                ];
            }

            if ($old_lead->comment2 != '') {
                $lead_comments[] = [
                    'body' => $old_lead->comment2,
                    'author_id' => $old_lead->manager->new_user_id,
                    'created_at' => $old_lead->date_order,
                ];
            }

            if (count($old_lead->comments) > 0) {

                foreach ($old_lead->comments as $comment) {
                    if ($comment->body_note != '') {
                        $lead_comments[] = [
                            'body' => $comment->body_note,
                            'company_id' => 1,
                            'author_id' => $old_lead->manager->new_user_id,
                            'created_at' => $comment->date_note.' '.$comment->time_note.':00',
                        ];
                    }

                }
            }

            if ($old_lead->fact_pay != 0) {
                $fact_pay = num_format($old_lead->fact_pay, 0);
                $lead_comments[] = [
                    'body' => 'Фактически оплачено: '. $fact_pay,
                    'company_id' => 1,
                    'author_id' => $old_lead->manager->new_user_id,
                    'created_at' => Carbon::now(),
                ];
            }

                // dd($lead_comments);

            $lead->notes()->createMany($lead_comments);

            $mass['comments'] = $lead_comments;

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

                    if (isset($challenge->finisher->new_user_id)) {
                        $finisher_id = $challenge->finisher->new_user_id;
                            # code...
                    } else {
                        $finisher_id = null;
                    }


                    if($challenge->deadline_challenge == '0000-00-00 00:00:00'){
                        $deadline_challenge = null;
                    } else {
                        $deadline_challenge = $challenge->deadline_challenge;
                    };

                    if($challenge->date_completed == '0000-00-00 00:00:00'){
                        $date_completed = null;
                    } else {
                        $date_completed = $challenge->date_completed;
                    };

                    $lead_challenges[] = [
                        'company_id' => 1,
                        'description' => $challenge->comment_challenge,
                        'appointed_id' => $challenge->appointed->new_user_id,
                        'finisher_id' => $finisher_id,
                        'author_id' => $challenge->author->new_user_id,
                        'deadline_date' => $deadline_challenge,
                        'status' => $status,
                        'completed_date' => $date_completed,
                        'challenges_type_id' => $challenge_type_id,
                        'created_at' => $challenge->date_challenge,
                    ];
                }

                    // dd($lead_challenges);
                $lead->challenges()->createMany($lead_challenges);

                $mass['lead_challenges'] = $lead_challenges;
            }

                // Пишем рекламации
            if (count($old_lead->claims) > 0) {
                $lead_claims = [];

                foreach ($old_lead->claims as $claim) {

                    $lead_claims[] = [
                        'company_id' => 1,
                        'body' => $claim->body_claim,
                            // 'lead_id' => $lead_id,
                        'old_claim_id' => $claim->id,
                        'author_id' => $claim->author->new_user_id,
                        'created_at' => $claim->date_claim,

                    ];

                }
                    // dd($lead_claims);
                $lead->claims()->createMany($lead_claims);

                $mass['lead_claims'] = $lead_claims;
            }

            if (isset($old_lead->service->choise_type)) {

                $choice = new Choice;
                $choice->lead_id = $lead_id;
                $choice->choices_id = $old_lead->service->choise_id;
                $choice->choices_type = $old_lead->service->choise_type;
                $choice->save();

                if ($choice == false) {
                    dd('Ошибка записи предпочтения лида.');
                }

                $mass['choice'] = $choice;
            }

            $old_lead->parse_status = 1;
            $old_lead->save();

            if ($old_lead) {

                $mass['old_lead'] = $old_lead;

                $mass['parse_lead'] = $old_lead->parse_status;
            } else {
                dd('Ошибка проставления отметки старому лиду: '.$old_lead->id);

            }


            echo 'Текущий лид: '.$mass['get_lead'].', записан в новую базу: '.$mass['save_lead'].', отметка: '.$mass['parse_lead']."\r\n";   

        }
            // echo '500 записей, id: '.$lead->id.', ';	
        // });

        return redirect('/admin/leads');

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

    public function dublicator(Request $request)
    {

        set_time_limit(0);
        $leads = Lead::with('location', 'notes', 'challenges', 'claims')->where('old_lead_id', '!=', null)->get(['id', 'old_lead_id']);
        // dd($leads);
        $mass = [];
        $count = 0;
        foreach ($leads as $lead) {

            $dubl_lead = Lead::where('old_lead_id', $lead->old_lead_id)->where('id', '!=', $lead->id)->first();

            if($dubl_lead) {
                    // dd($dubl_lead);

                $dubl_lead->location()->forceDelete();
                $dubl_lead->notes()->forceDelete();
                $dubl_lead->challenges()->forceDelete();
                $dubl_lead->claims()->forceDelete();
                $dubl_lead->choices_goods_categories()->forceDelete();
                $dubl_lead->choices_services_categories()->forceDelete();
                $dubl_lead->choices_raws_categories()->forceDelete();

                $dubl_lead->forceDelete();

                if ($dubl_lead) {
                    // $mass[$dubl_lead->id][] = $dubl_lead->case_number;
                    // $mass[$dubl_lead->id][] = $dubl_lead->old_lead_id;
                    $count++;
                }


            }

            if ($count == 200) {
                    // break;
                return redirect('/admin/cities');
            }

        } 


        return redirect('/admin/users');
        // $mass['count'] = $count;
            // dd($mass);

        
    }

    public function adder(Request $request)
    {
       // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // $cities = City::get('id', 'name');

        set_time_limit(0);

        $old_leads = OldLead::with(['comments.author', 'claims.author', 'task', 'stage', 'manager', 'city', 'service', 'challenges' => function ($query) {
            $query->with('author', 'appointed', 'finisher', 'stage', 'task');
        }])->whereNull('parse_status')->where('phone_contact', '!=', '')->get();

        // dd($old)

        // OldLead::with(['comments.author', 'claims.author', 'task', 'stage', 'manager', 'city', 'service', 'challenges' => function ($query) {
        //     $query->with('author', 'appointed', 'finisher', 'stage', 'task');
        // }])->whereNull('parse_status')->where('phone_contact', '!=', '')->chunk(100, function ($leads) {

        $mass = [];

        foreach ($old_leads as $old_lead) {

            $new_lead = Lead::where('old_lead_id', $old_lead->id)->first();

            if ($new_lead) {

                $old_lead->parse_status = 1;
                $old_lead->save();

            } else {

                $mass['get_lead'] = $old_lead->id;

                // Пишем локацию
                $location = new Location;
                $location->country_id = 1;
                $location->address = $old_lead->address_company;

                if ($old_lead->id_city == 0) {
                    $location->city_id = 2;
                } else {
                    $location->city_id = $old_lead->city->new_city_id;
                }
                
                $location->author_id = 1;
                $location->save();

                if ($location) {
                    $location_id = $location->id;

                    $mass['location'] = $location;
                } else {
                    dd('Ошибка записи адреса для лида: '.$old_lead->name_contact);
                }

                // Определяем тип лида
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
                $lead->serial_number = $old_lead->serial_order;
                $lead->phone = cleanPhone($old_lead->phone_contact);
                $lead->email = $old_lead->email_contact;
                $lead->location_id = $location_id;

                // Определяем тип лида
                $lead_type_id = null;
                if ($old_lead->status_site == 1) {
                    $lead->lead_type_id = 2;
                    $lead->site_id = 2;
                } else {
                    $lead->lead_type_id = 1; 
                }

                $lead->manager_id = $old_lead->manager->new_user_id;
                $lead->stage_id = $old_lead->stage->new_stage_id;
                $lead->old_lead_id = $old_lead->id;
                $lead->display = 1;
                $lead->author_id = $old_lead->manager->new_user_id;
                $lead->created_at = $old_lead->date_order;
                $lead->save();

                $mass['save_lead'] = $lead->id;

                if ($lead) {
                    $lead_id = $lead->id;

                    $mass['lead'] = $lead;
                } else {
                    dd('Ошибка записи лида: '.$old_lead->name_contact);
                }

                // dd($old_lead);
                // Пишем комменты
                $lead_comments = [];

                if ($old_lead->comment != '') {
                    $lead_comments[] = [
                        'body' => $old_lead->comment,
                        'company_id' => 1,
                        'author_id' => $old_lead->manager->new_user_id,
                        'created_at' => $old_lead->date_order,
                    ];
                }

                if ($old_lead->comment2 != '') {
                    $lead_comments[] = [
                        'body' => $old_lead->comment2,
                        'author_id' => $old_lead->manager->new_user_id,
                        'created_at' => $old_lead->date_order,
                    ];
                }

                if (count($old_lead->comments) > 0) {

                    foreach ($old_lead->comments as $comment) {
                        if ($comment->body_note != '') {
                            $lead_comments[] = [
                                'body' => $comment->body_note,
                                'company_id' => 1,
                                'author_id' => $old_lead->manager->new_user_id,
                                'created_at' => $comment->date_note.' '.$comment->time_note.':00',
                            ];
                        }
                        
                    }
                }

                if ($old_lead->fact_pay != 0) {
                    $fact_pay = num_format($old_lead->fact_pay, 0);
                    $lead_comments[] = [
                        'body' => 'Фактически оплачено: '. $fact_pay,
                        'company_id' => 1,
                        'author_id' => $old_lead->manager->new_user_id,
                        'created_at' => Carbon::now(),
                    ];
                }

                // dd($lead_comments);

                $lead->notes()->createMany($lead_comments);

                $mass['comments'] = $lead_comments;

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

                        if (isset($challenge->finisher->new_user_id)) {
                            $finisher_id = $challenge->finisher->new_user_id;
                            # code...
                        } else {
                            $finisher_id = null;
                        }


                        if($challenge->deadline_challenge == '0000-00-00 00:00:00'){
                            $deadline_challenge = null;
                        } else {
                            $deadline_challenge = $challenge->deadline_challenge;
                        };

                        if($challenge->date_completed == '0000-00-00 00:00:00'){
                            $date_completed = null;
                        } else {
                            $date_completed = $challenge->date_completed;
                        };

                        $lead_challenges[] = [
                            'company_id' => 1,
                            'description' => $challenge->comment_challenge,
                            'appointed_id' => $challenge->appointed->new_user_id,
                            'finisher_id' => $finisher_id,
                            'author_id' => $challenge->author->new_user_id,
                            'deadline_date' => $deadline_challenge,
                            'status' => $status,
                            'completed_date' => $date_completed,
                            'challenges_type_id' => $challenge_type_id,
                            'created_at' => $challenge->date_challenge,
                        ];
                    }

                    // dd($lead_challenges);
                    $lead->challenges()->createMany($lead_challenges);

                    $mass['lead_challenges'] = $lead_challenges;
                }

                // Пишем рекламации
                if (count($old_lead->claims) > 0) {
                    $lead_claims = [];

                    foreach ($old_lead->claims as $claim) {

                        $lead_claims[] = [
                            'company_id' => 1,
                            'body' => $claim->body_claim,
                            // 'lead_id' => $lead_id,
                            'old_claim_id' => $claim->id,
                            'author_id' => $claim->author->new_user_id,
                            'created_at' => $claim->date_claim,

                        ];

                    }
                    // dd($lead_claims);
                    $lead->claims()->createMany($lead_claims);

                    $mass['lead_claims'] = $lead_claims;
                }

                if (isset($old_lead->service->choise_type)) {

                    $choice = new Choice;
                    $choice->lead_id = $lead_id;
                    $choice->choices_id = $old_lead->service->choise_id;
                    $choice->choices_type = $old_lead->service->choise_type;
                    $choice->save();

                    if ($choice == false) {
                        dd('Ошибка записи предпочтения лида.');
                    }

                    $mass['choice'] = $choice;
                }

                $old_lead->parse_status = 1;
                $old_lead->save();

                if ($old_lead) {

                    $mass['old_lead'] = $old_lead;

                    $mass['parse_lead'] = $old_lead->parse_status;
                } else {
                    dd('Ошибка проставления отметки старому лиду: '.$old_lead->id);

                }

                
                echo 'Текущий лид: '.$mass['get_lead'].', записан в новую базу: '.$mass['save_lead'].', отметка: '.$mass['parse_lead']."\r\n";   
            }
        }
            // echo '500 записей, id: '.$lead->id.', '; 
        // });

        return redirect('/admin/users');

        

        
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function leads_check_bases(Request $request)
    {

        $a = Lead::whereYear('created_at', 2017)->count();
        $b = Lead::whereYear('created_at', 2018)->count();
        $b1 = Lead::whereYear('created_at', 2016)->count();

        $c = OldLead::whereYear('date_order', 2017)->count();
        $d = OldLead::whereYear('date_order', 2018)->count();
        $d1 = OldLead::whereYear('date_order', 2016)->count();

        $mass = [
            'Новая' => [
                '2016' => $b1,
                '2017' => $a,
                '2018' => $b
            ],
            'Старая' => [
                '2016' => $d1,
                '2017' => $c,
                '2018' => $d
            ],
        ];
        dd($mass);

    }

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


    public function andrey(Request $request)
    {
        $leads = Lead::where('manager_id', 5)->update(['manager_id' => 7]);
        $challenges = Challenge::where('appointed_id', 5)->update(['appointed_id' => 7]);

        $challenges = Challenge::where('author_id', 9)->update(['author_id' => 7]);
        dd('Готово!');
    }

    public function lead_type(Request $request)
    {
        $leads = Lead::whereNull('lead_type_id')->update(['lead_type_id' => 1]);
        dd('Заебца!');
    }


    public function old_claims(Request $request)
    {

        $claims = Claim::get();
        foreach ($claims as $claim) {
            $claim->serial_number = $claim->old_claim_id;
            $claim->manager_id = 9;
            $claim->status = 1;
            $claim->save();
        }
        dd('Удача, ебана!');
    }

    public function phone_parser(Request $request)
    {

        $companies = Company::get();
        foreach ($companies as $comapny) {
            if (isset($company->phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate(
                    ['phone' => $company->phone,
                ], [
                    'crop' => substr($company->phone, -4),
                ]);
                $company->phones()->attach($phone->id, ['main' => 1]);
            }
            if (isset($company->extra_phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $company->extra_phone
                ], [
                    'crop' => substr($company->extra_phone, -4),
                ]);
                $company->phones()->attach($phone->id);
            }
        }

        $users = User::get();
        foreach ($users as $user) {
            if (isset($user->phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $user->phone
                ], [
                    'crop' => substr($user->phone, -4),
                ]);
                $user->phones()->attach($phone->id, ['main' => 1]);
            }
            if (isset($company->extra_phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $user->extra_phone
                ], [
                    'crop' => substr($user->extra_phone, -4),
                ]);
                $user->phones()->attach($phone->id);
            }
        }

        $departments = Department::get();
        foreach ($departments as $department) {
            if (isset($department->phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $department->phone
                ], [
                    'crop' => substr($department->phone, -4),
                ]);
                $department->phones()->attach($phone->id, ['main' => 1]);
            }
        }

        $leads = Lead::get();
        foreach ($leads as $lead) {
            if (isset($lead->phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $lead->phone
                ], [
                    'crop' => substr($lead->phone, -4),
                ]);
                $lead->phones()->attach($phone->id, ['main' => 1]);
            }
        }

        dd('Норм');



    }

}
