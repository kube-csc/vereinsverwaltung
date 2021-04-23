<!-- Template Main CSS File abgeändert Ausgabeabhänging -->
<?php

$serverdomain=$_SERVER["HTTP_HOST"];

//todo Code überarbeiten

$abteilungs     = DB::table('sport_sections')->where('status' , '1')->orwhere('domain',$serverdomain)->orderby('status')->get();
$abteilungCount = DB::table('sport_sections')->where('status' , '1')->orwhere('domain',$serverdomain)->count();

$i=0;
foreach ( $abteilungs as $abteilung)
 {
  ++$i;
  //$i=1; // TEMP:
  if ($i==$abteilungCount)
  {
    ?>
    <style>
    <?php
    if( $abteilung->bild<>'' )
     {
      $bild=  $abteilung->bild;
      ?>
      #hero {
        width: 100%;
        height: 100vh;
        background: url("/storage/<?php echo $bild ;?>") top center;
        background-size: cover;
        position: relative;
        margin-bottom: -90px;
      }
     <?php
     }

    if( $abteilung->farbe<>'' )
     {
      $farbe= $abteilung->farbe;
        ?>
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

         .about .content .about-btn {
           /*   background: #d6338d; */
          background: rgba(<?php echo $farbe ;?>);
         }

         .back-to-top {
        /*   background: #d6338d; */
            background: rgba(<?php echo $farbe ;?>);
         }

         /* @media (max-width: 768px) { */
         @media (max-width: 995px) {
           #header.header-scrolled {
             padding: 15px 0;
           }

          #footer .footer-top .footer-info {
            border-top: 4px solid #d6338d;
         }

         .about .content .about-btn {
           /*   background: #d6338d; */
          background: rgba(<?php echo $farbe ;?>);
         }

         .back-to-top {
        /*   background: #d6338d; */
            background: rgba(<?php echo $farbe ;?>);
         }

         }
        }
              <?php
      }
     ?>
   </style>
  <?php
 }//if ($i==$abteilungCount)
}  //endforeach
?>
