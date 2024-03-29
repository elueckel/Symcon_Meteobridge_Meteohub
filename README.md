## Funktionsumfang

Das Modul lädt diverse Wetterdaten von Meteobridge und Meteohub Servern herunter und stellt sie in Symcon als Variablen zur Verfügung. Dadurch lassen sich z.B. Davis Vantage, Vue und andere Wetterstationen relativ einfach integrieren.

Für Fragen und Wünsche besuchen Sie bitte: https://www.symcon.de/forum/threads/39389-Modul-Meteobridge-Meteohub-%28z-B-f%C3%BCr-Davis-Vantage-Vue-%29?highlight=meteobridge

## Voraussetzungen

IP-Symcon ab Version 5.1

## Software-Installation

Über das Modul-Control folgende URL hinzufügen.
https://github.com/elueckel/Symcon_Meteobridge_Meteohub

## Einrichten der Instanzen in IP-Symcon
Unter "Instanz hinzufügen" ist das 'WundergroundPWSSync'-Modul unter dem Hersteller '(Sonstige)' aufgeführt.

## Konfigurationsseite:

Wetter Server: Auswahl des Protokolls (die XML Aufrufe unterscheiden sich)
Serveradresse: IP oder DNS Name des Meteobridge oder Meteohub Servers
Benutzername / Kennwort: Anmeldedaten am Wetterserver

Daten Hier können die Sensoren ausgewählt werden.

### Version 1.0 02/12/2018
* Temperatur 1 (normalerweise Teil der Wetterstation)
* Regen
* UV
* Solarstrahlung
* Wind
* Ein Timer zur einstellen des Intervalls
* Variablen werden nur bei aktiver Auswahl erstellt.

### Version 1.1 03/12/2018
* Fix Meteohub
* Fix Solarradtion und UV Daten werden nicht geladen

### Version 2.0 04/12/2018
* Neu Hinzufügen von Profil für Solarstrahlung w/m2
* Neu Hinzufügen von Profil für Bodenfeuchte cb
* Neu Hinzufügen von High Frequency Variablen für Markisen (Temp, Wind, Böen) und eigenem Timer
* Neu Hinzufügen von Abfragen für Blattfeuchte (leider noch nicht getestet)
* Neu Hinzufügen von Abfragen für Bodenfeuchte & -temperatur (leider noch nicht getestet)
### Version 2.1 04/12/2018
* Neu Variable die die Windgeschwindigkeit in Text ausgibt
* Fix Solarstrahlung wurde nicht ausgelesen

### Version 3.0 16/12/2018
* Neu bei Verwendung einer Davis Vantage inkl. Solar Radiation Sensor wird die Evoparation ausgelesen
* Neu wenn eine Meteobridge verwendet wird, da werden aktuell die folgenden Min-Max Tagesstatistiken (via Template) gelesen
* Temperatur (Min/Max), Luftfeuchte (Min/Max), Luftdruck (Min/Max), Taupunkt (Min/Max), Wind (Max), Böhen (Max), UV (Max), Solarstrahlung (Max), Evoparation (Max), Regen (Max)
* Die Tageswerte, werden via Timer einmal täglich um 23.58 ausgelesen.
* Statistikfunktion legt eine Menge an Variablen an - aktuell nicht konfigurierbar (die UI wird sonst überladen)
* Die Tagesstatistiken können via Symcon Charts etc einfach ausgewertet werden (ich habe bewusst auf Monats, Jahres etc. Auswertungen verzichtet)

### Version 3.1 28/04/2019
* Fix Passwort ist nun in Passwort Feld versteckt
* Bodenfeuchte / Temperatursensoren sind nun vollständig eingebunden

### Version 3.2 09/06/2019
* Fix Blattfeuchte Sensor
* Fix Komponente behält nun die alten Werte bei, falls die Meteobridge/Meteohub mal nicht erreichbar ist und setzt eine neue Error Variable auf welche man einen Trigger z.B. für den Versand einer Email setzen kann. 

### Version 4.0 04/01/2021
* Komplett überarbeitete Oberfläche für die Konfiguration
* Code Cleanup
* Debug Meldungen in der Konsole
* Umstellung auf CURL um Abfragen bei WLAN Ausfällen sicherer zu gestalten
* Timer für Min/Max Werte ins Modul verlegt - läuft immer um 23:59:00
* Direkte Umrechnung der Solarstrahlung von Watt in Lux (neue Variable)

### Version 4.01 24/03/2023
* Fix - Abfrage Blattfeuchte angepasst
* Fix - Timer für Abfrage von Bodensensoren angepasst um Timeout zu vermeiden

## Wo finde ich Informationen ob das Modul funktioniert
Debugübersicht im Modul
