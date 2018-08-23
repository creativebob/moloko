@extends('project.layouts.app')

@section('title', 'Услуги')

@section('content')
{{-- Контент --}}
<div class="grid-x align-center service">
  <div class="small-11 medium-10 cell">
    <div class="grid-x grid-margin-x">

      <aside class="small-12 large-3 cell">
        <nav class="services-nav">

          @if (isset($catalogs_tree))
          <ul>
            @foreach ($catalogs_tree as $catalog)
            @if($catalog['category_status'] == 1)
            <li class="first-item item">
              <div class="head-item-nav">- {{ $catalog['name'] }} -</div>
              @if (isset($catalog['children']))
              <ul class="menu vertical" data-accordion-menu data-multi-open="false">
                @foreach ($catalog['children'] as $category)
                <li class="medium-item item @if (isset($category['children'])) parent @endif">
                  <a class="medium-link @if ($id == $category['id']) active @endif" href="/services/{{ $category['id'] }}" data-link="{{ $category['id'] }}">{{ $category['name'] }}</a>

                  @if (isset($category['children']))
                  <ul class="menu vertical medium-list nested @if (isset($category['item_id'])) is-active @endif">
                    @foreach ($category['children'] as $category)
                    @include('project.services.categories-list', ['category' => $category, 'id' => $id])
                    @endforeach
                  </ul>
                  @endif
                </li>
                @endforeach
              </ul>
              @endif
            </li>
            @endif
            @endforeach
          </ul>
          @endif



<!--           <section>
            <h2>- КОРРЕКТИРОВКА -</h2>
            <ul class="vertical menu">
              <li><a href="/services">Фотоэпиляция</a></li>
              <li><a href="/services">Контурная пластика</a></li>
              <li><a href="/services">Прокол мочек ушей</a></li>
            </ul>
          </section>
          <section>
            <h2>- ЛЕЧЕНИЕ -</h2>
            <ul class="vertical menu">
              <li><a href="/services">Лечение пигментации</a></li>
              <li><a href="/services">Лечение рубцов</a></li>
              <li><a href="/services">Лечение угревой болезни</a></li>
              <li><a href="/services">Лечение повышеной потливости</a></li>
              <li><a href="/services">Лечение розацеа</a></li>
            </ul>
          </section> -->
        </nav>
      </aside>
      <div class="small-12 large-9 cell service-category">
        <h2 class="text-center title">{{ $current_catalog->name }}</h2>
        <div class="text-center service-photo">
          @if (isset($current_catalog->photo_id))
          <img src="/storage/{{ $current_catalog->company_id }}/media/catalogs/{{ $current_catalog->id }}/img/large/{{ $current_catalog->photo->name }}" alt="{{ $current_catalog->photo->name }}">
          @endif
        </div>
        @php
        echo $current_catalog->description;
        @endphp

        @if (isset($services))
        <table class="services-table stack">
          <caption>Стоимость услуг:</caption>
          <tbody>
            @foreach($services as $service)
            <tr>
              <td>{{ $service->services_article->name }}</td>
              <td class="price">@if (isset($service->price))
                {{ num_format($service->price, 0) }} руб.
                @endif
              </td>

            </tr>
            @endforeach
<!--             <tr>
              <td>Фракционное омоложение кожи (лицо частично)</td>
              <td>6 000</td>
            </tr>
            <tr>
              <td>Фракционное омоложение кожи (шея)</td>
              <td>6 000</td>
            </tr>
            <tr>
              <td>Фракционное омоложение кожи (декольте)</td>
              <td>10 000</td>
            </tr>
            <tr>
              <td>Фракционное омоложение кожи (лицо + шея)</td>
              <td>16 000</td>
            </tr>
            <tr>
              <td>Фракционное омоложение кожи (лицо+ шея+и декольте)</td>
              <td>22 000</td>
            </tr> -->
          </tbody>
        </table>
        @endif
      </div>

    </div>
  </div>
</div>


@endsection

@section('scripts')

@endsection