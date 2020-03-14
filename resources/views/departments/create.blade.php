<div class="reveal" id="modal-create" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>{{ $title }}</h5>
		</div>
	</div>

	<div class="grid-x tabs-wrap align-center tabs-margin-top">
		<div class="small-8 cell">
			<ul class="tabs-list" data-tabs id="tabs">

				@isset($parent_id)

                    <li class="tabs-title is-active">
                        <a href="#department" aria-selected="true">Отдел</a>
                    </li>

                    @can('create', App\Staffer::class)
                        <li class="tabs-title">
                            <a data-tabs-target="position" href="#position">Должность</a>
                        </li>
                    @endcan

				@else

                    <li class="tabs-title is-active">
                        <a href="#department" aria-selected="true">Филиал</a>
                    </li>

                    @can('index', App\Schedule::class)
                        <li class="tabs-title">
                            <a data-tabs-target="worktimes" href="#worktimes">График работы</a>
                        </li>
                    @endcan

                    @can('index', App\Site::class)
                        <li class="tabs-title">
                            <a data-tabs-target="site" href="#site">Настройки для сайта</a>
                        </li>
                    @endcan

{{--                    @can('index', App\Setting::class)--}}
{{--                        <li class="tabs-title">--}}
{{--                            <a data-tabs-target="tab-settings" href="#tab-settings">Настройки для продаж</a>--}}
{{--                        </li>--}}
{{--                    @endcan--}}

                @endisset

            </ul>
        </div>
    </div>

    <div class="tabs-wrap inputs">

        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::open(['id'=>'form-create', 'data-abide', 'novalidate']) }}

                <div class="tabs-panel is-active" id="department">
                    <div class="grid-x grid-padding-x align-center modal-content inputs">
                        <div class="small-10 cell">

                        {{-- Добавление города --}}
{{-- @include('system.common.includes.city_search', ['item' => $department, 'required' => isset($parent_id) ? null : true])--}}
                            @include('includes.scripts.class.city_search')
                            @include('includes.inputs.city_search', ['city' => isset($department->location->city->name) ? $department->location->city : null, 'id' => 'cityForm', 'required' => isset($parent_id) ? null : true])

                            @isset($parent_id)
                                <label>Расположение
                                    @include('includes.selects.categories_select', ['id' => $department->id, 'parent_id' => $parent_id])
                                </label>
                            @endisset

                       <label>Название
                           @include('includes.inputs.name', ['value' => $department->name, 'required' => true])
                           <div class="item-error">Такой {{ isset($parent_id) ? 'отдел' : 'филиал' }} уже существует в {{ isset($parent_id) ? 'филиале' : 'организации' }}!</div>
                       </label>

                       <label>Адрес
                           @include('includes.inputs.address', ['value' => isset($department->location->address) ? $department->location->address : null, 'name'=>'address'])
                       </label>

                       <label>Телефон
                           @include('includes.inputs.phone', ['value' => isset($department->main_phone->phone) ? $department->main_phone->phone : null, 'name' => 'main_phone', 'required' => isset($parent_id) ? null : true])
                       </label>

                         @if (count($department->extra_phones) > 0)
                             @foreach ($department->extra_phones as $extra_phone)
                                 @include('includes.extra-phone', ['extra_phone' => $extra_phone])
                             @endforeach
                         @else
                             @include('includes.extra-phone')
                         @endif

                             <label>Почта
                                 @include('includes.inputs.email', ['value' => $department->email, 'name' => 'email'])
                             </label>

                       {{ Form::hidden('id', null, ['id' => 'item-id']) }}
                       {{ Form::hidden('filial_id', $filial_id, ['id' => 'filial-id']) }}

                       @include('includes.control.checkboxes', ['item' => $department])

                       <div class="grid-x align-center">
                           <div class="small-6 medium-4 cell text-center">
                            {{ Form::submit('Добавить', ['class' => 'button modal-button submit-create']) }}
                        </div>
                    </div>

                </div>
                </div>
                </div>

@can('index', App\Schedule::class)
{{-- Схема работы --}}
<div class="tabs-panel" id="worktimes">
    <div class="grid-x grid-padding-x align-center">
     <div class="small-8 cell">
      @include('includes.inputs.schedule', ['worktime'=>$department->worktime])
  </div>
</div>
</div>
@endcan

    @can('index', App\Site::class)
        @empty($parent_id)
            {{-- Сайт --}}
            <div class="tabs-panel" id="site">
                <div class="grid-x grid-padding-x align-center">
                    <div class="small-8 cell">
                        <label>Код для карты
                            {!! Form::textarea('code_map', null, []) !!}
                        </label>
                    </div>
                </div>
            </div>
                @endempty
            @endcan

            {{-- Настройки дял продаж --}}
{{--            @can('index', App\Setting::class)--}}
{{--                @empty($parent_id)--}}
{{--                    <div class="tabs-panel" id="tab-settings">--}}
{{--                        <div class="grid-x grid-padding-x align-center">--}}
{{--                            <div class="small-8 cell">--}}
{{--                                @include('system.common.includes.settings.list', ['item' => $department])--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endempty--}}
{{--            @endcan--}}


{{ Form::close() }}

@can('create', App\Staffer::class)
{{-- Должность --}}
@isset ($parent_id)

{{ Form::model($department, ['route' => 'staff.store', 'id' => 'form-position-create']) }}
<div class="tabs-panel" id="position">
    <div class="grid-x grid-padding-x align-center modal-content inputs">
     <div class="small-12 cell">

      @isset($parent_id)
      <label>Расположение
       @include('includes.selects.categories_select', ['id' => $department->id, 'parent_id' => $parent_id])
   </label>
   @endisset

   <label>Должность
       @include('includes.selects.positions')
   </label>

   {{-- @include('includes.control.checkboxes', ['item' => $staffer]) --}}

   {{ Form::hidden('filial_id', $filial_id, ['class' => 'filial-id']) }}

   @php
   $staffer = new App\Staffer;
   @endphp
   {{-- Чекбокс отображения на сайте --}}
   @can ('display', $staffer)
   <div class="small-12 cell checkbox">
       {{ Form::checkbox('display', 1, $staffer->display, ['id' => 'display-checkbox-staff']) }}
       <label for="display-checkbox-staff"><span>Отображать на сайте</span></label>
   </div>
   @endcan

   {{-- Чекбокс модерации --}}
   @can ('moderator', $staffer)
   @moderation ($staffer)
   <div class="small-12 cell checkbox">
       {{ Form::checkbox('moderation', 1, $staffer->moderation, ['id'=>'moderation-checkbox-staff']) }}
       <label for="moderation-checkbox-staff"><span>Временная запись.</span></label>
   </div>
   @endmoderation
   @endcan

   {{-- Чекбокс системной записи --}}
   @can ('system', $staffer)
   <div class="small-12 cell checkbox">
       {{ Form::checkbox('system', 1, $staffer->system, ['id'=>'system-item-checkbox-staff']) }}
       <label for="system-item-checkbox-staff"><span>Сделать запись системной.</span></label>
   </div>
   @endcan

</div>
</div>

<div class="grid-x align-center">
 <div class="small-6 medium-4 cell">
  {{ Form::submit('Добавить должность', ['class'=>'button modal-button', 'id'=>'submit-staffer-create']) }}
</div>
</div>
</div>
{{ Form::close() }}
@endisset
@endcan


<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>
</div>
</div>

<script type="application/javascript">
	$.getScript("/js/system/jquery.maskedinput.js");
	$.getScript("/js/system/inputs_mask.js");
</script>





