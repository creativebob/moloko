{{ Form::select('manufacturer_id', $manufacturers->pluck('company.name', 'id'), $manufacturer_id ?? null, [$disabled ? 'disabled' : '', 'placeholder' => 'Любой']) }}
