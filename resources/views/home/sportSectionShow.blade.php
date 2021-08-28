<?php
// TODO: Config Daten anderes Einbinden
$verein= "Kanuten Emscher-Lippe e.V.";
$vereinstrasse = "Zu den Sportstätten 5";
$vereinsplz = "D-45711";
$vereinsort = "Datteln";
$vereintelefon = "(02363) 8420";
$vereinfax="";
$vereinemail = "kel@kel-datteln.de";
$vereineintagngsort = "Recklinghausen";
$vereinvrnummer = "VR 0627";
$keywords = "Kanuten Emscher-Lippe, Datteln, KEL, Kanuverein, Sport, Kanuwandersport, Kanurennsport, Kanu, Kajak, Paddeln, Drachenboot, Drachenbootregatta,
               Wassersport, Regatta, SUP, Outrigger";
$description = "Wir sind ein Kanuverein mit Jugend-, Wander-, Rennsport-, SUP-, Outrigger- und Drachenbootabteilung in Datteln NRW am Dortmund-Ems-Kanal.";

$slogen="Wir sind ein Kanuverein in Datteln am Dortmund Ems Kanal.";
$canonical="https://www.kel-datteln.de/neu";
$domain="kel-datteln.de";

$sozialmediaanzeigen='n';
?>

@extends('layouts.headFrontend')

@section('title' ,'Starteite')

@section('content')

    <main id="main">



    <!-- ======= About Section ======= -->
        <section id="about" class="about">
            <div class="container">
                <div class="row no-gutters">
            <?php
            foreach ( $sportSectionNames as $sportSectionName)
             {
             ?>
                    <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
                        <div class="content">
                            <!-- ======= Facebook======= -->
                            <center>
                                <div class="fb-like" data-href="http://www.<?php echo "$domain"?>" data-send="true" data-layout="box_count" data-width="183" data-show-faces="true" data-font="arial"></div>
                            </center>

                            <h3>{{ $sportSectionName->abteilung }}</h3>
                            @if($sportSectionName->event_id)
                              {!! $sportSectionName->event->beschreibung !!}
                            @endif

                        </div>
                    </div>
             @php
             }
             @endphp
                    <div class="col-xl-7 d-flex align-items-stretch">
                        <div class="icon-boxes d-flex flex-column justify-content-center">
                        <?php
                        $i=0;
                        foreach ( $sportTeamNames as $abteilung)
                        {
                            ++$i;
                            if ($i==1)
                            {
                            ?>
                            <div class="row">
                                <?php
                                }
                                $abgeschnitten=0;
                                if ($abteilung->event_id>0)
                                {
                                    $ausgabetext=$abteilung->event->beschreibung;
                                    $sollang=500;
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
                                       {!! $ausgabetext !!}
                                       @if ($abgeschnitten==1)
                                         <div class="read-more">
                                           <a href="/Abteilung/detail/{{ str_replace(' ', '_', $abteilung->abteilung) }}"><i class="icofont-arrow-right"></i> weiter</a>
                                         </div>
                                       @endif
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


    </main><!-- End #main -->
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

@endsection
