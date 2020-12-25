<div class="grid-x grid-padding-x">
    <div class="cell small-12">
        <lead-description-component></lead-description-component>
    </div>

    <div class="cell small-12">
        <lead-labels-component
            :active-labels="{{ $lead->estimate->labels }}"
        ></lead-labels-component>
    </div>
</div>
