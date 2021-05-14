<div class="grid-x grid-padding-x">
    <main class="cell small-12 main-content">

        {{-- Заголовок --}}
        @include('mwtour.pages.common.title')

		@include('project.composers.navigations.navigation_by_align', ['align' => 'left'])
        <div class="grid-x">
            <div class="cell small-12">

            @if(Auth::user()->userLeads->isNotEmpty())
                <div class="grid-x">
                    <div class="cell small-6 medium-shrink wrap-logo-bank">
                        <img src="../img/{{ $site->alias }}/tinkoff-bank.jpg">
                    </div>
                    <div class="cell small-6 medium-auto wrap-bank-text">
                        <p>Выполните оплату по номеру карты:<br><span class="phone-for-payment">5536 9139 2690 4703</span></p>
                        <p>Получатель: Шаталина Наталья Сергеевна<br>
                        В сообщении просто укажите: <span class="text-strong">Заказ №{{ session('confirmation')['lead']->id }}</span>
                        </p>
                    </div>
                </div>

                <ul class="my-tours-list">
                        @foreach(Auth::user()->userLeads as $lead)
                            <li>
                                <div class="grid-x">
                                    <div class="small-12 medium-4 wrap-my-tour-img cell">
                                        <img src="{{ getPhotoPathPlugEntity($lead->estimate->services_items->first()->service) }}"
                                             alt="{{ $lead->estimate->services_items->first()->service->process->name }}"
                                             title=""
                                             @if(isset($lead->estimate->services_items->first()->service->process->photo))
                                             width="530"
                                             height="246"
                                             @endif
                                             class="service_photo"
                                        >
                                    </div>
                                    <div class="small-12 cell medium-4 wrap-my-tour-info">
                                        <span>Заказ (бронь) №</span><span>{{ $lead->estimate->number }}</span>
                                        <h2>{{ $lead->estimate->services_items->first()->service->process->name }}</h2>
                                        <span>Стартуем </span><span>{{ $lead->estimate->services_items->first()->flow->start_at->translatedFormat('j F Y') }}</span>
                                    </div>
                                    <div class="small-12 cell medium-4 wrap-my-tour-status">


                                        @if($lead->case_number != null)

                                            @if($lead->estimate->is_dismissed == false)

                                                @if($lead->estimate->registered_at)

                                                    @if($lead->estimate->total == $lead->estimate->payment)
                                                        <span>Заказ оплачен</span>
                                                    @else
                                                        <span>Заказ подтвержден. Ожидаем оплату.</span>
                                                    @endif

                                                @else
                                                    <span>Заказ принят в работу</span>
                                                @endif

                                            @else
                                                <span>Заказ принят в работу</span>
                                            @endif

                                        @else
                                            <span>Заказ на бронирование отправлен</span>
                                        @endif

                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif                
            </div>
            <div class="cell small-12">

            </div>

        </div>
    </main>
</div>
