<?php
// TODO: anderes Einbinden
  $version = "1.0.0";
  $installdate = "01.10.2020";
  $verein= "Kanuten Emscher-Lippe e.V.";
  $vereinstrasse = "Zu den Sportstätten 5";
  $vereinsplz = "D-45711";
  $vereinsort = "Datteln";
  $vereintelefon = "(02363) 8420";
  $vereinfax="";
  $vereinemail = "kel@kel-datteln.de";
  $vereineintagngsort = "Recklinghausen";
  $vereinvrnummer = "VR 0627";
  $dateformat = "d.m.Y";
  $sponsoren = "Partner";
  $abmeldezeit = "3600";
  $keywords = "Kanuten Emscher-Lippe, Datteln, KEL, Kanuverein, Sport, Kanuwandersport, Kanurennsport, Kanu, Kajak, Paddeln, Drachenboot, Drachenbootregatta,
               Wassersport, Regatta, SUP, Outrigger";
  $description = "Wir sind ein Kanuverein mit Jugend-, Wander-, Rennsport-, SUP-, Outrigger- und Drachenbootabteilung in Datteln NRW am Dortmund-Ems-Kanal.";

  $slogen="Wir sind ein Kanuverein in Datteln am Dortmund Ems Kanal.";
  $canonical="https://www.kel-datteln.de/neu";
  $domain="kel-datteln.de";
?>


@include('_partials.option')  <?php // TODO:  KEL-Datteln muss noch als Variabel umgesetzt werden ?>

<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>@yield('title', 'KEL-Datteln' )</title> <?php // TODO:  KEL-Datteln muss noch als Variabel umgesetzt werden?>


  <meta content="" name="descriptison">
  <meta content="" name="keywordsanlegen">

  <?php /*
  <!-- Favicons -->
  <link href="asset/img/favicon.png" rel="icon">
  <link href="asset/img/apple-touch-icon.png" rel="apple-touch-icon">
*/ ?>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <?php /*  <link href="{{ asset('asset/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"> */ ?>
  <link href="{{ asset('asset/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/vendor/icofont/icofont.min.css') }}" rel="stylesheet">
  <?php /* <link href="{{ asset('asset/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet"> */ ?>
  <link href="{{ asset('asset/vendor/venobox/venobox.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/vendor/owl.carousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/vendor/aos/aos.css" rel="stylesheet') }}">

  <!-- Template Main CSS File -->
  <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">

  <!-- Laravell app.style.ss-->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  <?php // TODO: boxicons über webpack einbinden ?>
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Squadfree - v2.2.0
  * Template URL: https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>


<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top header-transparent">
    <div class="container d-flex align-items-center">

      <div class="logo mr-auto">
        <h1 class="text-light"><a href="home"><span>Squadfree</span></a></h1>
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="index.html"><img src="asset/img/logo.png" alt="" class="img-fluid"></a>-->
      </div>

      <nav class="nav-menu d-none d-lg-block">
        <ul>
          <li class="{{ Request::is('home') ? 'active' : '' }}"><a href="/">Home</a></li>
          <li class="{{ Request::is('/#about') ? 'active' : '' }}"><a href="#about">About Us</a></li>
          <li class="{{ Request::is('/#services') ? 'active' : '' }}"><a href="#services">Services</a></li>
          <li class="{{ Request::is('/#portfolio') ? 'active' : '' }}"><a href="#portfolio">Portfolio</a></li>
          <li class="{{ Request::is('/#team') ? 'active' : '' }}"><a href="#team">Team</a></li>
          <li class="drop-down {{ Request::is('deep') ? 'active' : '' }}"><a href="">Drop Down</a>
            <ul>
              <li><a href="#">Drop Down 1</a></li>
              <li class="drop-down"><a href="#">Drop Down 2</a>
                <ul>
                  <li><a href="#">Deep Drop Down 1</a></li>
                  <li><a href="#">Deep Drop Down 2</a></li>
                  <li><a href="#">Deep Drop Down 3</a></li>
                  <li><a href="#">Deep Drop Down 4</a></li>
                  <li><a href="#">Deep Drop Down 5</a></li>
                </ul>
              </li>
              <li><a href="#">Drop Down 3</a></li>
              <li><a href="#">Drop Down 4</a></li>
              <li><a href="#">Drop Down 5</a></li>
            </ul>
          </li>
          <li class="{{ Request::is('/#contact') ? 'active' : '' }}"><a href="#contact">Contact Us</a></li>

        </ul>
      </nav><!-- .nav-menu -->

    </div>
  </header><!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero">
    <div class="hero-container" data-aos="fade-up">
      <h1>Welcome to Squad</h1>
      <h2>We are team of talanted designers making websites with Bootstrap</h2>
          <a href="#about" class="btn-get-started scrollto"><i class="bx bx-chevrons-down"></i></a>
    </div>
  </section><!-- End Hero -->

