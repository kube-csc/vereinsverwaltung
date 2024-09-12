## Update Anleitung
**Version V00.05.00**
- 
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

- Wenn die Migration 2024_08_09_010548_add_foreignkey_gruppe_id_to_table fehlschlägt, dann ist die Daten aus dem alten Projekt vorher zu Seeden.
- artisan migrate

**Version V00.04.01**

Die Abteilungen / Sportarten können nach einer Positionsnummer sortiert werden
- artisan migrate

**Version V00.04.00**

- der Ordner storage/app/public ist anzulegen
- der Ordner storage/app/images ist anzulegen
- die Files des public/storage ist unter storage/app/public zu verschieben
- php artisan storage:link
- der Ordner public/storage ist zu löschen

**Version V00.03.02**

Helferdatenbank wurde ergänzt

- artisan migrate
- in der .env muss ergänzt werden:
- PP_REGATTA=ja #ja oder nein

**Version V00.03.01**

- In der .env muss von der .env.example neu parametriert werden

**Version V00.03.00**

**Version V00.02.00**

- artisan migrate
