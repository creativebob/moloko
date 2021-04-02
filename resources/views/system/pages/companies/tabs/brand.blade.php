<div class="grid-x">
    <div class="cell small-12 large-5">
        <div class="grid-x grid-padding-x">
            <div class="small-12 cell">
                <label>Слоган
                    @include('includes.inputs.name', ['value'=>$company->slogan, 'name' => 'slogan'])
                </label>
            </div>
            <div class="small-12 cell">
                <photo-upload-component
                    :options='@json(['title' => 'Стандартный логотип (jpg или png)', 'name' => 'photo'])'
                    :photo='@json($company->photo)'
                ></photo-upload-component>
                <photo-upload-component
                    :options='@json(['title' => 'Белый логотип (svg)', 'name' => 'white'])'
                    :photo='@json($company->white)'
                ></photo-upload-component>
                <photo-upload-component
                    :options='@json(['title' => 'Черный логотип (svg)', 'name' => 'black'])'
                    :photo='@json($company->black)'
                ></photo-upload-component>
                <photo-upload-component
                    :options='@json(['title' => 'Цветной логотип (svg)', 'name' => 'color'])'
                    :photo='@json($company->color)'
                ></photo-upload-component>
            </div>
        </div>
    </div>
</div>
