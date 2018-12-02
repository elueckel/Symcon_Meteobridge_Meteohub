## Funktionsumfang
Das Modul lädt diverse Wetterdaten von Meteobridge und Meteohub Servern herunter und stellt sie in Symcon als Variablen zur Verfügung. Dadurch lassen sich z.B. Davis Vantage, Vue und andere Wetterstationen relativ einfach integrieren. 

## Voraussetzungen
IP-Symcon ab Version 5.x (darauf wurde entwickelt - sollte aber auch mit Version 4.x funktionieren.

## Software-Installation
Über das Modul-Control folgende URL hinzufügen.
https://github.com/elueckel/Symcon_Meteobridge_Meteohub

## Einrichten der Instanzen in IP-Symcon
Unter "Instanz hinzufügen" ist das 'WundergroundPWSSync'-Modul unter dem Hersteller '(Sonstige)' aufgeführt.

## Konfigurationsseite:

* Wetter Server: Auswahl des Protokolls (die XML Aufrufe unterscheiden sich)
* Serveradresse: IP oder DNS Name des Meteobridge oder Meteohub Servers
* Benutzername / Kennwort: Anmeldedaten am Wetterserver

Daten
Hier können die Sensoren ausgewählt werden. Version 1.0 umfasst:
* Daten der Station
* Temperatur 1 (normalerweise Teil der Wetterstation)
* Regen
* UV
* Solarstrahlung
* Wind

!!! WICHTIG: Variablen werden nur angelegt wenn die Checkbox gewählt ist und sie werden gelöscht wenn die Funktion abgewählt wird (wichtig im Archiv!!!

* Update Timer, in Sekunden (wie oft Daten geladen werden) - 0 = Aus

## Wo finde ich Informationen ob das Modul funktioniert
Wenn etwas nicht funktioniert, sollten Fehler im Log ausgegeben werden. 

Um zu prüfen was die eigene Station sendet, bitte die folgenden URL's verwenden

- Bridge: http://<Server_Address>/cgi-bin/livedataxml.cgi
- Hub: http://<Server_Address>/meteolog.cgi?mode=data&type=xml&quotes=1
