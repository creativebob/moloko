<div class="grid-x">
    <div class="cell small-12 medium-6 large-5">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12">
                <label>Комментарий
                    @include('includes.inputs.textarea', ['name'=>'competitor_description', 'value' => $competitor->description])
                </label>
            </div>
        </div>
    </div>
    <div class="call small-12 medium-6 large-7">
        <fieldset class="fieldset-access">
            <legend>Конкурентное направление:</legend>
            <div class="grid-x grid-padding-x">
                <div class="small-12 cell">
                    @include('includes.lists.directions')
                </div>
            </div>
        </fieldset>
    </div>
</div>
