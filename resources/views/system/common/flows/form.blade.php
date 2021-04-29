<div class="grid-x">
    <div class="auto cell">
        <div class="grid-x tabs-wrap">
            <div class="small-12 cell">
                <ul class="tabs-list" data-tabs id="tabs">

                    <li class="tabs-title is-active">
                        <a href="#tab-general" aria-selected="true">Информация</a>
                    </li>
{{--                    <li class="tabs-title">--}}
{{--                        <a href="#tab-receipt" aria-selected="true">Поступления</a>--}}
{{--                    </li>--}}

                </ul>
            </div>
        </div>

        <div class="grid-x tabs-wrap inputs">
            <div class="cell small-12 tabs-margin-top">
                <div data-tabs-content="tabs">

                    <div class="tabs-panel is-active" id="tab-general">
                        @include('system.common.flows.tabs.general', ['autoInitiated' => false])
                    </div>

                    <div class="grid-x grid-padding-x">
                        {{-- Чекбоксы управления --}}
                        {{-- @include('includes.control.checkboxes', ['item' => $stock]) --}}

                        <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                            {{ Form::submit('Сохранить', ['class' => 'button']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="shrink cell">
    </div>
</div>

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush



