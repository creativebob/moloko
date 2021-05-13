
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
                        <p>Отлично, мы получили ваш заказ: №{{ session('confirmation')['lead']->id }} от {{ session('confirmation')['lead']->created_at->format('d.m.Y') }} года<br>
                            Сумма вашего заказа: {{ num_format(session('confirmation')['lead']->badget, 0) }} руб.
                        </p>
                    @endif
                </div>

                <div class="cell small-12 medium-6">
                    <ul class="tabs" data-tabs id="payment-tabs"  data-match-height="true">
                        <li class="tabs-title is-active"><a href="#panel1" aria-selected="true">Сбербанк Онлайн</a></li>
                        <li class="tabs-title"><a data-tabs-target="panel2" href="#panel2">Оплата по счету</a></li>
                    </ul>

                    <div class="tabs-content-payment" data-tabs-content="payment-tabs">
                        <div class="tabs-panel is-active" id="panel1">
                            <table>
                                <tr>
                                    <td>
                                        <img src="../img/{{ $site->alias }}/sb.png">
                                    </td>
                                    <td>
                                        <p>Выполните оплату по номеру телефона:<br><span class="phone-for-payment">+7 (902) 172-77-66</span></p>
                                        <p>Получатель: Шаталин Александр Эдуардович<br>
                                        В сообщении просто укажите: <span class="text-strong">Заказ №{{ session('confirmation')['lead']->id }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="tabs-panel" id="panel2">

                            {{-- Подключаем банковские реквизиты --}}
                            @include('project.composers.company.details', ['company' => $site->company])

                        </div>
                    </div>
                    @auth
                        <p>Отслеживать события по вашему заказу вы можете в <a href="/profile">личном кабинете</a>.</p>
                    @else
                        <p>Отслеживать события по вашему заказу вы можете в <a href="#" data-open="open-modal-login">личном кабинете</a>.</p>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</main>