<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  @php
      $Vereinsname = str_replace('_', ' ', env('Verein_DomainName'));
  @endphp
  <title> @yield( 'title' , '$Vereinsname' ) </title>
    @php
        // ToDo: Meta Conten bearbeiten
        $description = str_replace('_', ' ', env('Verein_description'));
        $keywords    = str_replace('_', ' ', env('Verein_Keywords'));
    @endphp
  <meta content="{!! $description !!}" name="descriptison">
  <meta content="{!! $keywords !!}"    name="keywordsanlegen">

  <?php /*
  <!-- Favicons -->
  <link href="asset/img/favicon.png" rel="icon">
  <link href="asset/img/apple-touch-icon.png" rel="apple-touch-icon">
*/ ?>

  <!-- Favicons -->
  <link href="/favicon.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('asset/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/vendor/icofont/icofont.min.css') }}" rel="stylesheet">
  <?php /* <link href="{{ asset('asset/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet"> */ ?>
  <link href="{{ asset('asset/vendor/venobox/venobox.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/vendor/owl.carousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/vendor/aos/aos.css" rel="stylesheet') }}">

  <!-- Template Main CSS File -->
  <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">

 @php  /*
  <!-- Laravell app.style.ss-->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  */
 @endphp

  <?php // TODO: boxicons über webpack einbinden ?>
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">

  @include('layouts.header')

  <!-- =======================================================
  * Template Name: Squadfree - v2.2.0
  * Template URL: https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

@if(env('Verein_Domain')=="ja")
    <!-- ======= Facebook======= -->
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/de_DE/all.js#xfbml=1";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>
@endif

<!-- ======= Header ======= -->
<header id="header" class="fixed-top header-transparent">
  <div class="container d-flex align-items-center">

    <div class="logo mr-auto">
      <h1 class="text-light"><a href="/"><span>{{ str_replace('_' , ' ' , env('Verein_Domain')) }}</span></a></h1>
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href="index.php"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->
    </div>

      @php
          $abteilungMenus  = DB::table('sport_sections')->where('status' , '>' , '1')
          ->where('sportSection_id' , NULL)
          ->orderby('abteilung')
          ->get();
      @endphp


      <nav class="nav-menu d-none d-lg-block">

          <ul>
              <li class="{{ Request::is('home') ? 'active' : '' }}"><a href="/#about">Home</a></li>

              <li class="drop-down {{ Request::is('deep') ? 'active' : '' }}"><a href="/#about">Abteilung</a>
                  <ul>
                      @foreach($abteilungMenus as $sportSectionMenu)

                          @php
                            $sportTeamMenus = DB::table('sport_sections')
                              ->where('status' , '>' , '1')
                              ->where('sportSection_id' , '=' , $sportSectionMenu->id)
                              ->orderby('abteilung')
                              ->get();

                            $sportTeamMenuCount = $sportTeamMenus->count();

                            $first=0;
                          @endphp
                          <li class="{{$sportTeamMenuCount > 0 ? 'drop-down' : ''}}">
                              <a href="/Abteilung/detail/{{ str_replace(' ', '_', $sportSectionMenu->abteilung) }}">
                                 {{ $sportSectionMenu->abteilung }}
                              </a>

                              @foreach($sportTeamMenus as $sportTeamMenu)
                                  @if($first==0)
                                      <ul>
                                          @php
                                              $first=1;
                                          @endphp
                                          <!-- ToDo: Wird für die mobile Version verwendet. Der Link, der eine Ebende höher ist, funktioniert nicht. -->
                                          <li>
                                              <a href="/Abteilung/detail/{{ str_replace(' ', '_', $sportSectionMenu->abteilung) }}">
                                                  {{ $sportSectionMenu->abteilung }}
                                              </a>
                                          </li>
                                          <!-- End -->
                                  @endif
                                          <li>
                                              <a href="/Abteilung/detail/{{ str_replace(' ', '_', $sportTeamMenu->abteilung) }}">
                                                  {{ $sportTeamMenu->abteilung }}
                                              </a>
                                          </li>

                              @endforeach

                                  @if($first==1)
                                      </ul> <!-- Teams -->
                                  @endif

                          </li>  <!-- Abteilung Mannschaft -->
                      @endforeach
                  </ul>   <!-- Abteilung -->
              </li>    <!-- Abteilung Mannschaft   -->
              @php
                  // TODO: Active im Menu funktioniert noch nicht
              @endphp

              <li class="{{ Request::is('/#services') ? 'active' : '' }}"><a href="/#services">Event</a></li>
              <li class="{{ Request::is('/#team') ? 'active' : '' }}"><a href="/#team">Team</a></li>

              <!-- <li><a href="#portfolio">Portfolio</a></li> -->

              <li class="{{ Request::is('/#contact') ? 'active' : '' }}"><a href="/#contact">Kontakt</a></li>
              <li class="{{ Request::is('/anfahrt') ? 'active' : '' }}"><a href="/Anfahrt">Anfahrt</a></li>
          </ul>

      </nav><!-- .nav-menu -->

    </div>
  </header><!-- End Header -->

