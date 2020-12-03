<?php

namespace App\Http\View\Composers\System;

use App\AgentType;

use Illuminate\View\View;

class AgentTypesComposer
{
	public function compose(View $view)
	{

        $agent_types_list = AgentType::get()->pluck('name', 'id');
		return $view->with('agent_types_list', $agent_types_list);

	}

}
