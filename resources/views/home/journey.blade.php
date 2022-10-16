@extends('layouts.frontend')

@section('about' ,'/anfahrt') <?php // ToDo: vor dem #About den Routenname hinzufügen verbessern?>
@section('title' ,'Anfahrt')

@section('content')
    <main id="main">

        <!-- ======= Breadcrumbs Section ======= -->
        <section class="breadcrumbs">
            <div class="container">

                <div class="d-flex justify-content-between align-items-center">
                    <h2>Anfahrt</h2>
                    <ol>
                        <li><a href="/">Home</a></li>
                        <li>Anfahrt</li>
                    </ol>
                </div>
            </div>
        </section><!-- End Breadcrumbs Section -->

        <!-- ======= Anfahrt Section ======= -->
        <?php // // ToDo: Bereinigung ?>
        <?php  /*<section class="inner-page">  */?>
        <?php  /*    <section id="anfahrt" class="contact section-bg"> */?>

        <section id="anfahrt" class="contact section-bg">
            <div id="about" class="container">
                <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                    <h2>Anfahrt {{ env('VEREIN_NAME') }}</h2>
                    @include('textimport.anfahrt')
                </div>

                <div class="row" data-aos="fade-up" data-aos-delay="125">
                    <div class="col-lg-6">
                        <div class="info-box mb-4">
                            <i class="bx bx-map"></i>
                            <h3>Adresse</h3>
                            <p>
                                @if (env('VEREIN_NAME')<>"")
                                    {{ str_replace('_', ' ', env('VEREIN_NAME')) }}
                                @else
                                    {{'Hier steht die Anschrift. Bitte in der .Env die Daten pflegen'}}
                                @endif
                                <br>
                                {{ str_replace('_', ' ', env('VEREIN_STRASSE')) }}<br>
                                {{ str_replace('_', ' ', env('VEREIN_PLZ')) }} {{ str_replace('_', ' ', env('VEREIN_ORT')) }}
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
                                    Tel: {{ str_replace('_', ' ', env('VEREIN_TELEFON')) }}<br>
                                @endif
                                @if(env('VEREIN_FAX')<>"")
                                    Fax: {{ str_replace('_', ' ', env('VEREIN_FAX')) }}<br>
                                @endif
                            </p>
                        </div>
                    </div>

                </div>

                <div class="row" data-aos="fade-up" data-aos-delay="200">

                    <div class="col-lg-6 ">
                        @include('textimport.map')
                    </div>

                    <div class="col-lg-6">

                    </div>

                </div>

            </div>
        </section> <!-- End Anfahrt-->
    </main><!-- End #main -->
@endsection
