<div class="grid-x">
    <div class="cell small-12 large-4">

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
</div>
