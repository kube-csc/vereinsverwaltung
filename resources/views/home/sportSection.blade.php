<?php
//$abgeschnitten=0;
$abteilung_event_link ='';

$textlaengeabteilung=550;
$textlaenge=$abteilungsCount/2*($textlaengeabteilung+50);
if ($textlaenge<1000)
{
  $textlaenge=1000;
}

$i=0;
foreach ( $abteilungHomes as $abteilungHome)
  {
    $ausgabetext='';
    ++$i;
    if ($i==$abteilungHomesCount)
    {
     if (isset($abteilungHome->event_id)) {
      $event = DB::table('events')->find($abteilungHome->event_id);
      $ausgabetext=$event->beschreibung ;
     }
      $abgeschnitten=0;
      textmax($ausgabetext,$textlaenge,$abgeschnitten);
      ?>
      <!-- ======= About Section ======= -->
      <section id="about" class="about">
        <div class="container">

          <div class="row no-gutters">
            <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
              <div class="content">
                <!-- ======= Facebook======= -->
                <center>
                <div class="fb-like" data-href="http://www.<?php echo "$domain"?>" data-send="true" data-layout="box_count" data-width="183" data-show-faces="true" data-font="arial"></div>
                </center>
                <h3>{{ $abteilungHome->abteilung }}</h3>
                {!! $ausgabetext !!}
                @php
                // todo Abgeschnittener Text Progrmmieren
                if ($abgeschnitten==1)
                {
                  ?>
                  <a href="Abteilung/{{ $abteilungHome->id }}" class="about-btn">mehr<i class="bx bx-chevron-right"></i></a>
                  <?php
                }
               @endphp
             </div>
            </div>
        @php
          }
        }
       @endphp

<div class="col-xl-7 d-flex align-items-stretch">
  <div class="icon-boxes d-flex flex-column justify-content-center">
        <?php
        $i=0;
        foreach ( $abteilungs as $abteilung)
          {
            ++$i;
            if ($i==1)
            {
              ?>
              <div class="row">
              <?php
            }
            if ($abteilung->event_id>0)
            {
              $event = DB::table('events')->find($abteilung->event_id);
              $ausgabetext=$event->beschreibung ;
              $sollang=500;
              $abgeschnitten=0;
              textmax($ausgabetext,$sollang,$abgeschnitten);
            }
          ?>
                  <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="100">
                    <i class="bx bx-receipt"></i>
                    <h4>{{ $abteilung->abteilung }}</h4>
                    <?php
                    if ($abteilung->event_id>0)
                    {
                      ?>
                    <p>
                       {!! $ausgabetext !!}
                      <?php
                      // TODO: Abgeschnittener Text Progrmmieren
                      if ($abgeschnitten==1)
                      {
                        ?>
                        <div class="read-more">
                          <a href="Abteilung/{{ $abteilung->id }}"><i class="icofont-arrow-right"></i> weiter</a>
                        </div>  <?php// TODO: Mehr Programmieren CSS Stile ?>
                        <?php
                      }
                     ?>
                   </p>
                    <?php
                    }
                    ?>
                  </div>
                  <?php
                  if ($i==2)
                  {
                    $i=0;
                    ?>
            </div>
                    <?php
                  }

        }
        ?>
      </div><!-- End .content-->
    </div>
        </div>
        </div>
      </section><!-- End About Section -->

  @php
  // TODO: Funktion anderes Integrieren
  function textmax(&$beschreibung,$sollang,&$abgeschnitten)
  {
   $abgeschnitten=0;
   $laenge=strlen($beschreibung);
   if ($laenge>$sollang)
    {
      $beschreibung=substr($beschreibung,0,$sollang);
      $beschreibung=$beschreibung."...";  // TODO:  Punkte werden nicht angefügt
      $abgeschnitten=1;
    }
  }
  @endphp
