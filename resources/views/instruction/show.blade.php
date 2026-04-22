@extends($layout ?? 'layouts.frontend')

@php
    // Neu: Controller liefert i.d.R. `$instruction`.
    // Fallback: Legacy-Variable `$instructions` (Collection) wird weiterhin unterstützt.
    $instruction = $instruction ?? ($instructions->first() ?? null);

    $texausgabe = $instruction
        ? str_replace(array("\\r\\n", "\\n", "\\r"), '<br>', (string)$instruction->beschreibung)
        : '';
    $texausgabe = str_replace(array("</li><br>"), '</li>', $texausgabe);

    $ueberschrift = $instruction?->ueberschrift ?? '';

    // Optional: Akzentfarbe für diese Informationsseite (Hex-Farbe, z.B. #0ea5e9)
    $accentColor = $instruction?->accentColor ?? null;

    // Sportgruppen-/Abteilungs-Logik im Projekt nutzt CSS-Variablen (siehe `textimport/cssColor.blade.php`).
    // Für Instructions überschreiben wir diese Variablen pro Seite, damit die Darstellung konsistent bleibt
    // (ohne Überschriften direkt zu recolorn).
    $accentHex6 = null;
    $accentMenuBg = null;
    $accentHover = null;

    if (!empty($accentColor) && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $accentColor)) {
        $hex = substr($accentColor, 1);
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $accentHex6 = '#'.$hex;
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Menübackground wie im Template üblich mit Transparenz.
        $accentMenuBg = 'rgba('.$r.', '.$g.', '.$b.', 0.8)';

        // Hover etwas heller.
        $hovR = (int) min(255, floor($r + (255 - $r) * 0.25));
        $hovG = (int) min(255, floor($g + (255 - $g) * 0.25));
        $hovB = (int) min(255, floor($b + (255 - $b) * 0.25));
        $accentHover = sprintf('#%02x%02x%02x', $hovR, $hovG, $hovB);
    }

    // Optional: Hero-Text kann pro Informationsseite überschrieben werden.
    $heroTitle = !empty($instruction?->headerTitel) ? $instruction->headerTitel : $ueberschrift;
    $heroSlogen = !empty($instruction?->headerSlogen) ? $instruction->headerSlogen : '';
@endphp

@section('title' , $ueberschrift)

@if(!empty($accentHex6) && !empty($accentMenuBg) && !empty($accentHover))
    @section('page_styles')
        <style>
            /*
             * Darstellung analog zur Abteilungs-/Sportgruppen-Logik:
             * Wir überschreiben die im Template verwendeten CSS-Variablen,
             * statt Überschriften direkt zu färben.
             */
            :root {
                --menubackground: {{ $accentMenuBg }};
            }

            /* Wie bei Sportgruppen: Header nicht transparent, sondern eingefärbt */
            #header,
            #header.header-transparent,
            #header.header-scrolled {
                background: {{ $accentMenuBg }} !important;
            }

            /* Wie bei Sportgruppen: Back-to-Top übernimmt die Akzentfarbe (Links bleiben Standard-Farbe) */
            .back-to-top {
                background: {{ $accentMenuBg }} !important;
            }

            .back-to-top:hover {
                background: {{ $accentHover }} !important;
            }

            /* Footer bleibt grundsätzlich unverändert – aber die obere Linie der Footer-Info soll akzentuiert werden */
            #footer .footer-top .footer-info {
                border-top: 4px solid {{ $accentHex6 }} !important;
            }
        </style>
    @endsection
@endif

{{-- Wenn ein Headerbild vorhanden ist, wird `layouts.headFrontend` verwendet.
     Dafür setzen wir optional Hero-Overrides (Titel + Background). --}}
@if(($layout ?? null) === 'layouts.headFrontend' && !empty($heroBackgroundUrl))
    @section('hero_background_url')
        {{ $heroBackgroundUrl }}
    @endsection

    @section('hero_h1')
        {{ $heroTitle }}
    @endsection

    @section('hero_h2')
        {{ $heroSlogen }}
    @endsection

    @section('hero_button_href')
        #main
    @endsection
@endif

@section('content')

  <main id="main">
    <!-- ======= Breadcrumbs Section ======= -->
    <section class="breadcrumbs">
      <div class="container">

        <div class="d-flex justify-content-between align-items-center">
          <h2>{{ $ueberschrift }}</h2>
          <ol>
            <li><a href="/">Home</a></li>
            <li>{{ $ueberschrift }}</li>
          </ol>
        </div>
      </div>
    </section><!-- End Breadcrumbs Section -->

          <!-- ======= Datenschutzerklärung Section ======= -->
    <?php  /*<section class="inner-page">  */?>
      <?php  /*<section id="contact" class="contact section-bg">  */?>
    <?php /*<section id="about" class="about"> */ ?>
    <section class="inner-page">
      <div class="container">

          <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>{{ $ueberschrift }}</h2>
              {!! $texausgabe !!}
          </div>

          @if($documents->count())
            <div class="section-title" data-aos="fade-in" data-aos-delay="150">
              <b>Dokumente zum Downloaden</b>
              @foreach($documents as $document)
                <div>
                  <a href="/storage/dokumente/{{$document->dokumentenFile}}" target="_blank">
                    <i class="bx bxs-note"></i> {{$document->dokumentenName}}
                  </a>
                </div>
              @endforeach
            </div>
          @endif

      </div>

    </section><!-- End About Section -->
  </main><!-- End #main -->

@endsection
