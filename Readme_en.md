## Functionality

This module allows to integrate Meteobridge and Meteohub weather servers in to Symcon. Through these Weather servers personal weather stations of DAVIS, like Vantage and Vaue can easily be integrated. 

In case of questions and addtional information please visit: https://www.symcon.de/forum/threads/39389-Modul-Meteobridge-Meteohub-%28z-B-f%C3%BCr-Davis-Vantage-Vue-%29?highlight=meteobridge

## Required

IP-Symcon Version 5.1

## Software-Installation

Komponent can be installed via Module Control or Module Store
https://github.com/elueckel/Symcon_Meteobridge_Meteohub

## Setup
Using the "Add Instance" the Meteobridge/Meteohub module can be selected.

## Configuration:

Weather Server: Select if you are using a Meteobridge or a MeteoHub
Serveradress: IP or DNS name of the server
Username and Password: Credentials to log into the server

Now the sensors can be selected, which should be made available in Symcon - obviously depending on the configuration of the Weather Station.


### Version 4.0 20/12/2020
* Complete Redesign of the config UI
* Code Cleanup
* Debug messages can be check in the console
* Changed to CURL to query weather servers 
* Timer for Min/Max is not internal 
* Direct conversion of Solar Readiation from Watt to Lux

## What can be learn more if things do not work
Debug mode of the module. 
