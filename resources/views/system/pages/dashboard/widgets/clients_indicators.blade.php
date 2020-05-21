<clients-indicators-component
    cur-year="{{ today()->format('Y') }}"
    :cur-month="{{ (int) today()->format('n') }}"
    :years-list='@json($data['yearsList'])'
    :clients-indicators='@json($data['data'])'
></clients-indicators-component>
