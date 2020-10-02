<payments-component
    :document='@json($lead->estimate)'
    :payments-types='@json($payments_types)'

    @if(auth()->user()->company->currencies->isNotEmpty())
    :currencies='@json(auth()->user()->company->currencies)'
    @endif

    >
</payments-component>
