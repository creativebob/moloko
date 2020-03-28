<div class="grid-x grid-padding-x inputs">
    <div class="small-12 medium-12 large-12 cell">
        @if($list_user_employees->isNotEmpty())
            <table class="content-table list-employees">
                <thead>
                    <tr>
                        <th>Должность</th>
                        <th>Ставка</th>
                        <th>Отдел</th>
                        <th>Дата принятия</th>
                        <th>Дата увольнения</th>
                        <!-- 										<th>Причина увольнения</th> -->
                        <th>Операции</th>
                    </tr>
                </thead>
                <tbody id="table-raws">
                @if($list_user_employees->isNotEmpty())
                    @foreach($list_user_employees as $employee_item)
                        <tr id="">
                            <td>
                                {{ $employee_item->staffer->position->name }}
                            </td>
                            <td>
                                {{ $employee_item->staffer->rate }}
                            </td>
                            <td>
                                {{ $employee_item->staffer->department->name }}

                                @if($employee_item->staffer->filial_id != $employee_item->staffer->department_id)
                                    <br>
                                    <span>{{ $employee_item->staffer->filial->name }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $employee_item->employment_date->format('d.m.Y') }}
                            </td>
                            <td>
                                @if(isset($employee_item->dismissal_date)){{ $employee_item->dismissal_date->format('d.m.Y') }} @endif
                            </td>
                            {{-- <td>
                                {{ $employee_item->dismissal_description }}
                            </td> --}}
                            <td class="actions">
                                @if($employee_item->dismissal_date == null)
                                    <a class="button alert tiny" id="employee-dismiss" data-id="{{ $employee_item->id }}">Уволить</a>
                                @else
                                    {{-- <a class="button tiny">Изменить</a> --}}
                                @endif
                            </td>
                        <tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        @endif

    </div>


    @if(($list_user_employees->where('dismissal_date', null)->sum('staffer.rate') < 1)&&($list_empty_staff->count() > 0))
        @if(isset($employee->id))
            <div class="small-12 cell">
                <a class="button green-button" id="employee-employment" data-user-id="{{ $employee->user->id }}">Трудоустроить</a>
            </div>
        @else
            <div class="small-12 cell">
                <div class="grid-x grid-padding-x inputs">
                    <div class="small-12 medium-8 cell">
                        <label>Вакантная должность:
                            @include('includes.selects.empty_staff', ['disabled' => true, 'mode' => 'default'])
                        </label>
                    </div>
                    <div class="small-12 medium-4 cell">
                        <label>Дата приема
                            @include('includes.inputs.date', ['name'=>'employment_date', 'required' => true])
                        </label>
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>
