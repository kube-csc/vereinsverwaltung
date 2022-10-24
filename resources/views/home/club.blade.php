@extends('layouts.frontend')

@section('about' , '/'.env('MENUE_VEREIN') ) <?php // ToDo: vor dem #About den Routenname hinzufügen verbessern?>
@section('title' , env('MENUE_VEREIN'))

@section('content')
  <main id="main">

      <!-- ======= Services Section ======= -->
      <section id="services" class="services">
          <div class="container">

              <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                  <h2>{{ env('MENUE_VEREIN') }}</h2>
              </div>

              @php
                  $delay=0;
              @endphp

              <div class="row">
              @foreach($clubs as $club)

                 @php
                   $sporttypes = DB::table('sporttypes')
                       ->join('club_sporttype' , 'sporttypes.id' , '=' , 'club_sporttype.sporttype_id')
                       ->where('club_sporttype.club_id' , $club->id)
                      ->orderby('sporttypes.sportart')
                       ->get();
                 @endphp

                 <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                     <div class="icon-box" data-aos="fade-up"
                     @if($loop->first)
                         data-aos-delay="{{$delay}}"
                     @endif
                     >
                         <h4 class="title"><a href="">{{ $club->clubname }}</a></h4>
                         @if($club->clubhomepage<>"")
                             <a href="{{ $club->clubhomepage }}" target="_blank">Homepage</a>
                         @endif
                         @if($sporttypes->count()>0)
                         <p class="description">{{ env('MENUE_VERBAND') }}:</p>
                         @endif
                         @foreach($sporttypes as $sporttype)
                         <p class="description">{{ $sporttype->sportart }}</p>
                         @endforeach
                     </div>
                 </div>
              @php
                $delay=$delay+100;
              @endphp
              @endforeach
              </div>
          </div>
      </section><!-- End Services Section -->

  </main><!-- End #main -->
@endsection
