<?php
$textlaengeabteilung=300;
$textlaenge=$abteilungsCount/2*($textlaengeabteilung+100);
if ($textlaenge<1000)
 {
  $textlaenge=1000;
 }

$i=0;
foreach ( $abteilungHomes as $abteilungHome)
  {
    $abgeschnitten=0;
    $ausgabetext='';
    ++$i;
    if ($i==$abteilungHomesCount)
    {
     if (isset($abteilungHome->event_id))
      {
       $ausgabetext=$abteilungHome->event->beschreibung;
       textmax($ausgabetext,$textlaenge,$abgeschnitten);
      }
      ?>
      <!-- ======= About Section ======= -->
      <section id="about" class="about">
        <div class="container">
          <div class="row no-gutters">
            <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
              <div class="content">
              @if(env('APP_SOZIALMEDINANZEIGE')=='ja')
                  <!-- ======= Facebook======= -->
                  <!-- ToDo: Facebook funktioniert nicht -->
                      <center>
                          <div class="fb-like" data-href="http://www.{{ str_replace('_' , ' ' , env('VEREIN_DOMAIN')) }} data-send="true" data-layout="box_count" data-width="183" data-show-faces="true" data-font="arial"></div>
                      </center>
                @endif
                <h3>{{ $abteilungHome->abteilung }}</h3>
                {!! $ausgabetext !!}
                @if ($abgeschnitten==1)
                    <div class="read-more">
                      <a href="/{{env('MENUE_ABTEILUNG')}}/{{ str_replace(' ', '_', $abteilungHome->abteilung) }}" class="about-btn">
                          mehr<i class="bx bx-chevron-right"></i>
                      </a>
                    </div>
                @endif
              </div>
            </div>
           @php
           }
          }
          @endphp

          @if($abteilungHome->domain != $_SERVER["HTTP_HOST"]  or $abteilungHome->status == 1)
           <div class="col-xl-7 d-flex align-items-stretch">
            <div class="icon-boxes d-flex flex-column justify-content-center">
            <?php
            $i=0;
            $time=-100;
            foreach ($abteilungs as $abteilung)
              {
                ++$i;
                $time=$time+100;
                if ($i==1)
                {
                  ?>
                  <div class="row">
                  <?php
                }
                $abgeschnitten=0;
                if ($abteilung->event_id>0)
                {
                  if($abteilung->event->nachtermin == ''){
                      $ausgabetext=$abteilung->event->beschreibung;
                   }
                  else{
                      $ausgabetext=$abteilung->event->nachtermin;
                   }
                  textmax($ausgabetext,$textlaengeabteilung,$abgeschnitten);
                 }
              ?>
              @if($i==1 )
               <div class="col-md-6 icon-box" data-aos="fade-up">
              @else
               <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="{{ $time }}">
              @endif
                <a href="/{{env('MENUE_ABTEILUNG')}}/{{ str_replace(' ' , '_' , $abteilung->abteilung) }}">
                  <h4>{{ $abteilung->abteilung }}</h4>
                </a>
                <?php
                if ($abteilung->event_id>0)
                 {
                ?>
                  {!! $ausgabetext !!}
                  @php
                   $sportTeams = DB::table('sport_sections')->where('sportSection_id' , $abteilung->id)->get();
                   $first=0;
                  @endphp

                  @foreach($sportTeams as $sportTeam)
                    @if ($first==0)
                        <br>
                          <h5><b>Mit den Mannschaften:</b></h5>
                       <ul>
                        @php
                         $first=1;
                        @endphp
                    @endif
                     <li>
                    {{ $sportTeam->abteilung }}
                     </li>
                  @endforeach
                   @if ($first==1)
                      </ul>
                   @endif
                  @if ($abgeschnitten==1 | $first==1)
                    <div class="read-more">
                      <a href="/{{env('MENUE_ABTEILUNG')}}/{{ str_replace(' ', '_', $abteilung->abteilung) }}"><i class="icofont-arrow-right"></i></a>
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
          @endif
      </section><!-- End About Section -->
