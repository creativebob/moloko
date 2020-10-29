<?php

namespace App\Imports;

use App\Subscriber;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;

class SubscribersImport implements ToModel
{

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $string = str_replace(' ', '', $row[0]);
        $array = explode(",", $string);

        foreach ($array as $email) {
            if (! empty($email)) {
                $validator = Validator::make([
                    'email' => $email
                ], [
                    'email' => 'email',
                ]);

                return Subscriber::firstOrCreate([
                    'email' => $email,
                    'is_valid' => $validator->fails() ? false : true,
                    'site_id' => request()->site_id
                ]);
            }
        }
    }
}
