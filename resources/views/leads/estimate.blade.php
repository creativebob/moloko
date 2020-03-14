<estimate-component
    :estimate='@json($lead->estimate)'
    :settings='@json(getSettings())'

    @if($stocks->isNotEmpty())
        @if($stocks->count() > 1)
            :stocks='@json($stocks)'
        @endif
    @endif
></estimate-component>



