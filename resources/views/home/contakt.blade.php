<!-- ======= Contact Section ======= -->
<section id="contact" class="contact section-bg">
  <div class="container">

    <div class="section-title" data-aos="fade-in" data-aos-delay="250">
      <h2>Kontakt</h2>
      <?php
        /* TODO:  Beschreibungstext Kontakte
      <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
      */ ?>
    </div>

    <div class="row" data-aos="fade-up" data-aos-delay="300">
      <div class="col-lg-6">
        <div class="info-box mb-4">
          <i class="bx bx-map"></i>
          <h3>Adresse</h3>
          <p>
            {{ str_replace('_', ' ', env('Verein_Name')) }}<br>
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
            <strong>Tel: </strong>{{ str_replace('_', ' ', env('Verein_Telefon')) }}<br>
            @endif
            @if(env('Verein_Fax')<>"")
            <strong>Fax: </strong>{{ str_replace('_', ' ', env('Verein_Fax')) }}<br>
            @endif
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
