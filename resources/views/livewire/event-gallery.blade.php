@if($reports->count() > 0)
   <div class="row" data-aos="fade-in">
        <div class="col-lg-12 d-flex justify-content-center">
           @auth
               <ul id="portfolio-flters">
                   <li data-filter=".filter-web">Web</li>
                   <li data-filter=".filter-intern">Intern</li>
                   <li data-filter="*" class="filter-active">All</li>
               </ul>
           @elseguest
             @if($reports->count() > 12)
               <ul id="portfolio-flters">
               @for ($i = 12 ; $i <= $reports->count() ; $i = $i + 12)
                  <li data-filter=".filter-{{ $i }}">{{ $i }}</li>
               @endfor
                  <li data-filter=".filter-{{ $i }}">{{ $i }}</li>
                  <li data-filter="*" class="filter-active">All</li>
                </ul>
              @endif
            @endauth
        </div>
   </div>

   <div class="row portfolio-container" data-aos="fade-up">
     @php
        $i  = 1;
        $ii = 12;
     @endphp
     @foreach($reports as $report)
        @auth
          @if($report->webseite == '0')
             <div class="col-lg-4 col-md-6 portfolio-item filter-intern">
                <div class="portfolio-wrap">
                    @if($report->bild != Null && !is_file('/storage/eventImage/'.$report->bild))
                        <img src="/storage/eventImage/{{ $report->bild }}" class="img-fluid" alt="{{ $report->titel }}">
                        <div class="portfolio-links">
                            <a href="/storage/eventImage/{{ $report->bild }}" data-gall="portfolioGallery" class="venobox" title="{{ $report->titel }}"><i class="bx bx-plus"></i></a>
                        </div>
                    @endif
                    @if($report->image != Null && !is_file('/storage/eventImage/'.$report->image))
                        <img src="/daten/bilder/{{ $report->image }}" class="img-fluid" alt="{{ $report->titel }}">
                        <div class="portfolio-links">
                            <a href="/daten/bilder/{{ $report->image }}" data-gall="portfolioGallery" class="venobox" title="{{ $report->titel }}"><i class="bx bx-plus"></i></a>
                        </div>
                    @endif
                </div>
             </div>
          @endif
          @if($report->webseite == '1')
             <div class="col-lg-4 col-md-6 portfolio-item filter-web">
                <div class="portfolio-wrap">
                    @if($report->bild != Null && !is_file('/storage/eventImage/'.$report->bild))
                        <img src="/storage/eventImage/{{ $report->bild }}" class="img-fluid" alt="{{ $report->titel }}">
                        <div class="portfolio-links">
                            <a href="/storage/eventImage/{{ $report->bild }}" data-gall="portfolioGallery" class="venobox" title="{{ $report->titel }}"><i class="bx bx-plus"></i></a>
                        </div>
                    @endif
                    @if($report->image != Null && !is_file('/storage/eventImage/'.$report->image))
                        <img src="/daten/bilder/{{ $report->image }}" class="img-fluid" alt="{{ $report->titel }}">
                        <div class="portfolio-links">
                            <a href="/daten/bilder/{{ $report->image }}" data-gall="portfolioGallery" class="venobox" title="{{ $report->titel }}"><i class="bx bx-plus"></i></a>
                        </div>
                    @endif
                </div>
            </div>
          @endif
        @elseguest
          @if($report->webseite == '1')
             <div class="col-lg-4 col-md-6 portfolio-item filter-{{ $ii }}">
                <div class="portfolio-wrap">
                    @if($report->bild != Null && !is_file('/storage/eventImage/'.$report->bild))
                       <img src="/storage/eventImage/{{ $report->bild }}" class="img-fluid" alt="{{ $report->titel }}">
                       <div class="portfolio-links">
                         <a href="/storage/eventImage/{{ $report->bild }}" data-gall="portfolioGallery" class="venobox" title="{{ $report->titel }}"><i class="bx bx-plus"></i></a>
                       </div>
                    @endif
                    @if($report->image != Null && !is_file('/storage/eventImage/'.$report->image))
                        <img src="/daten/bilder/{{ $report->image }}" class="img-fluid" alt="{{ $report->titel }}">
                        <div class="portfolio-links">
                           <a href="/daten/bilder/{{ $report->image }}" data-gall="portfolioGallery" class="venobox" title="{{ $report->titel }}"><i class="bx bx-plus"></i></a>
                        </div>
                    @endif
                </div>
             </div>
             @php
               $i = ++$i;
               if($i > 12){
                 $ii = $ii + 12;
                 $i  = 1;
               }
              @endphp
          @endif
        @endauth
     @endforeach
   </div>
@endif