@yield('content')

<!-- ======= Footer ======= -->
<footer id="footer">
  <div class="footer-top">
    <div class="container">
      <div class="row">

        <div class="col-lg-4 col-md-6">
          <div class="footer-info" data-aos="fade-up" data-aos-delay="50">
            <h3>{{ str_replace('_', ' ', env('Verein_Name')) }}</h3>
            <?php
              /*
                <p class="pb-3"><em>Qui repudiandae et eum dolores alias sed ea. Qui suscipit veniam excepturi quod.</em></p>
                // QUESTION: : Warum em
              */
              ?>
            <p>
              {{ str_replace('_', ' ', env('Verein_Name')) }}<br>
              {{ str_replace('_', ' ', env('Verein_Strasse')) }}<br>
              {{ str_replace('_', ' ', env('Verein_PLZ')) }} {{ str_replace('_', ' ', env('Verein_Ort')) }}<br>
              @if(env('Verein_Telefon')<>"")
               <i class="icofont-telephone"></i>{{ str_replace('_' , ' ' , env('Verein_Telefon')) }}<br>
              @endif
              @if(env('Verein_Fax')<>"")
               <i class="icofont-fax"></i>{{ str_replace('_' , ' ' , env('Verein_Fax')) }}<br>
              @endif
              <i class="icofont-email"></i>
              <a href="mailto:{{ str_replace('_' , ' ' , env('Verein_Email')) }}">{{ str_replace('_' , ' ' , env('Verein_Email')) }}</a>
            </p>

              <div class="social-links mt-3">
                <a href="https://www.facebook.com/KELDatteln" class="facebook" target="_blank"><i class="bx bxl-facebook"></i></a>
                <?php /*
                <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
                <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
                <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
                <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
                */ ?>
              </div>

          </div>
        </div>

          <!--<div class="col-lg-2 col-md-6 footer-links" data-aos="fade-up" data-aos-delay="150"> -->
          <div class="col-lg-4 col-md-6 footer-newsletter" data-aos="fade-up" data-aos-delay="100">
          <h4>Sozial Media</h4>
          <ul>
            <li><a href="https://www.facebook.com/KELDatteln" class="facebook" target="_blank"><i class="bx bxl-facebook"></i>KEL-Datteln</a></li>
            <li><a href="https://de-de.facebook.com/pages/Emscher-Lippe-Dragons/125457887633531" class="facebook" target="_blank"><i class="bx bxl-facebook"></i>Emscher-Lippe-Dragons</a></li>
            <li><a href="https://www.facebook.com/Ohana-Dragons-229310284689451" class="facebook" target="_blank"><i class="bx bxl-facebook"></i>Ohana Dragons</a></li>
            <li><a href="https://www.facebook.com/PinkDragons45711" class="facebook" target="_blank"><i class="bx bxl-facebook"></i>Pink Dragons</a></li>
          </ul>
          <br>
          <h4>Weitere Seiten</h4>
          <ul>
            <li><a href="http://www.kel-datteln.de/index.php?sprung=regatta/eventausgabe.php&amp;menu=regatta&amp;terminsammler=1" target="_blank" class="bx bx-link-external">Rennsportregatta</a></li>
            <li><a href="http://www.day-of-dragons.de" target="_blank" class="bx bx-link-external">Day of Dragons</a></li>
            <li><a href="http://sup.kel-datteln.de"    target="_blank" class="bx bx-link-external">SUP Kurse</a></li>
            <li><a href="http://oc.kel-datteln.de"     target="_blank" class="bx bx-link-external">Outrigger für Vereinsmitglieder Buchen</a></li>
          </ul>
        </div>

