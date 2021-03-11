@extends('layouts.headFrontend')

@section('about' ,'/impressum') <?php // TODO: vor dem #About den Routenname hinzufügen verbessern?>
@section('title' ,'Impressum')

<?php
// TODO: Config Daten anderes Einbinden
  $version = "1.0.0";
  $installdate = "01.10.2020";
  $verein= "Kanuten Emscher-Lippe e.V.";
  $vereinstrasse = "Zu den Sportstätten 5";
  $vereinsplz = "D-45711";
  $vereinsort = "Datteln";
  $vereintelefon = "(02363) 8420";
  $vereinfax="";
  $vereinemail = "kel@kel-datteln.de";
  $vereineintagngsort = "Recklinghausen";
  $vereinvrnummer = "VR 0627";
  $dateformat = "d.m.Y";
  $sponsoren = "Partner";
  $abmeldezeit = "3600";
  $keywords = "Kanuten Emscher-Lippe, Datteln, KEL, Kanuverein, Sport, Kanuwandersport, Kanurennsport, Kanu, Kajak, Paddeln, Drachenboot, Drachenbootregatta,
               Wassersport, Regatta, SUP, Outrigger";
  $description = "Wir sind ein Kanuverein mit Jugend-, Wander-, Rennsport-, SUP-, Outrigger- und Drachenbootabteilung in Datteln NRW am Dortmund-Ems-Kanal.";

  $slogen="Wir sind ein Kanuverein in Datteln am Dortmund Ems Kanal.";
  $canonical="https://www.kel-datteln.de/neu";
  $domain="kel-datteln.de";
?>

