<div class="grid-x grid-padding-x">
    <div class="small-12 medium-6 cell">
        <label>Описание:
            {{ Form::textarea('content', $process->content, ['id' => 'content-ckeditor', 'autocomplete' => 'off', 'size' => '10x3']) }}
        </label>

        <label>Description
            @include('includes.inputs.textarea', ['value' => $process->seo_description, 'name' => 'seo_description'])
        </label>

        <label>Keywords
            @include('includes.inputs.textarea', ['value' => $process->keywords, 'name' => 'keywords'])
        </label>

    </div>
</div>
