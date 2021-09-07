@extends('layouts.headFrontend')

@section('title' ,'Starteite')

@section('content')

    <main id="main">

    <!-- ======= About Section ======= -->
        <section id="about" class="about">
            <div class="container">
                <div class="row no-gutters">

                @foreach ($sportSectionNames as $sportSectionName)
                    <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
                        <div class="content">
                            <!-- ======= Facebook======= -->
                            <center>
                                <div class="fb-like" data-href="http://www.{{ str_replace('_' , ' ' , env('Verein_Domain')) }} data-send="true" data-layout="box_count" data-width="183" data-show-faces="true" data-font="arial"></div>
                            </center>

                            <h3>{{ $sportSectionName->abteilung }}</h3>
                            @if($sportSectionName->event_id)
                              {!! $sportSectionName->event->beschreibung !!}
                            @endif

                        </div>
                    </div>
                @endforeach

                    <div class="col-xl-7 d-flex align-items-stretch">
                        <div class="icon-boxes d-flex flex-column justify-content-center">
                        <?php
                        $i=0;
                        foreach ($sportTeamNames as $abteilung)
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
                                    <a href="/Abteilung/detail/{{ str_replace(' ', '_', $abteilung->abteilung) }}">
                                        <i class="bx bx-receipt"></i>
                                    </a>
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

@endsection

@php
    function textmax(&$beschreibung,$sollang,&$abgeschnitten)
    {
     $abgeschnitten=0;
     $laenge=strlen($beschreibung);
     if ($laenge>$sollang)
      {
        $beschreibung=substr($beschreibung,0,$sollang);
        $beschreibung=$beschreibung."...";
        $abgeschnitten=1;
      }
    }
@endphp

