<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  @php
      $vereinsname = str_replace('_', ' ', env('VEREIN_DOMAIN'));
  @endphp
  <title> @yield( 'title' , '$vereinsname' ) </title>
    @php
        // ToDo: Meta Conten bearbeiten
        $description = str_replace('_', ' ', env('VEREIN_DESCRIPTION'));
        $keywords    = str_replace('_', ' ', env('VEREIN_KEYWORDS'));
    @endphp
  <meta content="{!! $description !!}" name="descriptison">
  <meta content="{!! $keywords !!}"    name="keywordsanlegen">

  <!-- Favicons -->
  <link href="/favicon.ico" rel="icon">
  <link href="/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('asset/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/vendor/icofont/icofont.min.css') }}" rel="stylesheet">
  <?php /* <link href="{{ asset('asset/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet"> */ ?>
  <link href="{{ asset('asset/vendor/venobox/venobox.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/vendor/owl.carousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/vendor/aos/aos.css') }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">

 @php  /*
  <!-- Laravell app.style.ss-->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  */
 @endphp

  <?php // ToDo: boxicons über webpack einbinden ?>
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">

  @include('layouts.header')

  <!-- =======================================================
  * Template Name: Squadfree - v2.2.0
  * Template URL: https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

  @livewireStyles

</head>

<body>

