@extends('layouts.frontend')

@section('about' , '/'.env('MENUE_VERBAND') ) <?php // ToDo: vor dem #About den Routenname hinzufügen verbessern?>
@section('title' , env('MENUE_VERBAND'))

@section('content')
  <main id="main">

      <!-- ======= Services Section ======= -->
      <section id="services" class="services">
          <div class="container">

              <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                  <h2>{{ env('MENUE_VERBAND') }}</h2>
              </div>

              @php
                  $delay=0;
              @endphp

              <div class="row">
              @foreach($sporttypes as $sporttype)

                  @php
                      $clubs = DB::table('clubs')
                        ->join('club_sporttype' , 'clubs.id' , '=' , 'club_sporttype.club_id')
                        ->where('club_sporttype.sporttype_id' , $sporttype->id)
                        ->orderby('clubs.clubname')
                        ->get();
                  @endphp

                  <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                      <div class="icon-box" data-aos="fade-up"
                      @if($loop->first)
                          data-aos-delay="{{$delay}}"
                      @endif
                      >
                          <h4 class="title"><a href="">{{ $sporttype->sportart }}</a></h4>
                          <p class="description">{{ env('MENUE_VEREIN') }}:</p>
                          @foreach($clubs as $club)
                              <p class="description">{{ $club->clubname }}</p>
                          @endforeach
                      </div>
                  </div>
              @php
                  $delay=$delay+100;
              @endphp
              @endforeach
              </div>


              </div>

          </div>
      </section><!-- End Services Section -->

  </main><!-- End #main -->
@endsection
