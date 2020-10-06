<div class="grid-x grid-margin-x">
    <div class="small-12 medium-12 large-12 cell">
        <estimate-component
            :estimate='@json($lead->estimate)'
            :settings='@json(getSettings())'

            @if($stocks->isNotEmpty())
            @if($stocks->count() > 1)
            :stocks='@json($stocks)'
            @endif
            @endif
        ></estimate-component>
    </div>
</div>
