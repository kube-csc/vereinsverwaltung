@extends('layouts.headFrontend')

@section('title' ,'Starteite')

<?php
// TODO: Config Daten anderes Einbinden
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

@section('content')
  <main id="main">

    <!-- ======= About Section ======= -->
    <section id="about" class="about">
      <div class="container">

        <div class="row no-gutters">
          <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
            <div class="content">
              <h3>Voluptatem dignissimos provident quasi</h3>


              <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Duis aute irure dolor in reprehenderit
              </p>
              <a href="#" class="about-btn">About us <i class="bx bx-chevron-right"></i></a>
            </div>
          </div>
          <div class="col-xl-7 d-flex align-items-stretch">
            <div class="icon-boxes d-flex flex-column justify-content-center">
              <div class="row">
                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="100">
                  <i class="bx bx-receipt"></i>
                  <h4>Corporis voluptates sit</h4>
                  <p>Consequuntur sunt aut quasi enim aliquam quae harum pariatur laboris nisi ut aliquip</p>
                </div>
                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="200">
                  <i class="bx bx-cube-alt"></i>
                  <h4>Ullamco laboris nisi</h4>
                  <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt</p>
                </div>
                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="300">
                  <i class="bx bx-images"></i>
                  <h4>Labore consequatur</h4>
                  <p>Aut suscipit aut cum nemo deleniti aut omnis. Doloribus ut maiores omnis facere</p>
                </div>
                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="400">
                  <i class="bx bx-shield"></i>
                  <h4>Beatae veritatis</h4>
                  <p>Expedita veritatis consequuntur nihil tempore laudantium vitae denat pacta</p>
                </div>
              </div>
            </div><!-- End .content-->
          </div>
        </div>

      </div>
    </section><!-- End About Section -->

    <!-- ======= Team Section ======= -->
    <section id="team" class="team">
      <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
          <h2>Team</h2>
          <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
        </div>

        <div class="row">

          <div class="col-lg-4 col-md-6">
            <div class="member" data-aos="fade-up">
              <div class="pic"><img src="asset/img/team/team-1.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Walter White</h4>
                <span>Chief Executive Officer</span>
                <div class="social">
                  <a href=""><i class="icofont-twitter"></i></a>
                  <a href=""><i class="icofont-facebook"></i></a>
                  <a href=""><i class="icofont-instagram"></i></a>
                  <a href=""><i class="icofont-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="member" data-aos="fade-up" data-aos-delay="150">
              <div class="pic"><img src="asset/img/team/team-2.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Sarah Jhonson</h4>
                <span>Product Manager</span>
                <div class="social">
                  <a href=""><i class="icofont-twitter"></i></a>
                  <a href=""><i class="icofont-facebook"></i></a>
                  <a href=""><i class="icofont-instagram"></i></a>
                  <a href=""><i class="icofont-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="member" data-aos="fade-up" data-aos-delay="200">
              <div class="pic"><img src="asset/img/team/team-3.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>William Anderson</h4>
                <span>CTO</span>
                <div class="social">
                  <a href=""><i class="icofont-twitter"></i></a>
                  <a href=""><i class="icofont-facebook"></i></a>
                  <a href=""><i class="icofont-instagram"></i></a>
                  <a href=""><i class="icofont-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>
    </section><!-- End Team Section -->

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact section-bg">
      <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="250">
          <h2>Kontakt</h2>
          <?php /* TODO:  Beschreibungstext Kontakte
          <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
          */ ?>
        </div>

        <div class="row" data-aos="fade-up" data-aos-delay="300">
          <div class="col-lg-6">
            <div class="info-box mb-4">
              <i class="bx bx-map"></i>
              <h3>Adresse</h3>
              <p>
                <?php
                echo "$verein <br>";
                echo "$vereinstrasse <br>";
                echo "$vereinsplz $vereinsort <br>";
                ?>
              </p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="info-box  mb-4">
              <i class="bx bx-envelope"></i>
              <h3>Email</h3>
              <p><a href="mailto:<?php echo $vereinemail ; ?>"><?php echo $vereinemail ; ?></a></p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="info-box  mb-4">
              <i class="bx bx-phone-call"></i>
              <h3>Telefon</h3>
              <p>
                <?php
                if ($vereintelefon<>"")
                {
                echo "<strong>Tel:</strong> $vereintelefon <br>";
                }
                if ($vereinfax<>"")
                {
                echo "<strong>Fax:</strong> $vereinfax <br>";
                }
                ?>
              </p>
            </div>
          </div>

        </div>

        <div class="row" data-aos="fade-up" data-aos-delay="350">

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
    <?php /* TODO: Massagebox aktivieren
            <form action="forms/contact.php" method="post" role="form" class="php-email-form">
              <div class="form-row">
                <div class="col-md-6 form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" data-rule="minlen:4" data-msg="Please enter at least 4 chars" />
                  <div class="validate"></div>
                </div>
                <div class="col-md-6 form-group">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" data-rule="email" data-msg="Please enter a valid email" />
                  <div class="validate"></div>
                </div>
              </div>
              <div class="form-group">
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" data-rule="minlen:4" data-msg="Please enter at least 8 chars of subject" />
                <div class="validate"></div>
              </div>
              <div class="form-group">
                <textarea class="form-control" name="message" rows="5" data-rule="required" data-msg="Please write something for us" placeholder="Message"></textarea>
                <div class="validate"></div>
              </div>
              <div class="mb-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your message has been sent. Thank you!</div>
              </div>
              <div class="text-center"><button type="submit">Send Message</button></div>
            </form>
            */ ?>
          </div>

        </div>

      </div>
     </section><!-- End Contact Section -->

  </main><!-- End #main -->
@endsection
