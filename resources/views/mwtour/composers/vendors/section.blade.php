@if ($vendors->isNotEmpty())
    <section>
        <h2>Мы официальные представители</h2>
        <ul class="grid-x grid-padding-x small-up-1 medium-up-1 large-up-2 align-center">
            @foreach($vendors as $vendor)
                <li class="cell" data-equalizer-watch>
                    <div class="media-object">
                        <div class="media-object-section align-self-top">
                            <div class="thumbnail">
                                <img src="{{ getPhotoPath($vendor->supplier->company, 'medium') }}" width="180"
                                     height="120" alt="{{ $vendor->supplier->company->name }}">
                            </div>
                        </div>
                        <div class="media-object-section main-section">
                            <p>{{ $vendor->status }}</p>

                            {{-- Блок для вывода на сайт прикрепленных к вендорам файлов (Для скачивания) --}}
                            @if($vendor->files->isNotEmpty())
                                <ul class="vendor-files">
                                    @foreach($vendor->files as $file)
                                        <li>
                                            <a href="{{ $file->path }}" title="Скачать документ" download>{{ $file->name }}</a>&nbsp;<span class="format-file">{{ $file->extension }}, {{ $file->size }} kb</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
@endif
