{!! Form::select('supplier_id', $suppliers->pluck('company.name', 'company.id'), null) !!}

