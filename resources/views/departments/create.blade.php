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
				<li class="tabs-title">
					<a data-tabs-target="position" href="#position">Должность</a>
				</li>

				@else

				<li class="tabs-title is-active">
					<a href="#department" aria-selected="true">Филиал</a>
				</li>
				<li class="tabs-title">
					<a data-tabs-target="worktimes" href="#worktimes">График работы</a>
				</li>
                <li class="tabs-title">
                    <a data-tabs-target="site" href="#site">Сайт</a>
                </li>

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

       <label>Адресс
           @include('includes.inputs.address', ['value' => isset($department->location->address) ? $department->location->address : null, 'name'=>'address'])
       </label>
       <label>Телефон
           @include('includes.inputs.phone', ['value' => isset($department->main_phone->phone) ? $department->main_phone->phone : null, 'name' => 'main_phone', 'required' => isset($parent_id) ? null : true])
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

{{-- Схема работы --}}
<div class="tabs-panel" id="worktimes">
    <div class="grid-x grid-padding-x align-center">
     <div class="small-8 cell">
      @include('includes.inputs.schedule', ['worktime'=>$department->worktime])
  </div>
</div>
</div>

@empty ($parent_id)
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




{{ Form::close() }}

{{-- Должность --}}
@isset ($parent_id)

{{ Form::model($department, ['id' => 'form-position-create']) }}
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
       {{ Form::checkbox('system_item', 1, $staffer->system_item, ['id'=>'system-item-checkbox-staff']) }}
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


<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>
</div>
</div>

<script type="application/javascript">
	$.getScript("/crm/js/jquery.maskedinput.js");
	$.getScript("/crm/js/inputs_mask.js");
</script>





