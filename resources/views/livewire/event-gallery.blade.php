
      @if($reports->count()>0)
        <div class="row" data-aos="fade-in">
            <div class="col-lg-12 d-flex justify-content-center">
                @auth
                    <ul id="portfolio-flters">
                        <li data-filter=".filter-web">Web</li>
                        <li data-filter=".filter-intern">Intern</li>
                        <li data-filter="*" class="filter-active">All</li>
                    </ul>
                @elseguest
                  @if($reports->count()>10)
                    <ul id="portfolio-flters">
                    @for ($i = 10; $i <= $reports->count(); $i=$i+10)
                        <li data-filter=".filter-10">{{ $i }}</li>
                    @endfor
                        <li data-filter=".filter-10">{{ $i }}</li>
                        <li data-filter="*" class="filter-active">All</li>
                    </ul>
                  @endif
                @endauth
            </div>
        </div>

        <div class="row portfolio-container" data-aos="fade-up">
          @php
            $i=1;
            $ii=10;
          @endphp
          @foreach($reports as $report)
            @auth
               @if($report->webseite=='0')
                 <div class="col-lg-4 col-md-6 portfolio-item filter-intern">
                    <div class="portfolio-wrap">
                        <img src="/storage/eventImage/{{$report->bild}}" class="img-fluid" alt="">
                        <div class="portfolio-links">
                            <a href="/storage/eventImage/{{$report->bild}}" data-gall="portfolioGallery" class="venobox" title="{{$report->titel}}"><i class="bx bx-plus"></i></a>

                        </div>
                    </div>
                 </div>
               @endif
               @if($report->webseite=='1')
                 <div class="col-lg-4 col-md-6 portfolio-item filter-web">
                    <div class="portfolio-wrap">
                        <img src="/storage/eventImage/{{$report->bild}}" class="img-fluid" alt="">
                        <div class="portfolio-links">
                            <a href="/storage/eventImage/{{$report->bild}}" data-gall="portfolioGallery" class="venobox" title="{{$report->titel}}"><i class="bx bx-plus"></i></a>

                        </div>
                    </div>
                </div>
               @endif
            @elseguest
               @if($report->webseite=='1')
                 <div class="col-lg-4 col-md-6 portfolio-item filter-{{ $ii }}">
                    <div class="portfolio-wrap">
                        <img src="/storage/eventImage/{{$report->bild}}" class="img-fluid" alt="">
                        <div class="portfolio-links">
                            <a href="/storage/eventImage/{{$report->bild}}" data-gall="portfolioGallery" class="venobox" title="{{$report->titel}}"><i class="bx bx-plus"></i></a>
                        </div>
                    </div>
                 </div>
                 @php
                   $i= ++$i;
                   if($i>10){
                     $ii=$ii+10;
                     $i=1;
                   }
                  @endphp
               @endif
            @endauth
          @endforeach
        </div>
      @endif
