<?php

namespace App\Http\Controllers\System\Traits;

use App\Representative;

trait Leadable
{

    /**
     * Update the specified resource in storage.
     *
     * @param $lead
     * @return mixed
     */
	public function updateLead($oldLead, $newLead)
    {

        return response()->json(true);

        $newLead = request()->input()->lead;
        $location = $this->getLocation();
        $data['location_id'] = $location->id;

        if (empty($request->user_id)) {
            $user = $this->storeUser();
            $data['user_id'] = $user->id;
        }

        $lead->update($data);

        $this->savePhones($lead);

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

        return $lead;
    }
}
