<div class="grid-x grid-padding-x inputs">
    <div class="small-12 medium-7 large-5 cell tabs-margin-top">

        {{-- Сайт --}}
        <label>Название сайта
            @include('includes.inputs.name', ['value' => $site->name, 'required' => true])
        </label>

        <label>Домен сайта {{ $site->domen }}
            @include('includes.inputs.varchar', ['value' => $site->domain, 'name' => 'domain', 'required' => true, 'check' => true])
            <div class="sprite-input-right find-status" id="name-check"></div>
            <div class="item-error">Такой сайт уже существует!</div>
        </label>

    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">

        <fieldset class="fieldset-access">
            <legend>Разделы сайта</legend>
            @include('includes.lists.site_menus')
        </fieldset>

        <fieldset class="fieldset-access">
            <legend>Филиалы</legend>
            @include('includes.lists.departments', ['filials' => true])
        </fieldset>

    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $site])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class'=>'button site-button']) }}
    </div>
</div>

<script type="text/javascript">
    var entity = '{{ $site->getTable() }}';
</script>

