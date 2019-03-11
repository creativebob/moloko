{{ Form::select('manufacturer_id', $manufacturers->pluck('company.name', 'id'), $manufacturer_id ?? null, [($draft == true) ? '' : 'disabled']) }}
