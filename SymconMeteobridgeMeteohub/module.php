<?php

if (!defined('vtBoolean')) {
    define('vtBoolean', 0);
    define('vtInteger', 1);
    define('vtFloat', 2);
    define('vtString', 3);
    define('vtArray', 8);
    define('vtObject', 9);
}


	class Meteobridge_Meteohub extends IPSModule

	{

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			//Properties
			$this->RegisterPropertyInteger("SourceID", 0);
			$this->RegisterPropertyString("Server_Address","");
			$this->RegisterPropertyString("WeatherServer","B");
			$this->RegisterPropertyString("User_Name", "");
			$this->RegisterPropertyString("Password", "");
			$this->RegisterPropertyBoolean("Station_ISS",0);
			$this->RegisterPropertyBoolean("Temperature_1", 0);
			//$this->RegisterPropertyBoolean("Temperature_2", 0);
			//$this->RegisterPropertyBoolean("Temperature_3", 0);
			$this->RegisterPropertyBoolean("Rain", 0);
			$this->RegisterPropertyBoolean("UV", 0);
			$this->RegisterPropertyBoolean("Solar_Radiation", 0);
			$this->RegisterPropertyInteger("Soil_Sensor_1", 0);
			$this->RegisterPropertyInteger("Soil_Sensor_2", 0);
			$this->RegisterPropertyInteger("Soil_Sensor_3", 0);
			$this->RegisterPropertyInteger("Soil_Sensor_4", 0);
			$this->RegisterPropertyBoolean("Leaf_Wetness_1", 0);
			$this->RegisterPropertyBoolean("Evaporation", 0);
			$this->RegisterPropertyBoolean("Statistics", 0);
			$this->RegisterPropertyString("Forecast", "F_OFF");
			$this->RegisterPropertyBoolean("Wind", 0);
			//$this->RegisterPropertyInteger("WebFrontInstanceID", 0);
			//$this->RegisterPropertyBoolean("PushMsgAktiv", false);
			$this->RegisterPropertyInteger("Timer", 0);
			$this->RegisterPropertyInteger("WarningTimer", 0);
			$this->RegisterPropertyBoolean("Debug", 0);

			//Creation of Weather Warning Variables
			//$this->RegisterVariableFloat('Warning_Wind_Gust', $this->Translate('_Warning Wind Gust'), "~WindSpeed.ms");
			//$this->RegisterVariableFloat('Warning_Wind_Speed', $this->Translate('_Warning Wind Speed'), "~WindSpeed.ms");
			//$this->RegisterVariableFloat('Warning_Sensor1_Temperature', $this->Translate('_Warning Sensor 1 Temperature'), "~Temperature");

			//Component sets timer, but default is OFF
			$this->RegisterTimer("UpdateTimer",0,"MHS_SyncStation(\$_IPS['TARGET']);");
			$this->RegisterTimer("WarningTimer",0,"MHS_WeatherWarning(\$_IPS['TARGET']);");
			$this->RegisterTimer("WeatherStatistics", 0, "MHS_Statistics(\$_IPS['TARGET']);");

		}

		public function ApplyChanges() {

			//Never delete this line!
			parent::ApplyChanges();


		        //Timers Update - if greater than 0 = On

				$TimerMS = $this->ReadPropertyInteger("Timer") * 1000;
				$this->SetTimerInterval("UpdateTimer",$TimerMS);

				//Warning Timer

				$WarningTimerMS = $this->ReadPropertyInteger("WarningTimer") * 1000;
				$this->SetTimerInterval("WarningTimer",$WarningTimerMS);

				//Statics Timer Creation - On - Off

				////$sourceID = $this->ReadPropertyInteger("SourceID");


				//Creation of Custom Variables

				if (IPS_VariableProfileExists("MHS.Solarradiation") == false){
					IPS_CreateVariableProfile("MHS.Solarradiation", 2);
					IPS_SetVariableProfileValues("MHS.Solarradiation", 0, 0, 1);
					IPS_SetVariableProfileText("MHS.Solarradiation", "", " W/qm");
					IPS_SetVariableProfileIcon("MHS.Solarradiation",  "Sun");
				}

				if (IPS_VariableProfileExists("MHS.SoilMoisture") == false){
					IPS_CreateVariableProfile("MHS.SoilMoisture", 1);
					IPS_SetVariableProfileValues("MHS.SoilMoisture", 0, 0, 1);
					IPS_SetVariableProfileText("MHS.SoilMoisture", "", " cb");
					IPS_SetVariableProfileIcon("MHS.SoilMoisture",  "Drops");
				}

				if (IPS_VariableProfileExists("MHS.Windspeed_Text") == false){
					IPS_CreateVariableProfile("MHS.Windspeed_Text", 2);
					IPS_SetVariableProfileValues("MHS.Windspeed_Text", 0, 0, 1);
					IPS_SetVariableProfileDigits("MHS.Windspeed_Text", 1);
					IPS_SetVariableProfileIcon("MHS.Windspeed_Text",  "WindSpeed");
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 0, "0 - Stille", "",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 0.3, "1 - schwacher Wind","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 1.6, "2 - schwacher Wind","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 3.4, "3 - schwacher Wind","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 5.5, "4 - maesiger Wind","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 8.0, "5 - frischer Wind","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 10.8, "6 - starker Wind","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 13.9, "7 - starker Wind","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 17.2, "8 - Sturm","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 20.8, "9 - starker Sturm","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 24.5, "10 - schwerer Sturm","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 28.5, "11 - orkanartiger Sturm","",-1);
					IPS_SetVariableProfileAssociation("MHS.Windspeed_Text", 32.7, "12 - Orkan","",-1);
				}

				if (IPS_VariableProfileExists("MHS.Evaporation") == false){
					IPS_CreateVariableProfile("MHS.Evaporation", 2);
					IPS_SetVariableProfileValues("MHS.Evaporation", 0, 0, 1);
					IPS_SetVariableProfileDigits("MHS.Evaporation", 1);
					IPS_SetVariableProfileText("MHS.Evaporation", "", " mm");
					IPS_SetVariableProfileIcon("MHS.Evaporation",  "Climate");
				}


				//Station variables

				$vpos = 20;
				$this->MaintainVariable('Station_Temperature', $this->Translate('Station Temperature'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_Humidity', $this->Translate('Station Humidity'), vtInteger, "~Humidity", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_Dewpoint', $this->Translate('Station Dew Point'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_AirPressure', $this->Translate('Station Air Pressure'), vtInteger, "~AirPressure", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_SeaPressure', $this->Translate('Station Sealevel Air Pressure'), vtInteger, "~AirPressure", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_LowBat', $this->Translate('Station Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
        		$this->MaintainVariable('Station_Error', $this->Translate('Station Error'), vtBoolean, "", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);

				//Temperature sensor 1 variables
				$vpos = 30;
				$this->MaintainVariable('Sensor1_Temperature', $this->Translate('Sensor 1 Temperature'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Temperature_1") == 1);
				$this->MaintainVariable('Sensor1_Humidity', $this->Translate('Sensor 1 Humidity'), vtInteger, "~Humidity", $vpos++, $this->ReadPropertyBoolean("Temperature_1") == 1);
				$this->MaintainVariable('Sensor1_Dewpoint', $this->Translate('Sensor 1 Dew Point'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Temperature_1") == 1);
				$this->MaintainVariable('Sensor1_LowBat', $this->Translate('Sensor 1 Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Temperature_1") == 1);

				//Wind sensor variables
				$vpos = 40;
				$this->MaintainVariable('Wind_Direction', $this->Translate('Wind Direction'), vtFloat, "~WindDirection.F", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Direction_Text', $this->Translate('Wind Direction Text'), vtFloat, "~WindDirection.Text", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Gust', $this->Translate('Wind Gust'), vtFloat, "~WindSpeed.ms", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Speed', $this->Translate('Wind Speed'), vtFloat, "~WindSpeed.ms", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Gust_KmH', $this->Translate('Wind Gust km/h'), vtFloat, "~WindSpeed.kmh", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Speed_KmH', $this->Translate('Wind Speed km/h'), vtFloat, "~WindSpeed.kmh", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Speed_Text', $this->Translate('Wind Speed Text'), vtFloat, "MHS.Windspeed_Text", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Chill', $this->Translate('Wind Chill'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_LowBat', $this->Translate('Wind Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);

				//Rain sensor variables
				$vpos = 50;
				$this->MaintainVariable('Rain_Rate', $this->Translate('Rain Rate'), vtFloat, "~Rainfall", $vpos++, $this->ReadPropertyBoolean("Rain") == 1);
				$this->MaintainVariable('Rain_Total', $this->Translate('Rain Total'), vtFloat, "~Rainfall", $vpos++, $this->ReadPropertyBoolean("Rain") == 1);
				$this->MaintainVariable('Rain_Delta', $this->Translate('Rain Delta'), vtFloat, "~Rainfall", $vpos++, $this->ReadPropertyBoolean("Rain") == 1);
				//$this->MaintainVariable('Rain_LowBat', $this->Translate('Rain Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Rain") == 1);

				//UV sensor variables
				$vpos = 60;
				$this->MaintainVariable('UV_Index', $this->Translate('UV Index'), vtInteger, "~UVIndex", $vpos++, $this->ReadPropertyBoolean("UV") == 1);

				//Solar Radiation sensor variables
				$vpos = 70;
				$this->MaintainVariable('Solar_Radiation', $this->Translate('Solar Radiation'), vtFloat, "MHS.Solarradiation", $vpos++, $this->ReadPropertyBoolean("Solar_Radiation") == 1);
				$this->MaintainVariable('Solar_Radiation_Lux', $this->Translate('Solar Radiation Lux'), vtFloat, "~Illumination.F", $vpos++, $this->ReadPropertyBoolean("Solar_Radiation") == 1);

				//Soil Moisture and Temperature sensor variables
				$vpos = 80;
				$this->MaintainVariable('Soil_Moisture1', $this->Translate('Soil Moisture 1'), vtInteger, "MHS.SoilMoisture", $vpos++, $this->ReadPropertyInteger("Soil_Sensor_1") == 1 OR $this->ReadPropertyInteger("Soil_Sensor_1") == 3);
				$this->MaintainVariable('Soil_Temperature1', $this->Translate('Soil Temperature 1'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyInteger("Soil_Sensor_1") == 2 OR $this->ReadPropertyInteger("Soil_Sensor_1") == 3);
				$this->MaintainVariable('Soil_Moisture2', $this->Translate('Soil Moisture 2'), vtInteger, "MHS.SoilMoisture", $vpos++, $this->ReadPropertyInteger("Soil_Sensor_2") == 1 OR $this->ReadPropertyInteger("Soil_Sensor_2") == 3);
				$this->MaintainVariable('Soil_Temperature2', $this->Translate('Soil Temperature 2'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyInteger("Soil_Sensor_2") == 2 OR $this->ReadPropertyInteger("Soil_Sensor_2") == 3);
				$this->MaintainVariable('Soil_Moisture3', $this->Translate('Soil Moisture 3'), vtInteger, "MHS.SoilMoisture", $vpos++, $this->ReadPropertyInteger("Soil_Sensor_3") == 1 OR $this->ReadPropertyInteger("Soil_Sensor_3") == 3);
				$this->MaintainVariable('Soil_Temperature3', $this->Translate('Soil Temperature 3'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyInteger("Soil_Sensor_3") == 2 OR $this->ReadPropertyInteger("Soil_Sensor_3") == 3);
				$this->MaintainVariable('Soil_Moisture4', $this->Translate('Soil Moisture 4'), vtInteger, "MHS.SoilMoisture", $vpos++, $this->ReadPropertyInteger("Soil_Sensor_4") == 1 OR $this->ReadPropertyInteger("Soil_Sensor_4") == 3);
				$this->MaintainVariable('Soil_Temperature4', $this->Translate('Soil Temperature 4'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyInteger("Soil_Sensor_4") == 2 OR $this->ReadPropertyInteger("Soil_Sensor_4") == 3);
				//$this->MaintainVariable('Soil_Moisture1_LowBat', $this->Translate('Soil Moisture1 Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Solar_Radiation") == 1);


				//Leaf Wetness 1 sensor variables
				$vpos = 90;
				$this->MaintainVariable('Leaf_Wetness1', $this->Translate('Leaf Wetness 1'), vtFloat, "", $vpos++, $this->ReadPropertyBoolean("Leaf_Wetness_1") == 1);
				
				//Evaporation calculation from Vantage
				$this->MaintainVariable('Evaporation', $this->Translate('Evaporation'), vtFloat, "MHS.Evaporation", $vpos++, $this->ReadPropertyBoolean("Evaporation") == 1);

				//Weather Forecast
				$this->MaintainVariable('Forecast', $this->Translate('Forecast'), vtString, "", $vpos++, $this->ReadPropertyString("Forecast") != "F_OFF");

				//Warning Variables
				$this->MaintainVariable('Warning_Wind_Gust', $this->Translate('_Warning Wind Gust'), vtFloat, "~WindSpeed.ms", 10, $this->ReadPropertyInteger("WarningTimer") > 0);
				$this->MaintainVariable('Warning_Wind_Speed', $this->Translate('_Warning Wind Speed'), vtFloat, "~WindSpeed.ms", 11, $this->ReadPropertyInteger("WarningTimer") > 0);
				$this->MaintainVariable('Warning_Wind_Gust_KmH', $this->Translate('_Warning Wind Gust km/h'), vtFloat, "~WindSpeed.kmh", 10, $this->ReadPropertyInteger("WarningTimer") > 0);
				$this->MaintainVariable('Warning_Wind_Speed_KmH', $this->Translate('_Warning Wind Speed km/h'), vtFloat, "~WindSpeed.kmh", 11, $this->ReadPropertyInteger("WarningTimer") > 0);

				$this->SetStatisticsTimer();

		}

		public function SetStatisticsTimer() {
			
			if ($this->ReadPropertyBoolean("Statistics") == 1) {
					//$this->Statistics(); // get current data
					
					//$this->SetTimerInterval("WeatherStatistics", 86400000);
					$CurrentTimer = $this->GetTimerInterval("WeatherStatistics");
					//if ($CurrentTimer == 0) {
					$now = new DateTime();
					$target = new DateTime();
					$now->getTimestamp();
					$NewTime = "23:59:00";
	
					if ($NewTime < date("H:i:s")) {
						$target->modify('+1 day');
					}
					//$nextHour = (intval($now->format('H')) + 1) % 24;
					$target->setTime(23, 59, 00);
					$diff = $target->getTimestamp() - $now->getTimestamp();
					$EvaTimer = $diff * 1000;
					$this->SetTimerInterval('WeatherStatistics', $EvaTimer);
					//$this->SetTimerInterval("QueryAWATTAR",3600000);
					//}
			} else if ($this->ReadPropertyBoolean("Statistics") == 0) {
				$this->SetTimerInterval("WeatherStatistics", 0);
			}
		}



		//Fetch data from Station

		public function SyncStation() {

			//$Debug = $this->ReadPropertyBoolean("Debug");

			$Server_Address = $this->ReadPropertyString("Server_Address");
			$User_Name = $this->ReadPropertyString("User_Name");
			$Password = $this->ReadPropertyString("Password");

			if($this->ReadPropertyString("WeatherServer") == "B") {
				//$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi');
				$url = 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_TIMEOUT, 5);
				$data = curl_exec($ch);
				$xml = simplexml_load_string($data);
				//var_dump($xml);
			}

			elseif($this->ReadPropertyString("WeatherServer") == "H") {
				$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/meteolog.cgi?mode=data&type=xml&quotes=1');
			}

			if (isset($xml)) {

				If ($this->ReadPropertyBoolean("Station_ISS") == 1)	{
					
					//$THB_XML = $xml->THB;
					////$sourceID = $this->ReadPropertyInteger("SourceID");
					if (isset($xml->THB['temp'])) {
						$THB_Temp = ($xml->THB['temp']);
						$this->SendDebug('Station Temperature', (float)$THB_Temp . " C", 0);
						SetValue($this->GetIDForIdent("Station_Temperature"), (float)$THB_Temp);
						SetValue($this->GetIDForIdent("Station_Error"), 0);
					}
					else {
						SetValue($this->GetIDForIdent("Station_Error"), 1);
						//WFC_PushNotification(ReadPropertyInteger(WebFrontInstanceID), 'Meteobridge nicht erreichbar', 'Batterie leer, WLAN weg', '', 0);
					}
					
					if (isset($xml->THB['hum']))	{
						$THB_Hum = ($xml->THB['hum']);
						$this->SendDebug('Station Humidity', (int)$THB_Hum , 0);
						SetValue($this->GetIDForIdent("Station_Humidity"), (integer)$THB_Hum);
					}

					if (isset($xml->THB['dew']))	{
						$THB_Dew = ($xml->THB['dew']);
						$this->SendDebug('Station Dewpoint', (float)$THB_Dew . " C", 0);
						SetValue($this->GetIDForIdent("Station_Dewpoint"), (float)$THB_Dew);
					}

					if (isset($xml->THB['press'])) {
						$THB_Pressure = ($xml->THB['press']);
						$this->SendDebug('Station Airpressure', (int)$THB_Pressure, 0);
						SetValue($this->GetIDForIdent("Station_AirPressure"), (integer)$THB_Pressure);
					}

					if (isset($xml->THB['seapress'])) {
						$THB_SeaPressure = ($xml->THB['seapress']);
						$this->SendDebug('Station Sea Pressure', (int)$THB_SeaPressure, 0);
						SetValue($this->GetIDForIdent("Station_SeaPressure"), (integer)$THB_SeaPressure);
					}

					if (isset($xml->THB['lowbat'])) {
						$THB_Lowbat = (!$xml->THB['lowbat']);
						SetValue($this->GetIDForIdent("Station_LowBat"), (bool)$THB_Lowbat);
					}

				}

				If ($this->ReadPropertyBoolean("Temperature_1") == 1) {
					//$TH_XML = $xml->TH;
					////$sourceID = $this->ReadPropertyInteger("SourceID");

					if (isset($xml->TH['temp']))	{
						$TH_Temp = ($xml->TH['temp']);
						$this->SendDebug('Sensor 1 Temperature', (float)$TH_Temp . " C", 0);
						SetValue($this->GetIDForIdent("Sensor1_Temperature"), (float)$TH_Temp);
					}

					if (isset($xml->TH['hum'])) {
						$TH_Hum = ($xml->TH['hum']);
						$this->SendDebug('Sensor 1 Humidity', (float)$TH_Hum, 0);
						SetValue($this->GetIDForIdent("Sensor1_Humidity"), (float)$TH_Hum);
					}

					if (isset($xml->TH['dew'])) {
						$TH_Dew = ($xml->TH['dew']);
						$this->SendDebug('Sensor 1 Dew Point', (float)$TH_Dew . " C", 0);
						SetValue($this->GetIDForIdent("Sensor1_Dewpoint"), (float)$TH_Dew);
					}

					if (isset($xml->TH['lowbat'])) {
						$TH_Lowbat = (!$xml->TH['lowbat']);
						SetValue($this->GetIDForIdent("Sensor1_LowBat"), (bool)$TH_Lowbat);
					}

				}


				If ($this->ReadPropertyBoolean("Wind") == 1) {
					//$Wind_XML = $xml->WIND;
					////$sourceID = $this->ReadPropertyInteger("SourceID");

					if (isset($xml->WIND['dir'])) {
						$Wind_Dir = ($xml->WIND['dir']);
						$this->SendDebug('Wind Direction', trim($Wind_Dir), 0);
						SetValue($this->GetIDForIdent("Wind_Direction"), (float)$Wind_Dir);
					}

					if (isset($xml->WIND['dir'])) {
						$Wind_Dir = ($xml->WIND['dir']);
						SetValue($this->GetIDForIdent("Wind_Direction_Text"), (float)$Wind_Dir);
					}

					if (isset($xml->WIND['gust'])) {
						$Wind_Gust = ($xml->WIND['gust']);
						$this->SendDebug('Wind Gust', $Wind_Gust . "m/s", 0);
						SetValue($this->GetIDForIdent("Wind_Gust"), (float)$Wind_Gust);
						$WindGustKmH = $Wind_Gust * 3.6;
						$this->SendDebug('Wind Gust KmH', $WindGustKmH . "KmH", 0);
						SetValue($this->GetIDForIdent("Wind_Gust_KmH"), (float)$WindGustKmH);
					}

					if (isset($xml->WIND['wind'])) {
						$Wind_Speed = ($xml->WIND['wind']);
						$this->SendDebug('Wind Speed', $Wind_Speed. "m/s", 0);
						SetValue($this->GetIDForIdent("Wind_Speed"), (float)$Wind_Speed);
						$WindSpeedKmH = $Wind_Speed * 3.6;
						$this->SendDebug('Wind Speed KmH', $WindSpeedKmH . "KmH", 0);
						SetValue($this->GetIDForIdent("Wind_Speed_KmH"), (float)$WindSpeedKmH);
					}

					if (isset($xml->WIND['wind'])) {
						$Wind_Speed = ($xml->WIND['wind']);
						SetValue($this->GetIDForIdent("Wind_Speed_Text"), (float)$Wind_Speed);
					}

					if (isset($xml->WIND['chill'])) {
						$Wind_Chill = ($xml->WIND['chill']);
						$this->SendDebug('Wind Chill', $Wind_Chill . "m/s", 0);
						SetValue($this->GetIDForIdent("Wind_Chill"), (float)$Wind_Chill);
					}

					if (isset($xml->WIND['lowbat'])) {
						$Wind_Lowbat = (!$xml->WIND['lowbat']);
						SetValue($this->GetIDForIdent("Wind_LowBat"), (bool)$Wind_Lowbat);
					}

				}


				If ($this->ReadPropertyBoolean("Rain") == 1) {
					//$Rain_XML = $xml->RAIN;
					//$sourceID = $this->ReadPropertyInteger("SourceID");

					if (isset($xml->RAIN['rate'])) {
						$Rain_Rate = ($xml->RAIN['rate']);
						$this->SendDebug('Rain Rate', trim($Rain_Rate) . "mm", 0);
						SetValue($this->GetIDForIdent("Rain_Rate"), (float)$Rain_Rate);
					}

					if (isset($Rain_XML['total'])) {
						$Rain_Total = ($xml->RAIN['total']);
						$this->SendDebug('Rain Total', trim($Rain_Total) . "mm", 0);
						SetValue($this->GetIDForIdent("Rain_Total"), (float)$Rain_Total);
					}

					if (isset($xml->RAIN['delta'])) {
						$Rain_Delta = ($xml->RAIN['delta']);
						$this->SendDebug('Rain Delta', trim($Rain_Delta). "mm", 0);
						SetValue($this->GetIDForIdent("Rain_Delta"), (float)$Rain_Delta);
					}

				}


					If ($this->ReadPropertyBoolean("UV") == 1) {
						//$UV_XML = $xml->UV;
						//$sourceID = $this->ReadPropertyInteger("SourceID");

						if (isset($xml->UV['index'])) {
							$UV_Index = ($xml->UV['index']);
							$this->SendDebug('UV Index', trim($UV_Index), 0);
							SetValue($this->GetIDForIdent("UV_Index"), (integer)$UV_Index);
						}

						//$UV_Lowbat = (!$UV_XML['lowbat']);
						//SetValue($this->GetIDForIdent("UV_LowBat"), (bool)$UV_Lowbat);

					}

					If ($this->ReadPropertyBoolean("Solar_Radiation") == 1)	{
						//$Solar_Radiation_XML = $xml->SOL;
						//$sourceID = $this->ReadPropertyInteger("SourceID");

						if (isset($xml->SOL['rad'])) {
							$Solar_Radiation = ($xml->SOL['rad']);
							SetValue($this->GetIDForIdent("Solar_Radiation"), (integer)$Solar_Radiation);

							$Lux = $Solar_Radiation * 0.13 * 1000;
							SetValue($this->GetIDForIdent("Solar_Radiation_Lux"), (int)$Lux);
						}

					}

					If (($this->ReadPropertyInteger("Soil_Sensor_1") == 1) OR ($this->ReadPropertyInteger("Soil_Sensor_1") == 3)) {

						$Server_Address = $this->ReadPropertyString("Server_Address");
						$User_Name = $this->ReadPropertyString("User_Name");
						$Password = $this->ReadPropertyString("Password");

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th10hum-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Soil_Moisture1 = curl_exec($ch);
						$this->SendDebug('Soil Moisture 4', trim($Soil_Moisture1) . " cb", 1);
						SetValue($this->GetIDForIdent("Soil_Moisture1"), (float)trim($Soil_Moisture1));
						curl_close($ch);

					}

					If (($this->ReadPropertyInteger("Soil_Sensor_1") == 2) OR ($this->ReadPropertyInteger("Soil_Sensor_1") == 3)) {

						$Server_Address = $this->ReadPropertyString("Server_Address");
						$User_Name = $this->ReadPropertyString("User_Name");
						$Password = $this->ReadPropertyString("Password");

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th10temp-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Soil_Temperature1 = curl_exec($ch);
						$this->SendDebug('Soil Temperature 1', trim($Soil_Temperature1) . " C", 0);
						SetValue($this->GetIDForIdent("Soil_Temperature1"), (float)trim($Soil_Temperature1));
						curl_close($ch);

					}

					If (($this->ReadPropertyInteger("Soil_Sensor_2") == 1) OR ($this->ReadPropertyInteger("Soil_Sensor_2") == 3)) {

						$Server_Address = $this->ReadPropertyString("Server_Address");
						$User_Name = $this->ReadPropertyString("User_Name");
						$Password = $this->ReadPropertyString("Password");

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th11hum-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Soil_Moisture2 = curl_exec($ch);
						$this->SendDebug('Soil Moisture 2', trim($Soil_Moisture2) . " cb", 0);
						SetValue($this->GetIDForIdent("Soil_Moisture2"), (float)trim($Soil_Moisture2));
						curl_close($ch);

					}

					If (($this->ReadPropertyInteger("Soil_Sensor_2") == 2) OR ($this->ReadPropertyInteger("Soil_Sensor_2") == 3)) {

						$Server_Address = $this->ReadPropertyString("Server_Address");
						$User_Name = $this->ReadPropertyString("User_Name");
						$Password = $this->ReadPropertyString("Password");

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th11temp-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Soil_Temperature2 = curl_exec($ch);
						$this->SendDebug('Soil Temperature 2', trim($Soil_Temperature2) . " C", 0);
						SetValue($this->GetIDForIdent("Soil_Temperature2"), (float)trim($Soil_Temperature2));
						curl_close($ch);

					}

					If (($this->ReadPropertyInteger("Soil_Sensor_3") == 1) OR ($this->ReadPropertyInteger("Soil_Sensor_3") == 3)) {

						$Server_Address = $this->ReadPropertyString("Server_Address");
						$User_Name = $this->ReadPropertyString("User_Name");
						$Password = $this->ReadPropertyString("Password");

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th12hum-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Soil_Moisture3 = curl_exec($ch);
						$this->SendDebug('Soil Moisture 3', trim($Soil_Moisture3) . " cb", 0);
						SetValue($this->GetIDForIdent("Soil_Moisture3"), (float)trim($Soil_Moisture3));
						curl_close($ch);

					}

					If (($this->ReadPropertyInteger("Soil_Sensor_3") == 2) OR ($this->ReadPropertyInteger("Soil_Sensor_3") == 3)) {

						$Server_Address = $this->ReadPropertyString("Server_Address");
						$User_Name = $this->ReadPropertyString("User_Name");
						$Password = $this->ReadPropertyString("Password");

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th12temp-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Soil_Temperature3 = curl_exec($ch);
						$this->SendDebug('Soil Temperature 3', trim($Soil_Temperature3) . " C", 0);
						SetValue($this->GetIDForIdent("Soil_Temperature3"), (float)trim($Soil_Temperature3));
						curl_close($ch);

					}

					If (($this->ReadPropertyInteger("Soil_Sensor_4") == 1) OR ($this->ReadPropertyInteger("Soil_Sensor_4") == 3)) {

						$Server_Address = $this->ReadPropertyString("Server_Address");
						$User_Name = $this->ReadPropertyString("User_Name");
						$Password = $this->ReadPropertyString("Password");

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th13hum-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Soil_Moisture4 = curl_exec($ch);
						$this->SendDebug('Soil Moisture 4', trim($Soil_Moisture4) . " cb", 0);
						SetValue($this->GetIDForIdent("Soil_Moisture4"), (float)trim($Soil_Moisture4));
						curl_close($ch);

					}

					If (($this->ReadPropertyInteger("Soil_Sensor_4") == 2) OR ($this->ReadPropertyInteger("Soil_Sensor_4") == 3)) {

						$Server_Address = $this->ReadPropertyString("Server_Address");
						$User_Name = $this->ReadPropertyString("User_Name");
						$Password = $this->ReadPropertyString("Password");

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th13temp-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Soil_Temperature4 = curl_exec($ch);
						$this->SendDebug('Soil Temperature 4', trim($Soil_Temperature4)." C", 0);
						SetValue($this->GetIDForIdent("Soil_Temperature4"), (float)trim($Soil_Temperature4));
						curl_close($ch);

					}


					If ($this->ReadPropertyBoolean("Leaf_Wetness_1") == 1) {

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th16hum-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$leaf = curl_exec($ch);
						$this->SendDebug('Leafwetness', trim($leaf),0);
						SetValue($this->GetIDForIdent("Leaf_Wetness1"), (float)trim($leaf));
						curl_close($ch);

						//$Leaf_Wetness1 = (!$Leaf_Wetness1_XML['lowbat']);
						//SetValue($this->GetIDForIdent("Leaf_Wetness1_LowBat"), (bool)$Leaf_Wetness1_Lowbat);

					}
					

					If ($this->ReadPropertyBoolean("Evaporation") == 1)	{

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[sol0evo-act]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$evo = curl_exec($ch);
						$this->SendDebug('Evaporation', trim($evo),0);
						SetValue($this->GetIDForIdent("Evaporation"), (float)trim($evo));
						curl_close($ch);

					}

					if($this->ReadPropertyString("Forecast") == "F_DE")	{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[forecast-textdehtml]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Forecast = curl_exec($ch);
						SetValue($this->GetIDForIdent("Forecast"), (string)trim($Forecast));
						curl_close($ch);
					}
					elseif($this->ReadPropertyString("Forecast") == "F_EN")	{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[forecast-text]');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3);
						$Forecast = curl_exec($ch);
						SetValue($this->GetIDForIdent("Forecast"), (string)trim($Forecast));
						curl_close($ch);
					}
					elseif($this->ReadPropertyString("Forecast") == "F_OFF") {
						//do nothing
					}

			}
			
		}

		// Section run on a second more frequent timer intended for weather warnings

		public function WeatherWarning() {

			$Server_Address = $this->ReadPropertyString("Server_Address");
			$User_Name = $this->ReadPropertyString("User_Name");
			$Password = $this->ReadPropertyString("Password");

			if($this->ReadPropertyString("WeatherServer") == "B") {
				$url = 'http://' . $User_Name . ':' . $Password . '@' . $Server_Address . '/cgi-bin/livedataxml.cgi';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_TIMEOUT, 5);
				$data = curl_exec($ch);
				$xml = simplexml_load_string($data);
			}
			elseif($this->ReadPropertyString("WeatherServer") == "H") {
				$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/meteolog.cgi?mode=data&type=xml&quotes=1');
			}

			if (isset($xml)) {
				//$Wind_XML = $xml->WIND;
				//$sourceID = $this->ReadPropertyInteger("SourceID");

				if (isset($xml->WIND['gust'])) {
					$Wind_Gust = ($xml->WIND['gust']);
					SetValue($this->GetIDForIdent("Warning_Wind_Gust"), (float)$Wind_Gust);
					$WindGustKmH = $Wind_Gust * 3.6;
					$this->SendDebug('Warning Wind Gust KmH', $WindGustKmH . "KmH", 0);
					SetValue($this->GetIDForIdent("Warning_Wind_Gust_KmH"), (float)$WindGustKmH);
				}

				if (isset($xml->WIND['wind'])) {
					$Wind_Speed = ($xml->WIND['wind']);
					SetValue($this->GetIDForIdent("Warning_Wind_Speed"), (float)$Wind_Speed);
					$WindSpeedKmH = $Wind_Speed * 3.6;
					$this->SendDebug('Warning Wind Speed KmH', $WindSpeedKmH . "KmH", 0);
					SetValue($this->GetIDForIdent("Warning_Wind_Speed_KmH"), (float)$WindSpeedKmH);
				}
				
			}

		}

		public function Statistics() {

			//Create Statics Variables

			$vpos = 200;

			$this->MaintainVariable('Stat_Temp_S1_Min', $this->Translate('Statistic Temperature Sensor1 Min'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Temp_S1_Max', $this->Translate('Statistic Temperature Sensor1 Max'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Hum_S1_Min', $this->Translate('Statistic Humidity Sensor1 Min'), vtInteger, "~Humidity", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Hum_S1_Max', $this->Translate('Statistic Humidity Sensor1 Max'), vtInteger, "~Humidity", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Pres_Min', $this->Translate('Statistic Pressure Min'), vtInteger, "~AirPressure", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Pres_Max', $this->Translate('Statistic Pressure Max'), vtInteger, "~AirPressure", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Dew_S1_Min', $this->Translate('Statistic Dewpoint Sensor1 Min'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Dew_S1_Max', $this->Translate('Statistic Dewpoint Sensor1 Max'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Wind_Max', $this->Translate('Statistic Wind Max'), vtFloat, "~WindSpeed.ms", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Gust_Max', $this->Translate('Statistic Gusts Max'), vtFloat, "~WindSpeed.ms", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Windchill_Min', $this->Translate('Statistic Windchill Min'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Windchill_Max', $this->Translate('Statistic Windchill Max'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_UV', $this->Translate('Statistic UV'), vtInteger, "~UVIndex", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Sol', $this->Translate('Statistic Solar Radiation'), vtFloat, "MHS.Solarradiation", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Evo', $this->Translate('Statistic Evaporation'), vtFloat, "MHS.Evaporation", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);
			$this->MaintainVariable('Stat_Rain', $this->Translate('Statistic Rain'), vtFloat, "~Rainfall", $vpos++, $this->ReadPropertyBoolean("Statistics") == 1);

			// Query Meteobrdige for data

			$Server_Address = $this->ReadPropertyString("Server_Address");
			$User_Name = $this->ReadPropertyString("User_Name");
			$Password = $this->ReadPropertyString("Password");

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th0temp-dmin]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Temp_S1_Min = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Temp_S1_Min"), (float)trim($Stat_Temp_S1_Min));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th0temp-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Temp_S1_Max = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Temp_S1_Max"), (float)trim($Stat_Temp_S1_Max));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th0hum-dmin]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Hum_S1_Min = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Hum_S1_Min"), (integer)trim($Stat_Hum_S1_Min));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th0hum-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Hum_S1_Max = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Hum_S1_Max"), (integer)trim($Stat_Hum_S1_Max));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[thb0press-dmin]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Pres_Min = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Pres_Min"), (float)trim($Stat_Pres_Min));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[thb0press-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Pres_Max = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Pres_Max"), (float)trim($Stat_Pres_Max));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th0dew-dmin]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Dew_S1_Min = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Dew_S1_Min"), (float)trim($Stat_Dew_S1_Min));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[th0dew-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Dew_S1_Max = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Dew_S1_Max"), (float)trim($Stat_Dew_S1_Max));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[wind0avgwind-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Wind_Max = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Wind_Max"), (float)trim($Stat_Wind_Max));
				curl_close($ch);


			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[wind0wind-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Gust_Max = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Gust_Max"), (float)trim($Stat_Gust_Max));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[wind0chill-dmin]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Windchill_Min = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Windchill_Min"), (float)trim($Stat_Windchill_Min));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[wind0chill-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Windchill_Max = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Windchill_Max"), (float)trim($Stat_Windchill_Max));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[uv0-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_UV = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_UV"), (integer)trim($Stat_UV));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[sol0rad-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Sol = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Sol"), (float)trim($Stat_Sol));
				curl_close($ch);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[sol0evo-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Evo = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Evo"), (float)trim($Stat_Evo));
				curl_close($ch);


			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/template.cgi?template=[rain0total-dmax]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$Stat_Rain = curl_exec($ch);
				SetValue($this->GetIDForIdent("Stat_Rain"), (float)trim($Stat_Rain));
				curl_close($ch);

			$this->SetStatisticsTimer();

		}

	}
