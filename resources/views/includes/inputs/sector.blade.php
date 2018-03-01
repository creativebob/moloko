<select name="{{ $name }}" id="sectors-select">
  @foreach ($sectors_list as $sector)
    <option value="{{ $sector['id'] }}" @if($sector['industry_status'] == 1) class="sector" @endif @if($sector_id == $sector['id']) selected @endif>{{ $sector['sector_name'] }}</option>
  @endforeach
</select>