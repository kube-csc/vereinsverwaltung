<!-- Template Main CSS File abgeändert bei verschiedene Ausgaben -->
<?php
$serverdomain=$_SERVER["HTTP_HOST"];

//$sportSectionSearch wird in public function homeSportSelect im HomeController gebildet
if(isset($sportSectionSearch)){
 $abteilungStyls  = DB::table('sport_sections')->where('abteilung' , $sportSectionSearch)->get();
 }
 else{
  $abteilungStyls = DB::table('sport_sections')->where('status' , '1')->orwhere('domain',$serverdomain)->orderby('status')->get();
 }

$abteilungStylsCount = $abteilungStyls->count();

$i=0;
?>
<style>

@include('textimport.cssColor')

@foreach ( $abteilungStyls as $abteilungStyl)
    @php
      ++$i;
    @endphp
    @if ($i == $abteilungStylsCount)

          @if( $abteilungStyl->bild<>'' )
            @php
             $bild = $abteilungStyl->bild;
            @endphp
           #hero {
                    width: 100%;
                    height: 100vh;
                    background: url("/storage/header/{{$bild}}") top center;
                    background-size: cover;
                    position: relative;
                    margin-bottom: -90px;
                }
          @endif
          @if( $abteilungStyl->farbe<>'' )
                @php
                    $farbe= $abteilungStyl->farbe;
                @endphp

          #header {
               transition: all 0.5s;
               z-index: 997;
               transition: all 0.5s;
               padding: 15px 0;
               background: rgba(<?php echo $farbe ;?>);
           }

          #header.header-scrolled {
              background: rgba(<?php echo $farbe ;?>);
              padding: 0;
          }

          #footer .footer-top .footer-info {
              border-top: 4px solid rgba(<?php echo $farbe ;?>);
          }

          .about .content .about-btn {
              background: rgba(<?php echo $farbe ;?>);
          }

          .back-to-top {
              background: rgba(<?php echo $farbe ;?>);
          }

          /* @media (max-width: 768px) { */
          @media (max-width: 995px) {
              #header.header-scrolled {
                  padding: 15px 0;
              }

          #footer .footer-top .footer-info {
              border-top: 4px solid rgba(<?php echo $farbe ;?>);
          }

          .about .content .about-btn {
              background: rgba(<?php echo $farbe ;?>);
          }

          .back-to-top {

              background: rgba(<?php echo $farbe ;?>);
          }
          @endif
    @endif
@endforeach
    </style>
