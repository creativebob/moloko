@extends('project.layouts.app')

@section('title', 'О нас')

@section('content')
{{-- Контент --}}
<main class="grid-x align-center about">
  <div class="small-11 medium-10 cell">

    <h2 class="text-center title">Косметологи</h2>

    <div class="grid-x grid-margin-x grid-padding-x small-up-1 large-up-2">

      @foreach ($staff as $staffer)
      @php
        $user = $staffer->user;
      @endphp

      <div class="cell bio">
        <div class="grid-x grid-margin-x media">
          <div class="small-12 medium-8 cell">
            @if (isset($user->photo_id))
            <img class="logo" src="/storage/{{ $user->company_id }}/media/users/{{ $user->id }}/img/medium/{{ $user->avatar->name }}" alt="{{ $user->avatar->name }}">
            @endif
          </div>
          <div class="small-12 medium-4 cell media-info">
            <h3>{{ $user->second_name }}<br class="hide-for-small">{{ $user->first_name . ' ' . $user->patronymic }}</h3>
            @if (isset($user->degree))
            <dl>
              <dt>Учёная степень, категория:</dt>
              <dd>{{ $user->degree }}</dd>
            </dl>
            @endif
            <dl>
              <dt>Специальность:</dt>
              <dd>{{ $user->specialty }}</dd>
            </dl>
          </div>
        </div>
        <article>
          @php
         echo $user->about;
         @endphp
        </article>
      </div>

      @endforeach

     <!--  <div class="cell">
        <div class="media-object stack-for-small">
          <div class="media-object-section">
            <img class="logo" src="/img/cosmeticians/cosmetician-2.png" alt="Лого">
          </div>
          <div class="media-object-section">
            <h3>Беломестнова<br>Вита Евгеньевна</h3>
            <dl>
              <dt>Учёная степень, категория:</dt>
              <dd></dd>
            </dl>
            <dl>
              <dt>Специальность:</dt>
              <dd>Косметология</dd>
            </dl>
          </div>
        </div>
        <article>
          <p>Врач косметолог. Окончила лечебный факультет Иркутского государственного медицинского института в 1986 г. В 1988 году - интернатуру по специальности «анестезиология и реаниматология». С 1988 по 2011 врач анестезиолог-реаниматолог ОГБУЗ ИГКБ №3 г.Иркутска , имеет высшую квалификационную  категорию. С 2011 по 2013 прошла клиническую ординатуру по специальности «косметология» при ФГБОУ ВО ИГМУ. С 2013 г по 2015г. работала врачом- косметологом в РЦ «Микрохирургия». С 2015 в отделении косметологии «Профессорской клинике»  ИГМУ.</p>
          <p>Владеет всеми основными практическими навыками в косметологии: деструктивными методиками удаления новообразований кожи, пилингами, иньекционными методиками: мезотерапией, озонотерапией, ботулинотерапией, контурным моделированием; аппаратными технологиями (IPL-терапией, фракционными и другими  лазерными технологиями).</p>
          <p>Имеет печатные работы в центральных журналах. Автор методического пособия для врачей и ординаторов «Неотложные состояния в практике врача косметолога». Является лектором на курсе медицинской косметологии ИГМУ.</p>


        </article>
      </div> -->
    </div>
  </div>
</main>


@endsection

@section('scripts')


@endsection