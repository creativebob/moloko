<div class="cell small-12">
    <label>Сайт
        {!! Form::select('site_id', $sites->pluck('name', 'id'), $user->site_id) !!}
    </label>
</div>
