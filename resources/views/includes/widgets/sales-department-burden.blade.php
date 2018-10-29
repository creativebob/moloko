
                                <table class="widget-table stack unstriped hover responsive-card-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="right-border">Менеджер</th>
                                            <th colspan="4" class="common-th right-border">Лиды</th>
                                            <th colspan="6" class="common-th">Кол-во задач</th>
                                        </tr>
                                        <tr>
                                            <th>В работе</th>
                                            <th>Без задач</th>
                                            <th>Бюджет</th>
                                            <th class="right-border">Отказы</th>
                                            <th>Все</th>
                                            <th>Просрочка</th>
                                            <th>Сегодня</th>
                                            <th>Завтра</th>
                                            <th>Неделя</th>
                                            <th>Отложенные</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($widget as $name => $data)
                                        <tr>
                                            <td class="right-border" data-label="Менеджер">{{ $name }}</td> 
                                            <td data-label="Лидов в работе">{{ num_format($data['leads_work'], 0) }}</td>
                                            <td data-label="Лидов без задач">{{ num_format($data['leads_without_challenges'], 0) }}</td>
                                            <td data-label="Бюджет">{{ num_format($data['leads_badget'], 0) }}</td>
                                            <td data-label="Отказы" class="right-border">{{ num_format(2, 0) }}</td>
                                            <td data-label="Все задачи">{{ num_format($data['challenges_work_count'], 0) }}</td>
                                            <td data-label="Просроченные">{{ num_format($data['challenges_last_count'], 0) }} <span class="tiny-text">({{ num_format($data['challenges_last_percent'], 1) }}%)</span></td>
                                            <td data-label="Задачи на сегодня">{{ num_format($data['challenges_today_count'], 0) }}</td>
                                            <td data-label="Задачи на завтра">{{ num_format($data['challenges_tomorrow_count'], 0) }}</td>
                                            <td data-label="Задачи на неделю">{{ num_format($data['challenges_week_count'], 0) }}</td>
                                            <td data-label="Отложенные">{{ num_format($data['challenges_future_count'], 0) }}</td>
                                        </tr>
                                        @endforeach
                    
                                    </tbody>
                                </table>
