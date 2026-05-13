## Update Anleitung
**Version V00.10.06**

***Neue Funktionen***
- **Vollständige deutsche Übersetzung:**
    - Erstellung der zentralen Sprachdatei `resources/lang/de.json` für UI-Elemente.
    - Übersetzung der Authentifizierungs-Meldungen (`auth.php`).
    - Komplette deutsche Validierungsfehlermeldungen (`validation.php`) inklusive Attribut-Mapping (z.B. "email" -> "E-Mail-Adresse").
    - Lokalisierung aller Jetstream-Standardansichten (Login, Register, Passwort vergessen, etc.).

***Erweiterung Einladungs-System***
- **Flexible Einladungsmodi:**
    - Unterstützung von Einladungen ohne feste E-Mail-Adresse (nur mit Label/Name).
    - Automatisches Nachpflegen der E-Mail-Adresse in die Einladungs-Tabelle bei erfolgreicher Registrierung.
- **Einmal-Verwendung & Sicherheit:**
    - Striktes Tracking der Token-Nutzung (`registered_at`).
    - Verknüpfung des neu erstellten Benutzers mit der Einladung (`user_id`).
- **WhatsApp-Integration:**
    - Button zum direkten Teilen des Registrierungslinks via WhatsApp.
    - Dynamischer Einladungstext unter Verwendung des konfigurierten App-Namens.

*** Layout-Verbesserungen (Fix)***
- **Zentralisierung der Textverarbeitung:**
    - Die Funktion `textmax` wurde in ein zentrales Partial `resources/views/_partials/function.blade.php` ausgelagert.
    - Automatische Einbindung der Funktionen in die Frontend-Layouts (`frontend`, `headFrontend`, `frontendLivewire`).
- **Intelligente Textkürzung mit HTML-Support:**
    - Die `textmax`-Funktion berücksichtigt nun HTML-Tags beim Kürzen.
    - **Tag-Sicherheit:** Offene HTML-Tags werden automatisch geschlossen, um Layout-Fehler zu vermeiden.
    - **Wort-Erhalt:** Texte werden nicht mitten im Wort abgeschnitten.

***Hinweise nach dem Update***
- `php artisan migrate`
- in Ordner "/recources/views/textimport ist folgendes zu Bearbeiten:
   mailImpressum.blade.php anlegen und mit der Vorlage von mailImpressum_example.blade.php ausfüllen
- - .env   - Korrektur der E-Mail-Konfiguration für SMTP-Server (Absenderadresse und Name).

**Version V00.10.05**

***Neue Funktionen***
- **Informationsseiten: optionaler Header (Hero) im Frontend**
  - Pro Informationsseite kann optional ein **Headerbild** gesetzt werden.
  - Optional können zusätzlich **Header-Titel** und **Header-Slogen** gepflegt werden.
  - **Frontend-Layout-Switch:** Wenn `headerBild` gesetzt ist, wird automatisch `layouts.headFrontend` verwendet, ansonsten `layouts.frontend`.
- **Backend: Informationsseiten-Menüverwaltung**
  - Bei Menü-Unterpunkten (`hauptmenu = 3`) werden **Down-/MaxDown-Pfeile** nur angezeigt, wenn der nächste Menüpunkt ebenfalls ein Unterpunkt im gleichen Block ist.
  - Am **letzten Menüpunkt** werden keine Down-Pfeile angezeigt.

***Datenbankanpassungen***
- Migration: `instructions` um folgende Felder erweitert:
  - `headerBild` (nullable)
  - `headerTitel` (nullable)
  - `headerSlogen` (nullable)
- **Speicherort Headerbilder:** `storage/app/public/instructionHeader` (öffentlich über `/public/storage/...` nach `php artisan storage:link`).

***Hinweise nach dem Update***
- Neue/fehlende Migration für **`sport_sections`** ergänzt.
- Feld **`tabeles.beschreibung`** wurde auf **`TEXT`** geändert (vermeidet MySQL Row-Size Probleme bei großen Texten).
- `php artisan migrate`
- Falls noch nicht vorhanden: `php artisan storage:link`

**Version V00.10.04**

