
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
                                        <tr>
                                            <td class="right-border" data-label="Менеджер">Виноградова Алена</td> 
                                            <td data-label="Лидов в работе">{{ num_format(1500, 0) }}</td>
                                            <td data-label="Лидов без задач">{{ num_format(500, 0) }}</td>
                                            <td data-label="Бюджет">{{ num_format(1462300, 0) }}</td>
                                            <td data-label="Отказы" class="right-border">{{ num_format(2, 0) }}</td>
                                            <td data-label="Все задачи">{{ num_format(68, 0) }}</td>
                                            <td data-label="Просроченные">{{ num_format(20, 0) }} <span class="tiny-text">({{ num_format(32.5, 0) }}%)</span></td>
                                            <td data-label="Задачи на сегодня">{{ num_format(47, 0) }}</td>
                                            <td data-label="Задачи на завтра">{{ num_format(78, 0) }}</td>
                                            <td data-label="Задачи на неделю">{{ num_format(151, 0) }}</td>
                                            <td data-label="Отложенные">{{ num_format(151, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="right-border" data-label="Менеджер">Полянская Екатерина</td> 
                                            <td data-label="Лидов в работе">{{ num_format(2500, 0) }}</td>
                                            <td data-label="Лидов без задач">{{ num_format(780, 0) }}</td>
                                            <td data-label="Бюджет">{{ num_format(1462300, 0) }}</td>
                                            <td data-label="Отказы" class="right-border">{{ num_format(0, 0) }}</td>
                                            <td data-label="Все задачи">{{ num_format(34, 0) }}</td>
                                            <td data-label="Просроченные">{{ num_format(8, 0) }}</td>
                                            <td data-label="Задачи на сегодня">{{ num_format(57, 0) }}</td>
                                            <td data-label="Задачи на завтра">{{ num_format(18, 0) }}</td>
                                            <td data-label="Задачи на неделю">{{ num_format(121, 0) }}</td>
                                            <td data-label="Отложенные">{{ num_format(151, 0) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
