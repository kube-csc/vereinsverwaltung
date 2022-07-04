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
            {{ str_replace('_', ' ', env('VEREIN_NAME')) }}<br>
            {{ str_replace('_', ' ', env('VEREIN_STRASSE')) }}<br>
            {{ str_replace('_', ' ', env('Verein_PLZ')) }} {{ str_replace('_', ' ', env('VEREIN_ORT')) }}
          </p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="info-box  mb-4">
          <i class="bx bx-envelope"></i>
          <h3>Email</h3>
          <p><a href="mailto:{{ str_replace('_', ' ', env('VEREIN_EMAIL')) }}">{{ str_replace('_', ' ', env('VEREIN_EMAIL')) }}</a></p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="info-box  mb-4">
          <i class="bx bx-phone-call"></i>
          <h3>Telefon</h3>
          <p>
            @if(env('VEREIN_TELEFON')<>"")
            <strong>Tel: </strong>{{ str_replace('_', ' ', env('VEREIN_TELEFON')) }}<br>
            @endif
            @if(env('VEREIN_FAX')<>"")
            <strong>Fax: </strong>{{ str_replace('_', ' ', env('VEREIN_FAX')) }}<br>
            @endif
          </p>
        </div>
      </div>

    </div>

    @if(env('VEREIN_MAP')=="ja")
    <div class="row" data-aos="fade-up" data-aos-delay="350">

      <div class="col-lg-6 ">
        @include('textimport.map')
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
    @endif

  </div>
 </section><!-- End Contact Section -->
