<payments-component
    :document='@json($lead->estimate)'


    @if(auth()->user()->company->currencies->isNotEmpty())
    :currencies='@json(auth()->user()->company->currencies)'
    @endif

    >
</payments-component>

{{--:payments-types='@json($payments_types)'--}}
