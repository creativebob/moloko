<div class="grid-x grid-margin-x">
    <div class="small-12 medium-12 large-12 cell">
        <estimate-component
            :estimate='@json($lead->estimate)'
            :settings='@json(getSettings())'

            @isset($discount)
                :discount="{{ $discount }}"
                @endisset

            @if($stocks->isNotEmpty())
            :stocks='@json($stocks)'
            @endif
        ></estimate-component>
    </div>
</div>
