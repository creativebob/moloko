                
                <aside class="cell aside-actions small-12">
                    {{-- <div class="line">
                      <span class="actions-text"><span class="flame"></span>Акции</span>
                    </div> --}}
                    <div class="grid-x contur">
                        <div class="cell small-12 block-actions single-item">
                            @foreach($promotions as $promotion)
                              <div class="item-slide">
                                  <a href="{{ $promotion->link }}">
                                    <picture>
                                        <source media="(max-width: 420px)" srcset="{{ $promotion->tiny->path }} 420w" sizes="100vw">
                                        <source media="(max-width: 800px)" srcset="{{ $promotion->small->path }} 800w" sizes="100vw">
                                        <source media="(max-width: 1024px)" srcset="{{ $promotion->medium->path }} 1024w" sizes="100vw">
                                        <source media="(max-width: 1280px)" srcset="{{ $promotion->large->path }} 1280w" sizes="100vw">
                                        <source media="(max-width: 2000px)" srcset="{{ $promotion->large_x->path }} 2000w" sizes="200vw">
                                        <img srcset="{{ $promotion->medium->path }} 1024w">
                                    </picture>
                                  </a>
                              </div>
                            @endforeach
                        </div>
                    </div>
                </aside>
                @push('scripts')
                    <script type="application/javascript">
                        $(".single-item").slick({

                      // normal options...
                      infinite: false,
                      // arrows: false,

                      // the magic
                      responsive: [{

                          breakpoint: 2000,
                          settings: {
                            dots: true
                          }

                        }, {

                          breakpoint: 1024,
                          settings: {
                            infinite: true,
                            dots: true
                          }

                        }, {

                          breakpoint: 600,
                          settings: {
                            dots: true
                          }

                        }, {

                          breakpoint: 300,
                          settings: "unslick" // destroys slick

                        }]
                    });
                    </script>
                @endpush