***Neue Funktionen***
****Regatta-Verwaltung – RegattaTeamManager****
- **Team-Verlinkung (Historie):** Teams können über verschiedene Regatten hinweg mittels einer `teamlink`-ID verknüpft werden.
- **Andere Regatten:** Automatische Anzeige aller historischen Teilnahmen eines Teams (chronologisch sortiert) in der Übersicht.
- **Erweiterte Filter:** Neue Filter für Teams ohne Teamlink, regattaübergreifende Suche (0) und Teams mit nur einer Verwendung der ID.
- **Bearbeitungs-Ansicht:** Neue Seite zur Verwaltung von Teamlinks mit intelligenter Vorschlagslogik (gleicher Bootstyp, ähnliche Merkmale).
- **Synchronisation:** Direkte bidirektionale Zuweisung von Teamlink-IDs zwischen dem bearbeiteten Team und Vorschlägen.
- **ID-Generierung:** Automatische Vergabe neuer Teamlink-IDs inklusive Lückensuche im vorhandenen Nummernbereich.
- **UI-Optimierung:** Umstellung des Filters auf ein zweizeiliges Layout und Integration von Boxicons für alle Aktionen.
- **Detail-Anzeige:** Integration von technischen Parametern (Min/Max Paddler, Distanz) für Rennklassen und Bootstypen in allen Ansichten.

**Version V00.10.03**

***Datenbankanpassungen***
- Für das Fahrtenbuch erfolgen Datenbankanpassungen 

- migration

**Version V00.10.02**

- migration

***Neue Funktionen***

***Backend – Trainerverwaltung (Kursangebot)***
- <strong>Trainertypen verwalten</strong>: Trainerfunktionen (Rollen) können angelegt/bearbeitet und deaktiviert/reaktiviert werden.
- Pro Trainerfunktion können Voreinstellungen gepflegt werden (z. B. Standard-Veranstaltung/Kursangebot und Standard-Abteilung/Mannschaft).
- Neue Option <strong>„Im Kursangebot verwendbar“</strong>: steuert, ob eine Trainerfunktion im Kurs-/Training-Bereich angeboten werden soll.
- <strong>Trainer zuordnen</strong>: Benutzern können eine oder mehrere Trainerfunktionen zugewiesen werden; die Voreinstellungen werden dabei automatisch übernommen.
- <strong>Übersicht</strong>: Gruppierte Anzeige der Zuordnungen nach Trainerfunktion.

**Version V00.10.01**
Umstellung auf Laravel V10
Erweiterung der Datenbank  für die Trainingsverwaltung im Regattabereich
Erweiterung der Datenbank  für FAQ im Regattabereich
Erweiterung der Datenbank  für Header für Kurse / Termine / Trainings
Erweiterung der Datenbank für Trainingstermine

- migration
- php artisan db:seed --class=FaqSeeder  für Demodaten FAQ

**Version V00.10.00**

- migration

***Neue Funktionen***
- In den Rennen können jetzt youtube Videos und ein youtube LiveStream  Daten für die Slideshow angeben werden

**Version V00.09.02**

- werbung_options.php anlegen und mit der Vorlage von werbung_options_example.blade.php ausfüllen
- .env anpassen: TINYMCE_API_KEY=dein_tinymce_api_key eintragen
- migration

***Neue Funktionen***
- Regatta-Teams: Meldung, Bearbeitung und Übersicht erweitert
- Status-Feld für Teams mit Auswahl (Neuanmeldung, Warteliste, Nicht angetreten, Disqualifiziert, ausgeschieden, gelöscht)
- TinyMCE-Editor für Beschreibung/Kommentar, API-Key über .env steuerbar, Einbindung als Partial
- Werbungsquellen-Auswertung für Teams mit Tortendiagramm
- Werbungsquellen-Optionen und inaktive Werte zentral ausgelagert (resources/views/textimport/werbung_options.php)
- Slite-Integration für Regatta-Steuericon in der Racebearbeitung, 

**Version V00.09.01**

- migration

***Neue Funktionen***
- Trainingszeiten können für Abteilungen / Mannschaften hinterlegt werden
- Mitglieder können Bilder zu Veranstaltungen hochladen, wenn sie ein Passwort haben
- Bug fehlende migrationsdatei password_reset_tokens für die App Kurse
 
**Version V00.09.00**

Daten unter /storage/app/public/storage soll von Hand als backup gesichert werden
- migration

***Neue Funktionen***
- Trainingszeiten können für Abteilungen / Mannschaften hinterlegt werden

**Version V00.08.00**

- migration
- php artisan db:seed --class=PlayerDataSeeder
- php artisan db:seed --class=PlayerSeeder

