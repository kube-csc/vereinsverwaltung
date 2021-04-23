<!-- Template Main CSS File abgeändert Ausgabeabhänging -->
<?php

$serverdomain=$_SERVER["HTTP_HOST"];
//echo "$serverdomain"; // TEMP: 
//todo Code überarbeiten
$abteilungs     = DB::table('sport_sections')->where('status' , '1')->orwhere('domain',$serverdomain)->orderby('status')->get();
$abteilungCount = DB::table('sport_sections')->where('status' , '1')->orwhere('domain',$serverdomain)->count();

$i=0;
foreach ( $abteilungs as $abteilung)
  {
  ++$i;
   $bild=       $abteilung->bild;
   $idabteilung=$abteilung->id;

   //echo "$abteilung->id $abteilung->domain $abteilung->status <br>";  //temp

   if( $abteilung->farbe<>'' )
    {
     $farbe=      $abteilung->farbe;
    }
    else
    {
      $farbe="0, 98, 204, 0.8";
    }

?>
<style>
#hero {
  width: 100%;
  height: 100vh;
  background: url("/storage/<?php echo $bild ;?>") top center;
  background-size: cover;
  position: relative;
  margin-bottom: -90px;
}

<?php
if ($i==$abteilungCount)
{
  if ($idabteilung==9)
    {
    ?>
      #header {
        transition: all 0.5s;
        z-index: 997;
        transition: all 0.5s;
        padding: 15px 0;
        //background: rgba(204, 0, 113, 0.8);
        background: rgba(<?php echo $farbe ;?>);
      }

      #header.header-scrolled {
        //background: rgba(204, 0, 113, 0.8);
        background: rgba(<?php echo $farbe ;?>);
      padding: 0;
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
       background: #d6338d;
     }

     .back-to-top {
       background: #d6338d;
     }

     /*--------------------------------------------------------------
     # Back to top button
     --------------------------------------------------------------*/
     .back-to-top {
       position: fixed;
       display: none;
       width: 40px;
       height: 40px;
       border-radius: 50px;
       right: 15px;
       bottom: 15px;
       /*background: #67b0d1; KEL Dunkel*/
       background: #d6338d;
       color: #fff;
       transition: display 0.5s ease-in-out;
       z-index: 99999;
     }

     .back-to-top i {
       font-size: 24px;
       position: absolute;
       top: 7px;
       left: 8px;
     }


    }
  <?php
  }
  else
  {
    ?>
    /*--------------------------------------------------------------
    # Back to top button
    --------------------------------------------------------------*/
    /*
    .back-to-top {
      position: fixed;
      display: none;
      width: 40px;
      height: 40px;
      border-radius: 50px;
      right: 15px;
      bottom: 15px;
      /*background: #67b0d1; KEL Dunkel*/
      /*
      background: #0062cc;
      color: #fff;
      transition: display 0.5s ease-in-out;
      z-index: 99999;
      */
    }
   <?php
   }
 }//if ($i==$abteilungCount)
}  //endforeach
?>
</style>
