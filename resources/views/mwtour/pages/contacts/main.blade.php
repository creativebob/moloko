<div class="grid-x grid-padding-x">
	<aside class="cell small-12 medium-5 large-3 sidebar" data-sticky-container>
        <div class="sticky" data-sticky data-sticky-on="medium" data-top-anchor="278" data-btm-anchor="wrap-sidebar:bottom" data-margin-top="2">
			@include('project.composers.catalogs_services.sidebar')
		</div>
	</aside>
	<main class="cell small-12 medium-7 large-9 main-content">
		<article class="page-content">
			<div class="grid-x">
				<div class="cell small-12">
					{{-- Заголовок --}}
					@include('viandiesel.pages.common.title')
					<div class="grid-x grid-padding-x">
						<div class="cell small-12 large-6" itemscope itemtype="http://schema.org/Organization">
							<span itemprop="name" class="hide">{{ $site->company->name }}</span>

							<table class="unstriped" id="table-contacts">
								<tbody>
									<tr>
										<td>Наш адрес:</td>
										<td>
											<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
												г. <span itemprop="addressLocality">{{ $site->filial->location->city->name }}</span>,
												<span itemprop="streetAddress">{{ $site->filial->location->address }}</span>
											</div>
										</td>
									</tr>

									<tr>
										<td>Основной телефон:</td>
										<td>
											<a href="tel:{{ callPhone($site->filial->main_phone->phone) }}">
												<span itemprop="telephone">{{ decorPhone($site->filial->main_phone->phone) }}</span>
											</a>
										</td>
									</tr>

									@foreach($site->filial->extra_phones as $extra_phone)
									<tr>
										<td>Дополнительный:</td>
										<td>
											<a href="tel:{{ callPhone($extra_phone->phone) }}">
												<span itemprop="telephone">{{ decorPhone($extra_phone->phone) }}</span>
											</a>
										</td>
									</tr>
									@endforeach

									<tr>
										<td>Маркетинг:</td>
										<td>
											<a href="tel:+79041248595">
												<span itemprop="telephone">8 (904) 124-85-95</span>
											</a>
										</td>
									</tr>
									<tr>
										<td>Электронная почта:</td>
										<td>
											<a href="mailto:{{ $site->filial->email }}" target="_blank" title="Напишите нам!">
												<span itemprop="email">{{ $site->filial->email }}</span>
											</a>
										</td>
									</tr>
								</tbody>
							</table>

							<p>Режим работы:<br />10:00 - 19:00 (Понедельник - Пятница)<br />Обед: с 13:00 до 14:00</p>
							{{-- @include('project.composers.worktimes.table') --}}

                            <div class="media-object-section main-section">
                                @if($site->company->files->where('display', true)->isNotEmpty())
                                    <ul class="vendor-files">
                                        @foreach($site->company->files->where('display', true) as $file)
                                            <li>
                                                <a href="{{ $file->path }}" title="Скачать документ" download>{{ $file->name }}</a>&nbsp;<span class="format-file">{{ $file->extension }}, {{ $file->size }} kb</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

						</div>

						<div class="small-12 large-6 cell wrap-map">
							<div class="map contur">
								@if(isset($site->filial->code_map))
									{!! $site->filial->code_map !!}
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</article>
	</main>
</div>
