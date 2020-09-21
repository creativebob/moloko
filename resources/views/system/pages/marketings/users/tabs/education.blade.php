<div class="grid-x">
    <div class="cell small-12 large-5">

        <div class="grid-x grid-padding-x">
            <div class="cell small-12 large-6">
                <label>Специальность
                    @include('includes.inputs.string', ['name'=>'specialty', 'value'=>$user->specialty])
                </label>
            </div>
            <div class="cell small-12 large-6">
                <label>Ученая степень, звание
                    @include('includes.inputs.string', ['name'=>'degree', 'value'=>$user->degree])
                </label>
            </div>
            <div class="cell small-12">
                <label>Информация о человеке (Для сайта):
                    {{ Form::textarea('about', $user->about, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                </label><br>
            </div>
            <div class="cell small-12">
                <label>Фраза
                    @include('includes.inputs.string', ['name'=>'quote', 'value'=>$user->quote])
                </label>
            </div>
        </div>
    </div>
</div>
