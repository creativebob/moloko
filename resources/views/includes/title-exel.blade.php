<!-- <a href="{{ URL::to('productsDownload/xls') }}"><button class="button">Скачать Excel xls</button></a> -->
<a href="{{ URL::to('/'.$entity.'_download/xlsx') }}">
  <img src="/crm/img/svg/excel_export.svg">
  <!--  <button class="button">Скачать Excel xlsx</button> -->
</a>
<a>
  <img src="/crm/img/svg/excel_import.svg" data-toggle="exel-import">
</a>
<!-- <button class="button" type="button" data-toggle="exel-import">Загрузить</button> -->
<div class="dropdown-pane" id="exel-import" data-dropdown data-auto-focus="true" data-close-on-click="true">
  {{ Form::open(['url' => '/'.$entity.'_import', 'data-abide', 'novalidate', 'files'=>'true']) }}
  <input type="file" name="file" />
  <button class="button">Импортировать</button>
  {{ Form::close() }}
</div>