
<main class="cell small-12 delivery">
    <div class="grid-x grid-padding-x">

        <div class="cell small-12">
            <h1 class="h1-cart">{{ $page->header }}</h1>
        </div>

        <div class="cell small-12">

            <div class="grid-x">
                <div class="cell small-12 medium-6">

                    {!! $page->content !!}

                    @if (session('confirmation'))
                        <h2 class="h2-tour">Отлично, мы получили ваш заказ: №{{ session('confirmation')['lead']->id }} от {{ session('confirmation')['lead']->created_at->format('d.m.Y') }} года<h2>
                        <p>
                            Сумма вашего заказа: {{ num_format(session('confirmation')['lead']->badget, 0) }} руб.
                        </p>
                    @endif

                    <div class="grid-x">
                        <div class="cell small-6 medium-4 wrap-logo-bank">
                            <img src="../img/{{ $site->alias }}/tinkoff-bank.jpg">
                        </div>
                        <div class="cell small-6 medium-8">
                            <p>Выполните оплату по номеру карты:<br><span class="phone-for-payment">5536 9139 2690 4703</span></p>
                            <p>Получатель: Шаталина Наталья Сергеевна<br>
                            В сообщении просто укажите: <span class="text-strong">Заказ №{{ session('confirmation')['lead']->id }}</span>
                            </p>
                        </div>
                        <div class="cell small-12">
                        @auth
                            <p>Отслеживать события по вашему заказу вы можете в <a href="/profile">личном кабинете</a>.</p>
                        @else
                            <p>Отслеживать события по вашему заказу вы можете в <a href="#" data-open="open-modal-login">личном кабинете</a>.</p>
                        @endauth
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</main>