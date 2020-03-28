<?php

namespace App\Http\Controllers\Api\v1;

use App\Client;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{

    // Настройки контроллера
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->entity_alias = with(new Client)->getTable();
        $this->entity_dependence = false;
        $this->class = Client::class;
        $this->model = 'App\Client';
        $this->type = 'modal';
    }

    public function search($search)
    {

        $clients = Client::with([
            'clientable',
        ])
            ->whereHasMorph('clientable', [User::class, Company::class], function ($q) use ($search) {
                $q->whereHas('phones', function ($q) use ($search) {
                    $q->where('phone', 'like', '%' . $search . '%');
                });
            })
            ->get();
//         dd($clients);

        return response()->json($clients);
    }
}
