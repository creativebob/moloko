<div class="grid-x grid-padding-x">

    <div class="small-12 medium-5 cell">
        <promotion-component
            :promotion='@json($promotion)'
        ></promotion-component>

        <div class="grid-x">
            <div class="cell small-12 checkbox">
                {!! Form::hidden('is_slider', 0) !!}
                {!! Form::checkbox('is_slider', 1, $promotion->is_slider, ['id' => 'checkbox-is_slider']) !!}
                <label for="checkbox-is_slider"><span>Отображать слайдер</span></label>
            </div>
        </div>
    </div>

    <div class="small-12 medium-7 cell">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-6">
                <label>Alt
                    @include('includes.inputs.name', ['name' => 'alt'])
                </label>
            </div>
            <div class="cell small-12 medium-6">
                <label>Title
                    @include('includes.inputs.name', ['name' => 'title'])
                </label>
            </div>
        </div>
    </div>

</div>
