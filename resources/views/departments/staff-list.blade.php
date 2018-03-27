<li class="medium-as-last item" id="staff-{{ $staffer['id'] }}" data-name="{{ $staffer['position']['position_name'] }}">
  <div class="medium-as-last-link">
    <span>{{ $staffer['position']['position_name'] }} ( <a href="/staff/{{ $staffer['id'] }}/edit" class="link-recursion">
      @if (isset($staffer['user_id']))
      {{ $staffer['user']['first_name'] }} {{ $staffer['user']['second_name'] }}
      @else
      Вакансия
      @endif
    </a> )</span>
    @if ($staffer['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($staffer['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif
  </div>
  <div class="icon-list">
    <div class="del">
      @if(($staffer['system_item'] != 1) && ($staffer['delete'] == 1) && !isset($staffer['user']))
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="staffer-check-{{ $staffer['id'] }}">
    <label class="label-check" for="staffer-check-{{ $staffer['id'] }}"></label> 
  </div>
</li>