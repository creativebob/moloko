<div class="reveal" id="modal-plugin" data-reveal data-close-on-click="false">

    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Плагин</h5>
        </div>
    </div>

    @if ($plugin->exists)
    {{ Form::model($plugin, ['route' => ['plugins.update', $plugin->id], 'id' => 'form-plugin']) }}
    @method('PATCH')
    @else
    {{ Form::open(['route' => 'plugins.store', 'id' => 'form-plugin']) }}
    @endif

    <div class="grid-x modal-content inputs">

        <div class="small-12 cell">
            <label>Аккаунты
                {!! Form::select('account_id', $accounts->pluck('name', 'id'), null, ['disabled' => $plugin->exists]) !!}
            </label>

            <label>Код
                {!! Form::textarea('code', null) !!}
            </label>

            {!! Form::hidden('id', null, ['id' => 'item-id']) !!}
            {!! Form::hidden('site_id', $plugin->exists ? $plugin->site_id : $site_id) !!}

        </div>

    </div>

    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Сохранить', ['class' => 'button modal-button', 'id' => $plugin->exists ? 'submit-update-plugin' : 'submit-store-plugin']) }}
        </div>
    </div>

    {{ Form::close() }}

    <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>
</div>