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
                                    @if (env('Verein_Name')<>"")
                                        {{ str_replace('_', ' ', env('Verein_Name')) }}
                                    @else
                                        {{'Hier steht die Vereinsanschrift. Bitte in der .Env die Daten pflegen'}}
                                    @endif
                                    <br>
                                    {{ str_replace('_', ' ', env('Verein_Strasse')) }}<br>
                                    {{ str_replace('_', ' ', env('Verein_PLZ')) }} {{ str_replace('_', ' ', env('Verein_Ort')) }}
                                    <br><br>
                                    @if (env('Verein_EeintagngsOrt')<>"")
                                        Eingetragen in das
                                        Vereinsregister: {{ str_replace('_', ' ', env('Verein_EeintagngsOrt')) }}<br>
                                    @endif
                                    @if (env('Verein_VRNummer')<>"")
                                        VR-Nummer:  {{ str_replace('_', ' ', env('Verein_VRNummer')) }}
                                    @endif
                                    <br><br>
                                    @if(env('Verein_Telefon')<>"")
                                        Tel: {{ str_replace('_', ' ', env('Verein_Telefon')) }}<br>
                                    @endif
                                    @if(env('Verein_Fax')<>"")
                                        Fax: {{ str_replace('_', ' ', env('Verein_Fax')) }}<br>
                                    @endif
                                    <a href="mailto:{{ str_replace('_', ' ', env('Verein_Email')) }}">{{ str_replace('_', ' ', env('Verein_Email')) }}</a>
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="info-box  mb-4">
                                <i class="bx bx-home"></i>
                                <b>Dieser vertreten durch:</b>
                                <p>
                                    @if(env('Verein_HPVertreten')!='')
                                      {{ str_replace('_', ' ', env('Verein_HPVertreten')) }}<br>
                                    @endif
                                    @if(env('Verein_HPVertretenStrasse')!='')
                                      {{ str_replace('_', ' ', env('Verein_HPVertretenStrasse')) }}<br>
                                    @endif
                                    @if(env('Verein_HPVertretenPLZ')!='')
                                      {{ str_replace('_', ' ', env('Verein_HPVertretenPLZ')) }}
                                    @endif
                                    @if(env('Verein_HPVertretenOrt')!='')
                                      {{ str_replace('_', ' ', env('Verein_HPVertretenOrt')) }}<br>
                                    @endif
                                    @if(env('Verein_HPVertretenTelefon')!='')
                                      <i class="icofont-phone"></i> {{ str_replace('_', ' ', env('Verein_HPVertretenTelefon')) }}<br>
                                    @endif
                                    @if(env('Verein_HPVertretenMail')!='')
                                      <i class="icofont-envelope"></i> <a href="mailto:{{ str_replace('_', ' ', env('Verein_HPVertretenMail')) }}">{{ str_replace('_', ' ', env('Verein_HPVertretenMail')) }}</a><br>
                                    @endif
                                    <br>
                                    Für weitere Mitglieder des Vorstands <a href="index.php#team">hier</a> klicken.
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="info-box  mb-4">
                                <i class="bx bx-home"></i>
                                <b>Für den Inhalt dieser Seiten ist verantwortlich als Webmaster:</b>
                                <p>
                                    @if(env('Verein_HP_Tech_Vertreten')!='')
                                      {{ str_replace('_', ' ', env('Verein_HP_Tech_Vertreten')) }}<br>
                                    @endif
                                    @if(env('Verein_HP_Tech_VertretenStrasse')!='')
                                      {{ str_replace('_', ' ', env('Verein_HP_Tech_VertretenStrasse')) }}<br>
                                    @endif
                                    @if(env('Verein_HP_Tech_VertretenPLZ')!='')
                                      {{ str_replace('_', ' ', env('Verein_HP_Tech_VertretenPLZ')) }}
                                    @endif
                                    @if(env('Verein_HP_Tech_VertretenOrt')!='')
                                      {{ str_replace('_', ' ', env('Verein_HP_Tech_VertretenOrt')) }}<br>
                                    @endif
                                    @if(env('Verein_HP_Tech_VertretenTelefon')!='')
                                      <i class="icofont-phone"></i> {{ str_replace('_', ' ', env('Verein_HP_Tech_VertretenTelefon')) }}<br>
                                    @endif
                                    @if(env('Verein_HP_Tech_VertretenMail')!='')
                                      <i class="icofont-envelope"></i>  <a href="mailto:{{ str_replace('_', ' ', env('Verein_HP_Tech_VertretenMail')) }}">{{ str_replace('_', ' ', env('Verein_HP_Tech_VertretenMail')) }}</a><br>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                        @include('textimport.recht')
                    </div>
                </div>
            </section><!-- End Breadcrumbs Section -->
    </main><!-- End #main -->
@endsection
