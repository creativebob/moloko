<div class="grid-x grid-padding-x">
	<div class="small-12 medium-9 cell">

        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 large-9 cell cmv-indicators">
                <div class="grid-x grid-margin-x">
                    <div class="cell shrink">
                        <span class="indicator-name">Вес: </span><span data-amount="0" class="indicators_total total_raws_count_weight">0</span> <span>гр.</span>
                    </div>
                    <div class="cell auto">
                        <span class="indicator-name">Себестоимость: </span><span data-amount="0"  class="indicators_total total_raws_count_cost">0</span> <span>руб.</span>
                    </div>
                </div>
            </div>
            <div class="small-12 medium-6 large-3 cell">
                {{-- Если статус у товара статус черновика, то показываем сырье --}}
                @if ($process->draft)
                    <ul class="menu vertical">
                        <li>
                            <a class="button" data-toggle="dropdown-workflows">Добавить рабочий процесс</a>
                            <div class="dropdown-pane" id="dropdown-workflows" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

                                <ul class="checker" id="categories-list">
                                    @include('products.processes.services.workflows.workflows_list', ['process' => $process])
                                </ul>

                            </div>
                        </li>
                    </ul>
                @endif
            </div>
        </div>

        <div class="small-12 cell">

            {{-- Состав --}}
            <table class="table-compositions">

                <thead>
                    <tr>
                        <th>Категория:</th>
                        <th>Продукт:</th>
                        <th>Кол-во:</th>
                        <th>Продолжительность</th>
                        <th>Себестоимость</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody id="table-workflows">

                    @if ($process->workflows->isNotEmpty())
                        @foreach ($process->workflows as $workflow)
                            @include ('products.processes.services.workflows.workflow_input', $workflow)
                        @endforeach
                    @endif

                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="3"></td>
                        <td>
                            <span class="total_raws_count_weight">0</span> <span>гр.</span>
                        </td>
                        <td>
                            <span class="total_raws_count_cost">0</span> <span>руб.</span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</div>
</div>

@push('scripts')
    @include('products.processes.services.workflows.scripts')
@endpush