@if(env('APP_SOZIALMEDINANZEIGE')=="ja")
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
      <h1 class="text-light"><a href="{{env('APP_URL')}}"><span>{{ str_replace('_' , ' ' , env('VEREIN_DOMAIN')) }}</span></a></h1>
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
              @if($abteilungMenus->count()>1)
              <li class="drop-down {{ Request::is('deep') ? 'active' : '' }}"><a href="/#about">{{env('MENUE_ABTEILUNG')}}</a>
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
                              <a href="/{{ env('MENUE_ABTEILUNG') }}/{{ str_replace(' ', '_', $sportSectionMenu->abteilung) }}">
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
                                              <a href="/{{ env('MENUE_ABTEILUNG') }}/{{ str_replace(' ', '_', $sportSectionMenu->abteilung) }}">
                                                  {{ $sportSectionMenu->abteilung }}
                                              </a>
                                          </li>
                                          <!-- End -->
                                  @endif
                                          <li>
                                              <a href="/{{ env('MENUE_ABTEILUNG') }}/{{ str_replace(' ', '_', $sportTeamMenu->abteilung) }}">
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
              @endif
              @php
                  // ToDo: Active im Menu funktioniert noch nicht
              @endphp

              <li class="{{ Request::is('/#services') ? 'active' : '' }}"><a href="/Termine">Termine</a></li>
              <li class="{{ Request::is('/#services') ? 'active' : '' }}"><a href="/Berichte">Berichte</a></li>
              <li class="{{ Request::is('/#team') ? 'active' : '' }}"><a href="/#team">{{$sportSectionTeamNameMenu}}</a></li>

              <!-- <li><a href="#portfolio">Portfolio</a></li> -->

              <li class="{{ Request::is('/#contact') ? 'active' : '' }}"><a href="/#contact">Kontakt</a></li>
              @php
                  $instructionMenus = DB::table('instructions')
                    ->where('beschreibung' , '<>' , '')
                    ->where('hauptmenu' , 1)
                    ->where('visible' , 1)
                    ->orderby('ueberschrift')
                    ->get();
                   $countinstructionMenu=$instructionMenus->count();
              @endphp
              @if($countinstructionMenu>0)
                  <li class="drop-down"><a href="">Informationen</a>
                      <ul>
                          <li class="{{ Request::is('/anfahrt') ? 'active' : '' }}"><a href="/Anfahrt">Anfahrt</a></li>
                          @foreach($instructionMenus as $instructionMenu)
                              <li class="{{ Request::is('/anfahrt') ? 'active' : '' }}"><a href="/Information/{{ $instructionMenu->ueberschrift }}">{{ $instructionMenu->ueberschrift }}</a></li>
                          @endforeach
                      </ul>
                  </li>
              @else
                  <li class="{{ Request::is('/anfahrt') ? 'active' : '' }}"><a href="/Anfahrt">Anfahrt</a></li>
              @endif
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
              <h3>{{ str_replace('_', ' ', env('VEREIN_NAME')) }}</h3>
              <?php
              /*
                <p class="pb-3"><em>Qui repudiandae et eum dolores alias sed ea. Qui suscipit veniam excepturi quod.</em></p>
                // QUESTION: Warum em
              */
              ?>
              <p>
                  {{ str_replace('_', ' ', env('VEREIN_NAME')) }}<br>
                  {{ str_replace('_', ' ', env('VEREIN_STRASSE')) }}<br>
                  {{ str_replace('_', ' ', env('VEREIN_PLZ')) }} {{ str_replace('_', ' ', env('VEREIN_ORT')) }}<br>
                  @if(env('VEREIN_TELEFON')<>"")
                    <i class="icofont-telephone"></i>{{ str_replace('_' , ' ' , env('VEREIN_TELEFON')) }}<br>
                  @endif
                  @if(env('VEREIN_FAX')<>"")
                    <i class="icofont-fax"></i>{{ str_replace('_' , ' ' , env('VEREIN_FAX')) }}<br>
                  @endif
                    <i class="icofont-email"></i>
                    <a href="mailto:{{ str_replace('_' , ' ' , env('VEREIN_EMAIL')) }}">{{ str_replace('_' , ' ' , env('VEREIN_EMAIL')) }}</a>
              </p>

              @if(env('APP_SOZIALMEDINANZEIGE')=="ja")
                  <div class="social-links mt-3">
                      @if(env('APP_SOZIAL_FACEBOOK'))!='')
                      <a href="{{ str_replace('_' , ' ' , env('APP_SOZIAL_FACEBOOK')) }}" class="facebook" target="_blank"><i class="bx bxl-facebook"></i></a>
                      @endif
                      @if(env('APP_SOZIAL_TWITTER'))!='')
                      <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
                      @endif
                      @if(env('APP_SOZIAL_INSTEGRAMM'))!='')
                      <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
                      @endif
                      @if(env('APP_SOZIAL_SKYP'))!='')
                      <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
                      @endif
                      @if(env('APP_SOZIAL_LINKEDIN'))!='')
                      <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
                      @endif
                  </div>
              @endif

          </div>
        </div>

        <!--<div class="col-lg-2 col-md-6 footer-links" data-aos="fade-up" data-aos-delay="150"> -->
        <div class="col-lg-4 col-md-6 footer-newsletter" data-aos="fade-up" data-aos-delay="100">
          @include('textimport.footer')
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
            @php
                /* ToD@o: Netsletter
                <h4>Dein Newsletter</h4>
                <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
                <form action="" method="post">
                  <input type="email" name="email"><input type="submit" value="Subscribe">
                </form>
                <br>
                */
            @endphp
            @if($footerDocuments->count()>0)
                <br>
                <h4>Dokumente</h4>
                <ul>
                    @foreach($footerDocuments as $footerDocument)
                        <li><a href="/storage/dokumente/{{$footerDocument->dokumentenFile}}" target="_blank"><i class="bx bxs-note"></i>{{$footerDocument->dokumentenName}}</a></li>
                    @endforeach
                </ul>
            @endif

        @php
            $abteilungDomains  = DB::table('sport_sections')
            ->where('status' , '>' , '1')
            ->where('domain' , '!=' , '')
            ->orderby('abteilung')
            ->get();
            $count=$abteilungDomains->count();
        @endphp
        @if($count>0)
            <br>
            <h4>Webseiten {{env('MENUE_ABTEILUNG')}}</h4>
            <ul>
                @foreach($abteilungDomains as $abteilungDomain)
                    <li><a href="http://{{$abteilungDomain->domain}}" target="_blank" class="bx bx-link-external">{{$abteilungDomain->abteilung}}</a></li>
                @endforeach
            </ul>
        @endif

            <br>
            <h4>Intern</h4>
            <ul>
                <li><a href="{{ route('login') }}"><i class="bx bx-log-in"></i>Login</a></li>
                <?php // ToDo: Regestrieren noch nicht fertig ?>
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
      &copy; Copyright <strong><span>{{ str_replace('_', ' ', env('VEREIN_NAME')) }}</span></strong><br>
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

  @livewireScripts

</body>

    <!-- BotMan WebDrive -->
    @if(env('VEREIN_CHATBOT')=='ja')
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
