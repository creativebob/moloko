<div class="grid-x grid-margin-x">
    <div class="small-12 medium-12 large-12 cell">
        <estimate-component
            :estimate='@json($lead->estimate)'
            :settings='@json(auth()->user()->company->settings)'
            :outlet-settings='@json($outlet->settings)'

            @isset($discount)
            :discount="{{ $discount }}"
            @endisset
        ></estimate-component>
    </div>
</div>
