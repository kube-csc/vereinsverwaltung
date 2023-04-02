@extends('layouts.frontend')

@section('about' ,'/impressum') <?php // ToDo: vor dem #About den Routenname hinzufügen verbessern?>
@section('title' ,'Impressum')

@section('content')
    <main id="main">
        <name id="about">
            <!-- ======= Breadcrumbs Section ======= -->
            <section class="breadcrumbs">
                <div class="container">

                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Impressum</h2>
                        <ol>
                            <li><a href="../index.php">Home</a></li>
                            <li>Impressum</li>
                        </ol>
                    </div>
                </div>
            </section><!-- End Breadcrumbs Section -->

            <?php // ToDo: Beschreibungen in Beschreibungen anpassen ?>
                <!-- ======= Anfahrt Section ======= -->
            <?php  /*<section class="inner-page">  */?>
            <section id="about" class="about">
                <div class="container">
                    <?php /* TODO:
        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
          <h2>Impressum</h2>
          <p>

          </p>
        </div>
  */ ?>
                    <div class="row" data-aos="fade-up" data-aos-delay="100">
                        <div class="col-lg-6">
                            <div class="info-box mb-4">
                                <i class="bx bx-building-house"></i>
                                <b>Adresse</b>
                                <p>
                                    @if (env('VEREIN_NAME')<>"")
                                        {{ str_replace('_', ' ', env('VEREIN_NAME')) }}
                                    @else
                                        {{'Hier steht die Vereinsanschrift. Bitte in der .Env die Daten pflegen'}}
                                    @endif
                                    <br>
                                    {{ str_replace('_', ' ', env('VEREIN_STRASSE')) }}<br>
                                    {{ str_replace('_', ' ', env('VEREIN_PLZ')) }} {{ str_replace('_', ' ', env('VEREIN_ORT')) }}
                                    <br><br>
                                    @if (env('VEREIN_EINTRAGUNGSORT')<>"")
                                        Eingetragen in das
                                        Vereinsregister: {{ str_replace('_', ' ', env('VEREIN_EINTRAGUNGSORT')) }}<br>
                                    @endif
                                    @if (env('VEREIN_VRNUMMER')<>"")
                                        VR-Nummer:  {{ str_replace('_', ' ', env('VEREIN_VRNUMMER')) }}
                                    @endif
                                    <br><br>
                                    @if(env('VEREIN_TELEFON')<>"")
                                        Tel: {{ str_replace('_', ' ', env('VEREIN_TELEFON')) }}<br>
                                    @endif
                                    @if(env('VEREIN_FAX')<>"")
                                        Fax: {{ str_replace('_', ' ', env('VEREIN_FAX')) }}<br>
                                    @endif
                                    @if(env('VEREIN_EMAIL')<>"")
                                        <i class="icofont-envelope"></i> <a href="mailto:{{ str_replace('_', ' ', env('VEREIN_EMAIL')) }}">{{ str_replace('_', ' ', env('VEREIN_EMAIL')) }}</a>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="info-box  mb-4">
                                <i class="bx bx-home"></i>
                                <b>Dieser vertreten durch:</b>
                                <p>
                                    @if(env('VEREIN_HP_VERTRETEN')!='')
                                      {{ str_replace('_', ' ', env('VEREIN_HP_VERTRETEN')) }}<br>
                                    @endif
                                    @if(env('VEREIN_HP_VERTRETESTRASSE')!='')
                                      {{ str_replace('_', ' ', env('VEREIN_HP_VERTRETESTRASSE')) }}<br>
                                    @endif
                                    @if(env('VEREIN_HP_VERTRETEPLZ')!='')
                                      {{ str_replace('_', ' ', env('VEREIN_HP_VERTRETEPLZ')) }}
                                    @endif
                                    @if(env('VEREIN_HP_VERTRETEORT')!='')
                                      {{ str_replace('_', ' ', env('VEREIN_HP_VERTRETEORT')) }}<br>
                                    @endif
                                    @if(env('VEREIN_HP_VERTRETETELEFON')!='')
                                      <i class="icofont-phone"></i> {{ str_replace('_', ' ', env('VEREIN_HP_VERTRETETELEFON')) }}<br>
                                    @endif
                                    @if(env('VEREIN_HP_VERTRETEMAIL')!='')
                                      <i class="icofont-envelope"></i> <a href="mailto:{{ str_replace('_', ' ', env('VEREIN_HP_VERTRETEMAIL')) }}">{{ str_replace('_', ' ', env('VEREIN_HP_VERTRETEMAIL')) }}</a><br>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if(env('VEREIN_HP_TECH_VERTRETE')!='' | env('VEREIN_HP_TECH_VERTRETEMAIL')!='')
                        <div class="col-lg-6">
                            <div class="info-box  mb-4">
                                <i class="bx bx-home"></i>
                                <b>Für Technik dieser Seiten ist verantwortlich als Webmaster:</b>
                                <p>
                                    @if(env('VEREIN_HP_TECH_VERTRETE')!='')
                                      {{ str_replace('_', ' ', env('VEREIN_HP_TECH_VERTRETE')) }}<br>
                                    @endif
                                    @if(env('VEREIN_HP_TECH_VERTRETESTRASSE')!='')
                                      {{ str_replace('_', ' ', env('VEREIN_HP_TECH_VERTRETESTRASSE')) }}<br>
                                    @endif
                                    @if(env('VEREIN_HP_TECH_VERTRETEPLZ')!='')
                                      {{ str_replace('_', ' ', env('VEREIN_HP_TECH_VERTRETEPLZ')) }}
                                    @endif
                                    @if(env('VEREIN_HP_TECH_VERTRETEORT')!='')
                                      {{ str_replace('_', ' ', env('VEREIN_HP_TECH_VERTRETEORT')) }}<br>
                                    @endif
                                    @if(env('VEREIN_HP_TECH_VERTRETETELEFON')!='')
                                      <i class="icofont-phone"></i> {{ str_replace('_', ' ', env('VEREIN_HP_TECH_VERTRETETELEFON')) }}<br>
                                    @endif
                                    @if(env('VEREIN_HP_TECH_VERTRETEMAIL')!='')
                                      <i class="icofont-envelope"></i>  <a href="mailto:{{ str_replace('_', ' ', env('VEREIN_HP_TECH_VERTRETEMAIL')) }}">{{ str_replace('_', ' ', env('VEREIN_HP_TECH_VERTRETEMAIL')) }}</a><br>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @ENDIF
                    </div>

                    <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                        @include('textimport.recht')
                    </div>
                </div>
            </section><!-- End Breadcrumbs Section -->
    </main><!-- End #main -->
@endsection
