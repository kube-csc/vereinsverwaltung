## Update Anleitung
**Version V00.05.00**

In der Dantenbank in der Tabelle race sind die Felder in der Datenbank von Hand  
event_id von integer auf unsignedBigInteger umzustellen
tabele_id von integer auf unsignedBigInteger umzustellen
tabelerennen_id von integer auf unsignedBigInteger umzustellen
gruppe_id von integer auf unsignedBigInteger umzustellen

In der Dantenbank in der Tabelle tabele sind die Felder in der Datenbank von Hand  
event_id von integer auf unsignedBigInteger und auf nullable umzustellen
gruppe_id von integer auf unsignedBigInteger und auf nullable umzustellen
system_id von integer auf unsignedBigInteger und auf nullable umzustellen

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
