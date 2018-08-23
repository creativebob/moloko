@extends('project.layouts.app')

@section('title', 'Контакты')

@section('content')
{{-- Контент --}}
<main class="grid-x align-center contacts">
  <div class="small-11 medium-10 cell">

    <h2 class="text-center title">Контакты</h2>

    <section class="grid-x contacts-info">
      <div class="small-12 large-5 cell">

        <h3>Найти нас просто!</h3>

        <address>г. {{ $department->location->city->name . ', ' . $department->location->address }}</address>

        <ul class="vertical menu">
          @foreach ($staff as $staffer)
          <li>{{ $staffer->user->first_name . ' ' . $staffer->user->second_name }}: <a href="tel:{{ callPhone($staffer->user->phone) }}">{{ decorPhone($staffer->user->phone) }}</a></li>

          @endforeach
          <li>Регистратура: <a href="tel:+79014504556">8 (901) 450-45-56</a></li>
        </ul>

        

      </div>
      <div class="small-12 large-7 cell">
        <iframe frameborder="no" style="border: 1px solid #a3a3a3; box-sizing: border-box;" width="100%" height="400" src="http://widgets.2gis.com/widget?type=firmsonmap&amp;options=%7B%22pos%22%3A%7B%22lat%22%3A52.27544469080976%2C%22lon%22%3A104.28372859954834%2C%22zoom%22%3A16%7D%2C%22opt%22%3A%7B%22city%22%3A%22irkutsk%22%7D%2C%22org%22%3A%2270000001018491039%22%7D"></iframe>
      </div>
    </section>

    

    
  </div>
</main>


@endsection

@section('scripts')
<script type="text/javascript">
  $('.today').removeAttr('class');
  var date = '@php echo date("N"); @endphp';
  $('[id="day-' + date +'"]').attr('class', 'today');
</script>

@endsection