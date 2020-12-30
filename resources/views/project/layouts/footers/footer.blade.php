                    <footer class="grid-x grid-padding-x" id="footer">
                        <div class="cell small-12 medium-4 large-6 block-footer-1">
                            <span>&copy; @isset($filial->company->foundation_date){{ $filial->company->foundation_date->format('Y') . ' -' }} @endisset {{ date("Y") }} «{{ $filial->company->legal_form->name }} {{ $filial->company->name }}»</span>
                            <br>

                            @isset($filial->company->prename)
                                <span>{{ $filial->company->prename }}</span>
                                <br>
                            @endisset

                            @isset($filial->email)
                                <span>Почта: <a href="mailto:{{ $filial->email }}">{{ $filial->email }}</a></span>
                                <br>
                            @endisset
                        </div>

                        <div class="cell small-12 medium-4 large-3 block-footer-2">
                        </div>

                        <div class="cell small-12 medium-4 large-3 block-footer-3">
                            <div class="grid-x grid-padding-x">
                                <img src="img/{{ $site->alias }}/svg/logo.svg" class="logo-small" alt="Логотип"><br>
                                <span>Разработка сайта: <a>Creative<span class="color-red">Bob</span> Studio</a></span>
                            </div>
                        </div>
                    </footer>
