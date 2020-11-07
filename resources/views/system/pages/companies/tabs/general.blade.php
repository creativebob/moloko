<div class="grid-x">
    <div class="cell small-12 medium-7 large-5">
        <div class="grid-x grid-padding-x">
            <div class="small-2 medium-2 cell">
                @include('includes.selects.legal_forms', ['value'=>$company->legal_form_id])
            </div>
            <div class="small-10 medium-4 cell">
                <label>Название компании
                    @include('includes.inputs.name', ['value'=>$company->name, 'required' => true])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                {{-- Селект с секторами (Вид деятельности компании) --}}
                <label>Вид деятельности компании
                    @include('includes.selects.sectors_select', ['sector_id' => $company->sector_id])
                </label>
            </div>

            <div class="small-12 medium-6 cell">
                <label>Телефон
                    @include('includes.inputs.phone', ['value' => isset($company->main_phone->phone) ? $company->main_phone->phone : null, 'name'=>'main_phone', 'required' => true])
                </label>
            </div>
            <div class="small-12 medium-6 cell" id="extra-phones">
            @if (count($company->extra_phones) > 0)
                @foreach ($company->extra_phones as $extra_phone)
                    @include('includes.extra-phone', ['extra_phone' => $extra_phone])
                @endforeach
            @else
                @include('includes.extra-phone')
            @endif

            <!-- <span id="add-extra-phone">Добавить номер</span> -->
            </div>

            <div class="small-12 medium-6 cell">
                <label>Почта
                    @include('includes.inputs.email', ['value' => $company->email, 'name' => 'email'])
                </label>
                {{-- Город --}}


            </div>
            <div class="small-12 medium-6 cell">
                @include('includes.selects.countries', ['value'=>$company->location ? $company->location->country_id : null])

                <label>Адрес
                    @include('includes.inputs.address', ['value' => isset($company->location->address) ? $company->location->address : null, 'name'=>'address'])
                </label>
            </div>

            @if ($company->external_control == 0 && auth()->user()->company_id != $company->id)
                <div class="small-12 medium-6 cell">
                    <label>Сайт
                        @include('includes.inputs.name', ['value' => optional($company->domain)->domain, 'name' => 'domain'])
                    </label>
                </div>
            @endif

            <div class="small-12 medium-3 cell">
                <label>Почтовый индекс
                    @include('includes.inputs.zip_code', ['value'=>isset($company->location->zip_code) ? $company->location->zip_code : null, 'name'=>'zip_code'])
                </label>
            </div>
        </div>
    </div>
    <div class="cell small-12 medium-5 large-7 text-left">
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-4 cell">
            </div>
            @include('system.pages.companies.includes.director', ['director' => optional($company->director)->user, 'item' => optional($company->director)->user ?? auth()->user()])
        </div>
    </div>
</div>