@yield('content')

<!-- ======= Footer ======= -->
<footer id="footer">
  <div class="footer-top">
    <div class="container">
      <div class="row">

        <div class="col-lg-4 col-md-6">
          <div class="footer-info" data-aos="fade-up" data-aos-delay="50">
            <h3><?php echo $verein; ?></h3>
            <h4>Vorstandsadressen</h4>
            <?php /*
            <p class="pb-3"><em>Qui repudiandae et eum dolores alias sed ea. Qui suscipit veniam excepturi quod.</em></p> // QUESTION: : Warum em
            */ ?>
            <p>
              <?php
              echo "$verein <br>";
              echo "$vereinstrasse <br>";
              echo "$vereinsplz $vereinsort <br>";
              if ($vereintelefon<>'')
              {
                ?>
               <i class="icofont-telephone"></i><?php echo $vereintelefon ; ?>
                 <br>
              <?php
              }
              if ($vereinfax<>'')
              {
                ?>
                <i class="icofont-fax"></i><?php echo $vereinfax ; ?>
             <?php
              }
              ?>
              <i class="icofont-email"></i><a href="mailto:<?php echo $vereinemail ; ?>"><?php echo $vereinemail ; ?></a>
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
          <div class="col-lg-4 col-md-6 footer-newsletter" data-aos="fade-up" data-aos-delay="350">
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
            <li><a href="http://www.kel-datteln.de/index.php?sprung=regatta/eventausgabe.php&amp;menu=regatta&amp;terminsammler=1" target="_blank">Rennsportregatta</a></li>
            <li><a href="http://www.day-of-dragons.de" target="_blank">Day of Dragons</a></li>
            <li><a href="http://sup.kel-datteln.de"    target="_blank">SUP Kurse</a></li>
            <li><a href="http://oc.kel-datteln.de"     target="_blank">Outrigger für Vereinsmitglieder Buchen</a></li>
          </ul>
          <?php /* NOTE: mit fictogramm
           <li><i class="bx bx-chevron-right"></i> <a href="#" target="_blank">SUP Kurse</a></li>
          */ ?>
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
        <div class="col-lg-4 col-md-6 footer-newsletter" data-aos="fade-up" data-aos-delay="350">
        <?php /* TODO: Netsletter
          <h4>Dein Newsletter</h4>
          <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
          <form action="" method="post">
            <input type="email" name="email"><input type="submit" value="Subscribe">
          </form>
          <br>
          */ ?>
          <h4>Links</h4>
          <ul>
           <li><a href="anfahrt.php"><i class="bx bx-map"></i>Anfahrt</a></li> <?php // TODO: Akiv Menu bearbeiten ?>
          </ul>
          <br>
          <h4>Dokumente</h4>
          <ul>
           <li><a href="https://www.kel-datteln.de/download/vereinssatzung2010.pdf" target="_blank"><i class="bx bxs-note"></i>Vereinssatzung 2010</a></li> <?php // TODO: Akiv Menu bearbeiten ?>
           <li><a href="https://www.kel-datteln.de/download/Sportkleidung_2019.pdf" target="_blank"><i class="bx bxs-note"></i>Vereinskleidung</a></li>
          </ul>
          <br>
          <h4>Intern</h4>
          <ul>
            <?php /* TODO: Anmeldung alte Seite
               <li><a href="http://www.kel-datteln.de/passwort/password.php" target="_blank"><i class="bx bx-log-in"></i>Login (alte Seite)</a></li> <?php // TODO: Akiv Menu bearbeiten ?>
            */ ?>
           <li><a href="http://<?php echo $domain ?>/passwort/login.php"><i class="bx bx-log-in"></i>Admin Login</a></li> <?php // TODO: Akiv Menu bearbeiten ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="copyright">
      &copy; Copyright <strong><span><?php echo $verein ?></span></strong><br>
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
      <a href="/datenschutzerklaerung">Datenschutzerklärung</a> |
      <a href="/impressum">Impressum</a>
    </div>
  </div>
</footer><!-- End Footer -->

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('asset/vendor/jquery/jquery.min.js') }}"></script>
  <?php /* TODO:
  <script src="{{ asset('asset/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  */ ?>
  <script src="{{ asset('asset/vendor/jquery.easing/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('asset/vendor/waypoints/jquery.waypoints.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/counterup/counterup.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/venobox/venobox.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/owl.carousel/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('asset/vendor/aos/aos.js') }}"></script>


  <!-- Template Main JS File -->
  <script src="{{ asset('asset/js/main.js') }}"></script>


  </body>

  </html>
