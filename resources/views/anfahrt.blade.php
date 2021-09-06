@extends('layouts.frontend')

@section('about' ,'/anfahrt') <?php // TODO: vor dem #About den Routenname hinzufügen verbessern?>
@section('title' ,'Anfahrt')

@section('content')
  <main id="main">

      <!-- ======= Breadcrumbs Section ======= -->
      <section class="breadcrumbs">
        <div class="container">

          <div class="d-flex justify-content-between align-items-center">
            <h2>Anfahrt</h2>
            <ol>
              <li><a href="/">Home</a></li>
              <li>Anfahrt</li>
            </ol>
          </div>
        </div>
      </section><!-- End Breadcrumbs Section -->

            <!-- ======= Anfahrt Section ======= -->
            <?php // // ToDo: Bereinigung ?>
      <?php  /*<section class="inner-page">  */?>
      <?php  /*    <section id="anfahrt" class="contact section-bg"> */?>

    <section id="anfahrt" class="contact section-bg">
      <div id="about" class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
          <h2>Anfahrt über die A2</h2>
          <h3>Castrop-Rauxel Henrichenburg / Datteln</h3>
          <p>
            An der Ampel rechts, dem Straßenverlauf der B235 folgen.
            In Datteln schwenkt die B235 rechts in Richtung Olfen / Lüdinghausen.
            An der Aral Tankstelle rechts (dort steht auch ein "Hochhaus") in Richtung Waltrop.
            Nächste Straße links (Hafenstr.).
            Dem Straßenverlauf folgen und über den Kanal.
            Nächste Str. links "Zu den Sportstätten",
            bis zum Ende fahren. Dort ist das Bootshaus.
          </p>
          oder
          <h3>Dortmund Mengede (innerhalb des Kreuz Nord-West)</h3>
          <p>
            An der Ampel links in Richtung Waltrop / Datteln.
            Man fährt 3 mal über einen Kanal (das 2. und 3. mal erst nach der
            Ortsdurchfahrt Waltrop),
            nach der 3. Kanalquerung die nächste Straße rechts (Hafenstr.).
            Dem Straßenverlauf folgen und wieder einmal über den Kanal zurück.
            Die nächste Str. links "Zu den Sportstätten",
            bis zum Ende fahren. Dort ist das Bootshaus.
          </p>
        </div>

        <div class="row" data-aos="fade-up" data-aos-delay="125">
          <div class="col-lg-6">
            <div class="info-box mb-4">
              <i class="bx bx-map"></i>
              <h3>Adresse</h3>
              <p>
                 @if (env('Verein_Name')<>"")
                  {{ str_replace('_', ' ', env('Verein_Name')) }}
                 @else
                  {{'Hier steht die Vereinsanschrift. Bitte in der .Env die Daten pflegen'}}
                 @endif
                  <br>
                  {{ str_replace('_', ' ', env('Verein_Strasse')) }}<br>
                  {{ str_replace('_', ' ', env('Verein_PLZ')) }} {{ str_replace('_', ' ', env('Verein_Ort')) }}
              </p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="info-box  mb-4">
              <i class="bx bx-envelope"></i>
              <h3>Email</h3>
              <p><a href="mailto:{{ str_replace('_', ' ', env('Verein_Email')) }}">{{ str_replace('_', ' ', env('Verein_Email')) }}</a></p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="info-box  mb-4">
              <i class="bx bx-phone-call"></i>
              <h3>Telefon</h3>
              <p>
               @if(env('Verein_Telefon')<>"")
                 Tel: {{ str_replace('_', ' ', env('Verein_Telefon')) }}<br>
               @endif
               @if(env('Verein_Fax')<>"")
                 Fax: {{ str_replace('_', ' ', env('Verein_Fax')) }}<br>
               @endif
              </p>
            </div>
          </div>

        </div>

        <div class="row" data-aos="fade-up" data-aos-delay="200">

          <div class="col-lg-6 ">
            <iframe class="mb-4 mb-lg-0" src="https://maps.google.de/maps?q=kanuten+emscher+lippe&amp;ie=UTF8&amp;hq=kanuten+emscher+lippe&amp;hnear=Dorsten,+M%C3%BCnster,+Nordrhein-Westfalen&amp;t=m&amp;ll=51.659459,7.365475&amp;spn=0.012778,0.027466&amp;z=15&amp;iwloc=A&amp;output=embed" frameborder="0" style="border:0; width: 100%; height: 384px;" allowfullscreen></iframe>
            <?php /*
            <small>
            <a href="https://maps.google.de/maps?q=kanuten+emscher+lippe&amp;ie=UTF8&amp;hq=kanuten+emscher+lippe&amp;hnear=Dorsten,+M%C3%BCnster,+Nordrhein-Westfalen&amp;t=m&amp;ll=51.659459,7.365475&amp;spn=0.012778,0.027466&amp;z=15&amp;iwloc=A&amp;source=embed" style="color:#0000FF;text-align:left" target="_blank">Größere Kartenansicht</a>
            </small>
            <?php /*
            <iframe class="mb-4 mb-lg-0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d12097.433213460943!2d-74.0062269!3d40.7101282!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb89d1fe6bc499443!2sDowntown+Conference+Center!5e0!3m2!1smk!2sbg!4v1539943755621" frameborder="0" style="border:0; width: 100%; height: 384px;" allowfullscreen></iframe>
            */ ?>
          </div>

          <div class="col-lg-6">


          </div>

        </div>

      </div>
    </section> <!-- End Anfahrt-->
    </main><!-- End #main -->
@endsection
