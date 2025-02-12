## Update Anleitung
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
