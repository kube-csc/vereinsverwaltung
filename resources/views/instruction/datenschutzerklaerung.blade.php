<?php // TODO: Text muss noch über dei Datenbank eingelesen werden ?>
@extends('layouts.headFrontend')

@section('about' ,'/instruction/1') <?php // TODO: vor dem #About den Routenname hinzufügen verbessern?>
@section('title' ,'Datnschutzerklärung')

@section('content')
  <main id="main">
    <!-- ======= Breadcrumbs Section ======= -->
    <section class="breadcrumbs">
      <div class="container">

        <div class="d-flex justify-content-between align-items-center">
          <h2>Datenschutzerklärung</h2>
          <ol>
            <li><a href="/">Home</a></li>
            <li>Datenschutzerklärung</li>
          </ol>
        </div>
      </div>
    </section><!-- End Breadcrumbs Section -->

          <!-- ======= Datenschutzerklärung Section ======= -->
    <?php  /*<section class="inner-page">  */?>
      <?php  /*<section id="contact" class="contact section-bg">  */?>
    <section id="about" class="about">
     <div class="container">

      <div class="section-title" data-aos="fade-in" data-aos-delay="100">
        <h2>Datenschutzerklärung</h2>
        <?php //$instructions->beschreibung)
        $texausgabe = str_replace(array("\\r\\n", "\\n", "\\r"), '<br>', $instructions->beschreibung);
        $texausgabe = str_replace(array("</li><br>"), '</li>', $texausgabe);
        echo "$texausgabe";
        ?>
      </div>

    </div>

  </section><!-- End About Section -->
  </main><!-- End #main -->

@endsection
