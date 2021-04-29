<div class="grid-x grid-padding-x">
    <main class="cell small-12 wrap-content-cart">

        {{-- Заголовок --}}
        @include('viandiesel.pages.common.title')

        <div class="grid-x grid-padding-x">

            <div class="cell small-12 medium-9 wrap-cart-table">
                <cart-component></cart-component>

                <div class="grid-x grid-padding-x">
                    <div class="cell small-12 large-4">
                        <p>{!! $page->content !!}</p>
                    </div>
                </div>
            </div>

            <div class="cell small-12 medium-3 wrap-form-cart">
                <cart-form-component name="{{ optional(auth()->user())->first_name }}" phone="{{ isset(auth()->user()->main_phone) ? decorPhone(auth()->user()->main_phone->phone) : null }}">
                    @csrf
                </cart-form-component>
            </div>

        </div>

    </main>

</div>
