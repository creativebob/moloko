<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\System\Traits\Leadable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\System\Traits\Userable;
use App\Http\Controllers\Traits\Photable;
use App\Lead;
use App\Http\Controllers\Controller;
use App\Representative;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    use Leadable;
    use Locationable;
    use Phonable;
    use Photable;
    use Userable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd($request);
        $result = true;

        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::with([
            'location',
            'company'
        ])
            ->find($id);

        if ($lead) {
            // Подключение политики
//            $this->authorize(getmethod(__FUNCTION__), $lead);

            $data = $request->input();

            $location = $this->getLocation();
            $data['location_id'] = $location->id;

            if (empty($request->user_id)) {
                $user = $this->storeUser();
                $data['user_id'] = $user->id;
            }

            $lead->update($data);

//        $this->savePhones($lead);

            // Проверка на создание представителя
            if ($lead->organization_id) {
                $representative = Representative::where([
                    'user_id' => $lead->user_id,
                    'organization_id' => $lead->organization_id,
                    'company_id' => $lead->company_id,
                ])
                    ->first();

                if (empty($representative)) {
                    Representative::create([
                        'user_id' => $lead->user_id,
                        'organization_id' => $lead->organization_id,
                    ]);
                }
            }

        } else {
            $result = false;
        }

        return response()->json($result);
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
