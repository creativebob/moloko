                    <header class="grid-x grid-padding-x">
                        <div class="cell small-12 medium-4 large-6 block-header-1">
                            <a href="/" title="На главную">
                                <img src="/img/{{ $site->alias }}/svg/logo.svg" class="logo" alt="Логотип {{ $filial->company->prename ?? $filial->company->name ?? '' }}">
                            </a>
                            <p class="prename">{{ $filial->company->prename ?? '' }}</p>
                        </div>

                        <div class="cell small-12 medium-4 large-3 block-header-2">
                            @include($site->alias.'.includes.catalogs_services.menu_one_level')
                        </div>

                        <div class="cell small-12 medium-4 large-3 block-header-3">
                            <div class="grid-x grid-padding-x">
                                <div class="cell small-12 wrap-phone">
                                    @isset($filial->main_phone)
                                        <a href="tel:+{{ $filial->main_phone->phone }}" class="phone">{{ decorPhone($filial->main_phone->phone) }}</a>
                                    @endisset
                                </div>
                                <div class="cell small-12 wrap-address">
                                    <a>
                                        <span>
                                            @isset($filial->location)

                                            г. {{ $filial->location->city->name }},
                                            {{ $filial->location->address ?? '' }}
                                            @endisset
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </header>