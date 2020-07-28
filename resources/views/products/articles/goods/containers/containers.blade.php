<compositions-component
    :categories='@json($containersCategories)'
    :items='@json($containers->setAppends([
        'weight',
        'cost_unit'
    ]))'
    :item-items='@json($article->containers->setAppends([
        'weight',
        'cost_unit'
    ]))'
    name="containers"
    @if($article->draft == 0)
        :disabled="true"
    @endif
></compositions-component>

{{--<div class="grid-x grid-padding-x">--}}
{{--	<div class="small-12 medium-9 cell">--}}

{{--		<div class="grid-x grid-padding-x">--}}
{{--			<div class="small-12 medium-6 large-9 cell cmv-indicators">--}}
{{--				<div class="grid-x grid-margin-x">--}}
{{--					<div class="cell shrink">--}}
{{--						<span class="indicator-name">Вес: </span><span data-amount="0" class="indicators_total total_containers_count_weight">0</span> <span>гр.</span>--}}
{{--					</div>--}}
{{--					<div class="cell auto">--}}
{{--						<span class="indicator-name">Себестоимость: </span><span data-amount="0" class="indicators_total total_containers_count_cost">0</span> <span>руб.</span>--}}
{{--					</div>--}}
{{--				</div>--}}

{{--				--}}{{-- <p>Использовать фото упаковки вместо фото товара?</p><br>--}}
{{--				<div class="cell small-12 switch tiny">--}}
{{--					<input class="switch-input" id="yes-no" type="checkbox" name="exampleSwitch">--}}
{{--					<label class="switch-paddle" for="yes-no">--}}
{{--						<span class="show-for-sr">Использовать фото упаковки вместо фото товара?</span>--}}
{{--						<span class="switch-active" aria-hidden="true"> Да</span>--}}
{{--						<span class="switch-inactive" aria-hidden="true">Нет</span>--}}
{{--					</label>--}}
{{--				</div> --}}


{{--			</div>--}}
{{--			<div class="small-12 medium-6 large-3 cell">--}}
{{--				--}}{{-- Если статус у товара статус черновика, то показываем сырье --}}
{{--				@if ($article->draft)--}}
{{--				<ul class="menu vertical">--}}
{{--					<li>--}}
{{--						<a class="button" data-toggle="dropdown-containers">Добавить упаковку</a>--}}
{{--						<div class="dropdown-pane" id="dropdown-containers" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">--}}

{{--							<ul class="checker" id="categories-list">--}}
{{--								@include('products.articles.goods.containers.containers_list', ['article' => $article])--}}
{{--							</ul>--}}

{{--						</div>--}}
{{--					</li>--}}
{{--				</ul>--}}
{{--				@endif--}}
{{--			</div>--}}
{{--		</div>--}}

{{--        <div class="grid-x grid-padding-x">--}}
{{--            <div class="small-12 cell">--}}
{{--                --}}{{-- Упаковка --}}
{{--                <table class="table-compositions">--}}

{{--                    <thead>--}}
{{--                        <tr>--}}
{{--                            <th>п/п</th>--}}
{{--                            <th>Категория:</th>--}}
{{--                            <th>Продукт:</th>--}}
{{--                            <th>Кол-во:</th>--}}
{{--                            <th>Использование:</th>--}}
{{--                            --}}{{-- <th>Отход:</th>--}}
{{--                            <th>Остаток:</th>--}}
{{--                            <th>Операция над остатком:</th> --}}
{{--                            <th>Вес</th>--}}
{{--                            <th>Себестоимость</th>--}}
{{--                            <th></th>--}}
{{--                        </tr>--}}
{{--                    </thead>--}}

{{--                    <tbody id="table-containers">--}}

{{--                        @if ($article->containers->isNotEmpty())--}}
{{--                            @foreach ($article->containers as $container)--}}
{{--                                @include ('products.articles.goods.containers.container_input', $container)--}}
{{--                            @endforeach--}}
{{--                        @endif--}}

{{--                    </tbody>--}}
{{--                    <tfoot>--}}
{{--                        <tr>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td>--}}
{{--                                <span class="total_containers_count_weight">0</span> <span>гр.</span>--}}
{{--                            </td>--}}
{{--                            <td>--}}
{{--                                <span class="total_containers_count_cost">0</span> <span>руб.</span>--}}
{{--                            </td>--}}
{{--                            <td></td>--}}
{{--                        </tr>--}}
{{--                    </tfoot>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--	</div>--}}
{{--</div>--}}
{{--<div class="small-12 medium-3 cell">--}}


{{--</div>--}}

{{--@push('scripts')--}}
{{--	@include('products.articles.goods.containers.scripts')--}}
{{--@endpush--}}