<?php /*
        <div class="col-lg-2 col-md-6 footer-links" data-aos="fade-up" data-aos-delay="150">
          <h4>Useful Links</h4>
          <ul>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#">About us</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Services</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Terms of service</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Privacy policy</a></li>
          </ul>
        </div>
*/ ?>
<?php /*
        <div class="col-lg-2 col-md-6 footer-links" data-aos="fade-up" data-aos-delay="250">
          <h4>Our Services</h4>
          <ul>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Web Design</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Web Development</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Product Management</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Marketing</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Graphic Design</a></li>
          </ul>
        </div>
*/ ?>
        <div class="col-lg-4 col-md-6 footer-newsletter" data-aos="fade-up" data-aos-delay="150">
        <?php /* TODO: Netsletter
          <h4>Dein Newsletter</h4>
          <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
          <form action="" method="post">
            <input type="email" name="email"><input type="submit" value="Subscribe">
          </form>
          <br>
          */ ?>
            <h4>Informationen</h4>
            <ul>
                <li><a href="/Anfahrt"><i class="bx bx-map"></i>Anfahrt</a></li> <?php // ToDo: Anzeige akiv im Menu bearbeiten ?>
                <li><a href="/Information/Beiträge"><i class="bx bx-link"></i>Beiträge</a></li>  <?php // ToDo: noch nicht feritg?>
                <li><a href="/Information/Übernachtungskosten"><i class="bx bx-link"></i>Übernachtungskosten</a></li> <?php // ToDo: noch nicht feritg?>
            </ul>
            <br>
            <h4>Dokumente</h4>
            <ul>
                <li><a href="https://www.kel-datteln.de/download/vereinssatzung2010.pdf" target="_blank"><i class="bx bxs-note"></i>Vereinssatzung 2010</a></li> <?php // ToDo: Akiv Menu bearbeiten ?>
                <li><a href="https://www.kel-datteln.de/download/Sportkleidung_2019.pdf" target="_blank"><i class="bx bxs-note"></i>Vereinskleidung</a></li>
            </ul>
            <br>
            <h4>Intern</h4>
            <ul>
                <li><a href="{{ route('login') }}"><i class="bx bx-log-in"></i>Login</a></li>
                <?php // ToDo: Regestrieren noch nicht feritg ?>
                @if (Route::has('register') && 1==2)
                    <li><a href="{{ route('register') }}"><i class="bx bx-log-in"></i>Regestrieren</a></li>
                @endif
            </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="copyright">
      &copy; Copyright <strong><span>{{ str_replace('_', ' ', env('Verein_Name')) }}</span></strong><br>
      All Rights Reserved
    </div>
    <div class="credits">
      <?php /* // NOTE:
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      <br><br>
      */?>
      <a href="/Information/Datenschutzerklärung">Datenschutzerklärung</a> |
      <a href="/Impressum">Impressum</a>
    </div>
  </div>
</footer><!-- End Footer -->

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('asset/vendor/jquery/jquery.min.js') }}"></script>

  <script src="{{ asset('asset/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <?php /* TODO: */ ?>
  <script src="{{ asset('asset/vendor/jquery.easing/jquery.easing.min.js') }}"></script>
  @php /*
  <script src="{{ asset('asset/vendor/php-email-form/validate.js') }}"></script>
  */ @endphp
  <script src="{{ asset('asset/vendor/waypoints/jquery.waypoints.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/counterup/counterup.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/venobox/venobox.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/owl.carousel/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/aos/aos.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('asset/js/main.js') }}"></script>

</body>

    <!-- BotMan WebDrive -->
    @if(env('Verein_ChatBot')=='ja')
        <link rel="stylesheet" type="text/css" href="/asset/vendor/botman/chat.min.css">
        <script>
            var botmanWidget = {
                frameEndpoint: '/asset/vendor/botman/chat.php',
                placeholderText: "Sende eine Nachricht...",
                introMessage: "✋ Hollo, ich bin der KEL ChatBot",
                title: 'KEL ChatBot',
                mainColor: "#1148e0",
                headerTextColor: "#FFFFFF",
                bubbleBackground: "#1148e0",
                bubbleAvatarUrl: "/asset/img/chatbot.jpg",   //https://www.istockphoto.com/
                mobileHeight: "75%",
            };
        </script>
        <script src='/asset/vendor/botman/widget.js'></script>
    @endif

</html>
