## Funktionsumfang
Das Modul lädt diverse Wetterdaten von Meteobridge und Meteohub Servern herunter und stellt sie in Symcon als Variablen zur Verfügung. Dadurch lassen sich z.B. Davis Vantage, Vue und andere Wetterstationen relativ einfach integrieren.

## Voraussetzungen
IP-Symcon ab Version 5.x (darauf wurde entwickelt - sollte aber auch mit Version 4.x funktionieren (evtl. Probleme wegen verwendeter Profile).

## Software-Installation
Über das Modul-Control folgende URL hinzufügen.
https://github.com/elueckel/SymconWUPWSS

## Einrichten der Instanzen in IP-Symcon
Unter "Instanz hinzufügen" ist das 'WundergroundPWSSync'-Modul unter dem Hersteller '(Sonstige)' aufgeführt.

## Konfigurationsseite:

Wetter Server: Auswahl des Protokolls (die XML Aufrufe unterscheiden sich)
Serveradresse: IP oder DNS Name des Meteobridge oder Meteohub Servers
Benutzername / Kennwort: Anmeldedaten am Wetterserver

Daten Hier können die Sensoren ausgewählt werden.

### Version 1.0 02/12/2018
- Temperatur 1 (normalerweise Teil der Wetterstation)
- Regen
- UV
- Solarstrahlung
- Wind
- Ein Timer zur einstellen des Intervalls
- Variablen werden nur bei aktiver Auswahl erstellt.


### Version 1.1 03/12/2018
- Fix Meteohub
- Fix Solarradtion und UV Daten werden nicht geladen

### Version 2.0 04/12/2018
- Added Hinzufügen von Profil für Solarstrahlung w/m2
- Added Hinzufügen von Profil für Bodenfeuchte cb
- Erzeugen von High Frequency Variablen für Markisen (Temp, Wind, Böen) und eigenem Timer
- Hinzufügen von Abfragen für Blattfeuchte (leider noch nicht getestet)
- Hinzufügen von Abfragen für Bodenfeuchte & -temperatur (leider noch nicht getestet)


## Wo finde ich Informationen ob das Modul funktioniert
Das Modul postet Informationen ins Log (Stand V2.0). 
