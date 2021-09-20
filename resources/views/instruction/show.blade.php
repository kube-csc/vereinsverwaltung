@extends('layouts.frontend')

@foreach($instructions as $instruction)
    @php
        $texausgabe = str_replace(array("\\r\\n", "\\n", "\\r"), '<br>', $instruction->beschreibung);
        $texausgabe = str_replace(array("</li><br>"), '</li>', $texausgabe);
        $ueberschrift=$instruction->ueberschrift;
    @endphp
@endforeach

    @section('title' , $ueberschrift)

    @section('content')

  <main id="main">
    <!-- ======= Breadcrumbs Section ======= -->
    <section class="breadcrumbs">
      <div class="container">

        <div class="d-flex justify-content-between align-items-center">
          <h2>{{ $ueberschrift }}</h2>
          <ol>
            <li><a href="/">Home</a></li>
            <li>{{ $ueberschrift }}</li>
          </ol>
        </div>
      </div>
    </section><!-- End Breadcrumbs Section -->

          <!-- ======= Datenschutzerklärung Section ======= -->
    <?php  /*<section class="inner-page">  */?>
      <?php  /*<section id="contact" class="contact section-bg">  */?>
    <?php /*<section id="about" class="about"> */ ?>
      <section class="inner-page">
      <div class="container">

      <div class="section-title" data-aos="fade-in" data-aos-delay="100">
        <h2>{{ $ueberschrift }}</h2>
          {!! $texausgabe !!}
      </div>

    </div>

  </section><!-- End About Section -->
  </main><!-- End #main -->

@endsection