@section('content')
    <main id="main">

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

  <?php // TODO: Beschreibungen in Beschreibungen anpassen ?>
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
              <h3>Adresse</h3>
              <p>
                <?php
                if ($verein<>"") {echo "$verein<br>";} else {echo "Hier steht die Vereinsanschrift<br>Bitte in Einstellungen die Adressdaten eingeben!!<br>";}
                if ($vereinstrasse<>"") {echo "$vereinstrasse<br>";}
                if ($vereinsplz<>"" && $vereinsort<>"") {echo "$vereinsplz $vereinsort<br>";}
                if ($vereineintagngsort<>"") {echo "Eingetragen in das Vereinsregister $vereineintagngsort<br>";}
                if ($vereinvrnummer<>"") {echo "VR-Nummer: $vereinvrnummer<br>";}
                if ($vereintelefon<>"") {echo "Tel: $vereintelefon<br>";}
                if ($vereinfax<>"") {echo "Fax: $vereinfax<br>";}
                if ($vereinemail<>"") {?>E-Mail: <a href="mailto:<?php echo "$vereinemail";?>"><?php echo "$vereinemail";?></a> <?php ;}
                ?>
              </p>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="info-box  mb-4">
              <i class="bx bx-home"></i>
              <h3>Dieser vertreten durch den Vorsitzenden:</h3>
            <p>
              <b>1. Vorsitzender: </b><a href="mailto:d.kuhnert@kel-datteln.de">Detlev Kuhnert</a>
              <br>
              Kreuzstr. 1 b, 45711 Datteln,  Mobil 0172-6862220<br>
              Für weitere Mitglieder des Vorstands <a href="index.php#team">hier</a> klicken.
            </p>
          </div>
         </div>

          <div class="col-lg-6">
            <div class="info-box  mb-4">
              <i class="bx bx-home"></i>
              <h3>Für den Inhalt dieser Seiten ist verantwortlich als Webmaster:</h3>
              <p>
                <b>Webmaster: </b> <a href="mailto:webmaster@kel-datteln.de">Stefan Kuck</a>
                <br>Kieselstrasse 60, 45731 Waltrop, Tel. (02309) 73056
              </p>
              </p>
            </div>
          </div>

     </div>

      <div class="section-title" data-aos="fade-in" data-aos-delay="100">
        <h2>Haftungsausschluss</h2>
        <h3>Haftung für Inhalte</h3>
        <p>
          <?php /*?><p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';"> </span></p>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Quelle: <em><a href="http://www.e-recht24.de/nofollow" target="_blank"><span style="color: blue;">eRecht24</span></a>, Rechtsanwalt für Internetrecht Sören Siebert</em></span></p><?php */?>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt. Für die Richtigkeit,
          Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen. Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen
          Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu
          forschen, die auf eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine
          diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte
          umgehend entfernen.</span></p>
          <br>
          <h3>Haftung für Links</h3>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen Einfluss
          haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Die
          verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar. Eine permanente inhaltliche
          Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend
          entfernen.</span></p>
          <br>
          <h3>Urheberrecht</h3>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem
          deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors
          bzw. Erstellers. Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet. Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden
          die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir um einen
          entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.</span></p>
          <?php /*?><p style="line-height: normal;"><strong><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Datenschutz</span></strong></p>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Die Nutzung unserer Webseite ist in der Regel ohne Angabe personenbezogener Daten möglich.
          Soweit auf unseren Seiten personenbezogene Daten (beispielsweise Name, Anschrift oder eMail-Adressen) erhoben werden, erfolgt dies, soweit möglich, stets auf freiwilliger Basis. Diese Daten werden
          ohne Ihre ausdrückliche Zustimmung nicht an Dritte weitergegeben.</span></p>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Wir weisen darauf hin, dass die Datenübertragung im Internet (z.B. bei der Kommunikation per
          E-Mail) Sicherheitslücken aufweisen kann. Ein lückenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht möglich.</span></p>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Der Nutzung von im Rahmen der Impressumspflicht veröffentlichten Kontaktdaten durch Dritte zur
          Übersendung von nicht ausdrücklich angeforderter Werbung und Informationsmaterialien wird hiermit ausdrücklich widersprochen. Die Betreiber der Seiten behalten sich ausdrücklich rechtliche Schritte
          im Falle der unverlangten Zusendung von Werbeinformationen, etwa durch Spam-Mails, vor.</span></p>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';"> </span></p>
          <p style="line-height: normal;"><strong><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Datenschutzerklärung für die Nutzung von Facebook-Plugins
          (Like-Button)</span></strong></p>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Auf unseren Seiten sind Plugins des sozialen Netzwerks Facebook, 1601 South California Avenue,
          Palo Alto, CA 94304, USA integriert. Die Facebook-Plugins erkennen Sie an dem Facebook-Logo oder dem "Like-Button" ("Gefällt mir") auf unserer Seite. Eine Übersicht über die Facebook-Plugins finden
          Sie hier: <a href="http://developers.facebook.com/docs/plugins/" target="_blank"><span style="color: blue;">http://developers.facebook.com/docs/plugins/</span></a>.<br/>
          Wenn Sie unsere Seiten besuchen, wird über das Plugin eine direkte Verbindung zwischen Ihrem Browser und dem Facebook-Server hergestellt. Facebook erhält dadurch die Information, dass Sie mit Ihrer
          IP-Adresse unsere Seite besucht haben. Wenn Sie den Facebook "Like-Button" anklicken während Sie in Ihrem Facebook-Account eingeloggt sind, können Sie die Inhalte unserer Seiten auf Ihrem
          Facebook-Profil verlinken. Dadurch kann Facebook den Besuch unserer Seiten Ihrem Benutzerkonto zuordnen. Wir weisen darauf hin, dass wir als Anbieter der Seiten keine Kenntnis vom Inhalt der
          übermittelten Daten sowie deren Nutzung durch Facebook erhalten. Weitere Informationen hierzu finden Sie in der Datenschutzerklärung von facebook unter <a href="http://de-de.facebook.com/policy.php" target="_blank"><span style="color: blue;">http://de-de.facebook.com/policy.php</span></a></span></p>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Wenn Sie nicht wünschen, dass Facebook den Besuch unserer Seiten Ihrem Facebook-Nutzerkonto
          zuordnen kann, loggen Sie sich bitte aus Ihrem Facebook-Benutzerkonto aus.</span></p>
          <p style="line-height: normal;"><span style="font-size: 12pt; font-family: 'Times New Roman','serif';"> </span></p>
          <p style="line-height: normal;"><em><span style="font-size: 12pt; font-family: 'Times New Roman','serif';">Quellen: <a href="http://www.e-recht24.de/muster-disclaimer.htm" target="_blank"><span style="color: blue;">Disclaimer</span></a> von eRecht24, dem Portal zum Internetrecht von Rechtsanwalt Sören Siebert, <a href="http://www.e-recht24.de/artikel/datenschutz/6590-facebook-like-button-datenschutz-disclaimer.html" target="_blank"><span style="color: blue;">eRecht24 Datenschutzerklärung für
          Facebook</span></a></span></em></p><?php */?>
        </p>
      </div>

    </section><!-- End Breadcrumbs Section -->
    </main><!-- End #main -->
@endsection
