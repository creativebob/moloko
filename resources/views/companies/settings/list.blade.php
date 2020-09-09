@if ($settingsCategories->isNotEmpty())
    @foreach ($settingsCategories as $settingCategory)
        @if ($settingCategory->settings->isNotEmpty())
            @if ($settingCategory->alias == 'sales')
                <settings-stocks-component
                    :category='@json($settingCategory)'
                    :item='@json($company)'
                ></settings-stocks-component>
            @else
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
        @endif


    @endforeach
@endif
