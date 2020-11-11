<div class="grid-x">
    <div class="cell small-12 large-3">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12">
                @foreach ($settingsCategories as $settingCategory)
                    @if ($settingCategory->settings->isNotEmpty())

                        <fieldset>
                            <legend>{{ $settingCategory->name }}</legend>
                            <ul>
                                @foreach ($settingCategory->settings as $setting)

                                    <li class="checkbox">
                                        {!! Form::checkbox('settings[]', $setting->id, null, ['id' => "checkbox-setting-{$setting->id}"]) !!}
                                        <label
                                            for="checkbox-setting-{{ $setting->id }}"
                                        >
                                            <span>{{ $setting->name }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </fieldset>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="cell small-12 large-3">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12">
                <fieldset>
                    <legend>Методы платежа</legend>
                    <ul>
                        @foreach ($paymentsMethods as $paymentsMethod)

                            <li class="checkbox">
                                {!! Form::checkbox('payments_methods[]', $paymentsMethod->id, null, ['id' => "checkbox-payments_methods-{$paymentsMethod->id}"]) !!}
                                <label
                                    for="checkbox-payments_methods-{{ $paymentsMethod->id }}"
                                >
                                    <span>{{ $paymentsMethod->name }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </fieldset>
            </div>
        </div>
    </div>
</div>
