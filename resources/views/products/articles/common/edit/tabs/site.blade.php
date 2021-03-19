<div class="grid-x grid-padding-x">
    <div class="small-12 medium-6 cell">
        <label>Описание:
            {{ Form::textarea('content', $article->content, ['id' => 'content-ckeditor', 'autocomplete' => 'off', 'size' => '10x3']) }}
        </label>
    </div>
</div>