***Neue Funktionen***
- Social Media kann in den Events/Termine angegeben werden 

**Version V00.07.01**

- migration
- In der Datenbank in der Tabelle race_types und race_type_templates sind die Felder in der Datenbank von Hand
  training ist von boolean auf integer umzustellen
- In der Datenbank in der Tabelle regatta_teams ist das Felder in der Datenbank von Hand
  training ist von boolean auf decimal 8,2 umzustellen

***Neue Funktionen***
****Regatta-Verwaltung****
- Events / Termine können jetzt Ankündigung als kurz Text eingeben werden
- Events / Termine können jetzt Texte für die E-Mail Anmeldebestätigung eingeben werden
- Rennklassen können als Vorlage angelegt und bearbeitet werden
- Rennklassen können der Regatta zugeordnet werden

**Version V00.07.00**

Es werden die Datenbanken für Kursangebote und Buchungen integriert
- migration
- Folgende Demo Daten können "geseedert" werden:
   TrainertypSeeder
   TrainertableSeeder
   InstructionSeeder
   CourseParticipantSeeder
   OrganiserSeeder
   CourseSeeder
   CoursedateSeeder
   SportEquipmentSeeder
   SportEquipmentBookedSeeder
   OrganiserSportSectionSeeder
   CourseSportSectionSeeder
   CoursedateUserSeeder
   CourseParticipantBookedSeeder
   OrganiserinformationSeeder
-  anlegen der Unterordner unter /storage/app/public/sportgeraete

**Version V00.06.01**

***Neue Funktionen***
- Überarbeitung der Buchholz-Wertung
- Menu für Rennen überarbeitet

**Version V00.06.00**

- artisan migrate
- Folgende Demo Daten können "geseedert" werden:
    LaneSeeder

***Neue Funktionen***
- Rennen können in Tabellen ausgewertet werden
- Rennen mit die als Mixed gekennzeichnet sind, werden in unterschiedlichen Tabellen ausgewertet
- Rennen mit Einzelwertung können in Tabellen ausgewertet werden

**Version V00.05.00**

- Composer update
- In der Datenbank in der Tabelle race sind die Felder in der Datenbank von Hand  
  event_id von integer auf unsignedBigInteger bigint(20) Attribute=UNSIGNED umzustellen
  tabele_id von integer auf unsignedBigInteger bigint(20) Attribute=UNSIGNED umzustellen
  tabelerennen_id von integer auf unsignedBigInteger bigint(20) Attribute=UNSIGNED umzustellen
  gruppe_id von integer auf unsignedBigInteger bigint(20) Attribute=UNSIGNED umzustellen
  
  In der Datenbank in der Tabelle tabele sind die Felder in der Datenbank von Hand  
  event_id von integer auf unsignedBigInteger bigint(20) Attribute=UNSIGNED umzustellen
  gruppe_id von integer auf unsignedBigInteger und auf nullable umzustellen
  system_id von integer auf unsignedBigInteger und auf nullable umzustellen
  Feld veroeffentlichungUhrzeit in finaleAnzeigen Umbenennen

- artisan migrate
  Wenn die Migration 2024_08_09_010548_add_foreignkey_gruppe_id_to_table fehlschlägt, dann sind die Daten mit php artisan db:seed --class=RaceTypeSeeder nach Abruch der Mirgation zu seeden.
  und die Mirgation neu zu starten.


***Neue Funktionen***
- Mannschaften können den Rennen zugeteilt werden
- Platzierungen können eingegeben werden

**Version V00.04.01**

Die Abteilungen / Sportarten können nach einer Positionsnummer sortiert werden
- artisan migrate

***Neue Funktionen***
- Die Abteilungen / Sportarten können nach einer Positionsnummer sortiert werden
 
**Version V00.04.00**

- der Ordner storage/app/public ist anzulegen
- der Ordner storage/app/images ist anzulegen
- die Files des public/storage ist unter storage/app/public zu verschieben
- php artisan storage:link
- der Ordner public/storage ist zu löschen

**Version V00.03.02**

- artisan migrate
- in der .env muss ergänzt werden:
- PP_REGATTA=ja #ja oder nein

***Neue Funktionen***
- Helferdatenbank wurde ergänzt

**Version V00.03.01**

- In der .env muss von der .env.example neu parametriert werden

**Version V00.03.00**

**Version V00.02.00**

- artisan migrate
