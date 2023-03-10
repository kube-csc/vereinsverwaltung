    <!-- ======= About Section ======= -->
        <section id="about" class="about">
            <div class="container">
                <div class="row no-gutters">

                @foreach ($sportSectionNames as $sportSectionName)
                    <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
                        <div class="content">
                            @if(env('APP_SOZIALMEDINANZEIGE')=='ja')
                             <!-- ======= Facebook======= -->
                             <!-- ToDo: Facebook funktioniert nicht -->
                                 <center>
                               <div class="fb-like" data-href="http://www.{{ str_replace('_' , ' ' , env('VEREIN_DOMAIN')) }} data-send="true" data-layout="box_count" data-width="183" data-show-faces="true" data-font="arial"></div>
                             </center>
                            @endif
                            <h3>{{ $sportSectionName->abteilung }}</h3>
                            @if($sportSectionName->event_id)
                              <p>
                              {!! $sportSectionName->event->beschreibung !!}
                              </p>
                            @endif

                            @if($documents->count())
                              <div>
                                <b>Dokumente</b>
                                @foreach($documents as $document)
                                    <div>
                                        <a href="/storage/dokumente/{{$document->dokumentenFile}}" target="_blank"><i class="bx bxs-note"></i>{{$document->dokumentenName}}</a>
                                    </div>
                                @endforeach
                              </div>
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
                                $textlaengeabteilung=300;
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
                                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="100">
                                    <a href="/{{env('MENUE_ABTEILUNG')}}/{{ str_replace(' ', '_', $abteilung->abteilung) }}">
                                      <h4>{{ $abteilung->abteilung }}</h4>
                                    </a>
                                   <?php
                                   if ($abteilung->event_id>0)
                                    {
                                       ?>
                                       {!! $ausgabetext !!}
                                       @if ($abgeschnitten==1)
                                         <div class="read-more">
                                           <a href="/{{env('MENUE_ABTEILUNG')}}/{{ str_replace(' ', '_', $abteilung->abteilung) }}" class="icofont-arrow-right">mehr</a>
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



