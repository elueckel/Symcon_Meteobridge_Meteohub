<?

if (!defined('vtBoolean')) {
    define('vtBoolean', 0);
    define('vtInteger', 1);
    define('vtFloat', 2);
    define('vtString', 3);
    define('vtArray', 8);
    define('vtObject', 9);
}


	class Symcon_Meteobridge_Meteohub extends IPSModule
	
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
			$this->RegisterPropertyBoolean("Soil_Moisture_1", 0);
			$this->RegisterPropertyBoolean("Leaf_Wetness_1", 0);
			$this->RegisterPropertyBoolean("Wind", 0);
			$this->RegisterPropertyInteger("Timer", 0);
			$this->RegisterPropertyInteger("WarningTimer", 0);
			$this->RegisterPropertyBoolean("Debug", 0);
			
			//Creation of Weather Warning Variables
			$this->RegisterVariableFloat('Warning_Wind_Gust', $this->Translate('_Warning Wind Gust'), "~WindSpeed.ms");
			$this->RegisterVariableFloat('Warning_Wind_Speed', $this->Translate('_Warning Wind Speed'), "~WindSpeed.ms");
			//$this->RegisterVariableFloat('Warning_Sensor1_Temperature', $this->Translate('_Warning Sensor 1 Temperature'), "~Temperature");
									
			//Component sets timer, but default is OFF
			$this->RegisterTimer("UpdateTimer",0,"MHS_SyncStation(\$_IPS['TARGET']);");
			$this->RegisterTimer("WarningTimer",0,"MHS_WeatherWarning(\$_IPS['TARGET']);");
			
	
		}
	
		public function ApplyChanges()
		{
			
			//Never delete this line!
			parent::ApplyChanges();
			
									
		        //Timers Update - if greater than 0 = On
				
				$TimerMS = $this->ReadPropertyInteger("Timer") * 1000;
				$this->SetTimerInterval("UpdateTimer",$TimerMS);
				
				//Warning Timer
				
				$WarningTimerMS = $this->ReadPropertyInteger("WarningTimer") * 1000;
				$this->SetTimerInterval("WarningTimer",$WarningTimerMS);
    			
				$vpos = 4;
				
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
					
				
				//Station variables 
				
				$this->MaintainVariable('Station_Temperature', $this->Translate('Station Temperature'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_Humidity', $this->Translate('Station Humidity'), vtInteger, "~Humidity", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_Dewpoint', $this->Translate('Station Dew Point'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_AirPressure', $this->Translate('Station Air Pressure'), vtInteger, "~AirPressure", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_SeaPressure', $this->Translate('Station Sealevel Air Pressure'), vtInteger, "~AirPressure", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				$this->MaintainVariable('Station_LowBat', $this->Translate('Station Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Station_ISS") == 1);
				
				//Temperature sensor 1 variables
				$this->MaintainVariable('Sensor1_Temperature', $this->Translate('Sensor 1 Temperature'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Temperature_1") == 1);
				$this->MaintainVariable('Sensor1_Humidity', $this->Translate('Sensor 1 Humidity'), vtInteger, "~Humidity", $vpos++, $this->ReadPropertyBoolean("Temperature_1") == 1);
				$this->MaintainVariable('Sensor1_Dewpoint', $this->Translate('Sensor 1 Dew Point'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Temperature_1") == 1);
				$this->MaintainVariable('Sensor1_LowBat', $this->Translate('Sensor 1 Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Temperature_1") == 1);
				
				//Wind sensor variables
				$this->MaintainVariable('Wind_Direction', $this->Translate('Wind Direction'), vtFloat, "~WindDirection.F", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Direction_Text', $this->Translate('Wind Direction Text'), vtFloat, "~WindDirection.Text", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Gust', $this->Translate('Wind Gust'), vtFloat, "~WindSpeed.ms", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Speed', $this->Translate('Wind Speed'), vtFloat, "~WindSpeed.ms", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				//$this->MaintainVariable('Wind_Speed_Text', $this->Translate('Wind Speed Text'), vtFloat, "MHS.Windspeed_Text", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_Chill', $this->Translate('Wind Chill'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				$this->MaintainVariable('Wind_LowBat', $this->Translate('Wind Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Wind") == 1);
				
				//Rain sensor variables
				$this->MaintainVariable('Rain_Rate', $this->Translate('Rain Rate'), vtFloat, "~Rainfall", $vpos++, $this->ReadPropertyBoolean("Rain") == 1);
				$this->MaintainVariable('Rain_Total', $this->Translate('Rain Total'), vtFloat, "~Rainfall", $vpos++, $this->ReadPropertyBoolean("Rain") == 1);
				$this->MaintainVariable('Rain_Delta', $this->Translate('Rain Delta'), vtFloat, "~Rainfall", $vpos++, $this->ReadPropertyBoolean("Rain") == 1);
				//$this->MaintainVariable('Rain_LowBat', $this->Translate('Rain Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Rain") == 1);
	
				//UV sensor variables
				$this->MaintainVariable('UV_Index', $this->Translate('UV Index'), vtInteger, "~UVIndex", $vpos++, $this->ReadPropertyBoolean("UV") == 1);
				//$this->MaintainVariable('UV_LowBat', $this->Translate('UV Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("UV") == 1);
				
				
				//Solar Radiation sensor variables
				$this->MaintainVariable('Solar_Radiation', $this->Translate('Solar Radiation'), vtFloat, "MHS.Solarradiation", $vpos++, $this->ReadPropertyBoolean("Solar_Radiation") == 1);
				//$this->MaintainVariable('Solar_Radiation_LowBat', $this->Translate('Solar Radiation Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Solar_Radiation") == 1);
				
				//Soil Moisture and Temperature 1 sensor variables
				$this->MaintainVariable('Soil_Moisture1', $this->Translate('Soil Moisture 1'), vtInteger, "MHS.SoilMoisture", $vpos++, $this->ReadPropertyBoolean("Soil_Moisture_1") == 1);
				$this->MaintainVariable('Soil_Temperature1', $this->Translate('Soil Temperature 1'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Soil_Moisture_1") == 1);
				//$this->MaintainVariable('Soil_Moisture1_LowBat', $this->Translate('Soil Moisture1 Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Solar_Radiation") == 1);
								
				
				//Leaf Wetness 1 sensor variables
				$this->MaintainVariable('Leaf_Wetness1', $this->Translate('Leaf Wetness 1'), vtFloat, "", $vpos++, $this->ReadPropertyBoolean("Leaf_Wetness_1") == 1);
				//$this->MaintainVariable('Leaf_Wetness1_LowBat', $this->Translate('Leaf Wetness1 Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Leaf Wetness1") == 1);
				
				//Weather Warning Variables - stays in for now ... just in case it is needed
				//$this->RegisterVariable('Warning_Wind_Gust', $this->Translate('_Warning Wind Gust'), vtFloat, "~WindSpeed.ms", $vpos++, $this->ReadPropertyInteger("WarningTimer") > "0");
				//$this->MaintainVariable('Warning_Wind_Speed', $this->Translate('_Warning Wind Speed'), vtFloat, "~WindSpeed.ms", $vpos++, $this->ReadPropertyInteger("Wind") == 1);
				//$this->MaintainVariable('Warning_Sensor1_Temperature', $this->Translate('_Warning Sensor 1 Temperature'), vtFloat, "~Temperature", $vpos++, $this->ReadPropertyBoolean("Temperature_1") == 1);
				
				
		}
	
		
		//Fetch data from Station
		
		public function SyncStation()
		{
		
			//$Debug = $this->ReadPropertyBoolean("Debug");
		
			$Server_Address = $this->ReadPropertyString("Server_Address");
			$User_Name = $this->ReadPropertyString("User_Name");
			$Password = $this->ReadPropertyString("Password");	
		
			if($this->ReadPropertyString("WeatherServer") == "B") 
			{
				$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi');
			}
			
			elseif($this->ReadPropertyString("WeatherServer") == "H")
			{
				$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/meteolog.cgi?mode=data&type=xml&quotes=1');
			}
		
			
			If ($this->ReadPropertyBoolean("Station_ISS") == 1)
			{
				
				$THB_XML = $xml->THB;
				$sourceID = $this->ReadPropertyInteger("SourceID");
			
				$THB_Temp = ($THB_XML['temp']);
				SetValue($this->GetIDForIdent("Station_Temperature"), (float)$THB_Temp);
						
				$THB_Hum = ($THB_XML['hum']);
				SetValue($this->GetIDForIdent("Station_Humidity"), (integer)$THB_Hum);

				$THB_Dew = ($THB_XML['dew']);			
				SetValue($this->GetIDForIdent("Station_Dewpoint"), (float)$THB_Dew);
			
				$THB_Pressure = ($THB_XML['press']);		
				SetValue($this->GetIDForIdent("Station_AirPressure"), (integer)$THB_Pressure);
			
				$THB_SeaPressure = ($THB_XML['seapress']);		
				SetValue($this->GetIDForIdent("Station_SeaPressure"), (integer)$THB_SeaPressure);
			
				$THB_Lowbat = (!$THB_XML['lowbat']);		
				SetValue($this->GetIDForIdent("Station_LowBat"), (bool)$THB_Lowbat);

			}
		
		
			If ($this->ReadPropertyBoolean("Temperature_1") == 1)
			{
				$TH_XML = $xml->TH;
				$sourceID = $this->ReadPropertyInteger("SourceID");
				
				$TH_Temp = ($TH_XML['temp']);
				SetValue($this->GetIDForIdent("Sensor1_Temperature"), (float)$TH_Temp);
				
				$TH_Hum = ($TH_XML['hum']);
				SetValue($this->GetIDForIdent("Sensor1_Humidity"), (float)$TH_Hum);
				
				$TH_Dew = ($TH_XML['dew']);		
				SetValue($this->GetIDForIdent("Sensor1_Dewpoint"), (float)$TH_Dew);
				
				$TH_Lowbat = (!$TH_XML['lowbat']);		
				SetValue($this->GetIDForIdent("Sensor1_LowBat"), (bool)$TH_Lowbat);

			}
		
		
			If ($this->ReadPropertyBoolean("Wind") == 1)
			{
				$Wind_XML = $xml->WIND;
				$sourceID = $this->ReadPropertyInteger("SourceID");
				
				$Wind_Dir = ($Wind_XML['dir']);
				SetValue($this->GetIDForIdent("Wind_Direction"), (float)$Wind_Dir);
				
				$Wind_Dir = ($Wind_XML['dir']);
				SetValue($this->GetIDForIdent("Wind_Direction_Text"), (float)$Wind_Dir);
					
				$Wind_Gust = ($Wind_XML['gust']);
				SetValue($this->GetIDForIdent("Wind_Gust"), (float)$Wind_Gust);
				
				$Wind_Speed = ($Wind_XML['wind']);		
				SetValue($this->GetIDForIdent("Wind_Speed"), (float)$Wind_Speed);
				
				$Wind_Speed = ($Wind_XML['wind']);		
				SetValue($this->GetIDForIdent("Wind_Speed_Text"), (float)$Wind_Speed);
				
				$Wind_Chill = ($Wind_XML['chill']);		
				SetValue($this->GetIDForIdent("Wind_Chill"), (float)$Wind_Chill);
				
				$Wind_Lowbat = (!$Wind_XML['lowbat']);		
				SetValue($this->GetIDForIdent("Wind_LowBat"), (bool)$Wind_Lowbat);

			}
			
			
			If ($this->ReadPropertyBoolean("Rain") == 1)
			{
				$Rain_XML = $xml->RAIN;
				$sourceID = $this->ReadPropertyInteger("SourceID");
				
				$Rain_Rate = ($Rain_XML['rate']);
				SetValue($this->GetIDForIdent("Rain_Rate"), (float)$Rain_Rate);
				
				$Rain_Total = ($Rain_XML['total']);
				SetValue($this->GetIDForIdent("Rain_Total"), (float)$Rain_Total);
				
				$Rain_Delta = ($Rain_XML['delta']);		
				SetValue($this->GetIDForIdent("Rain_Delta"), (float)$Rain_Delta);
				
				//$Rain_Lowbat = (!$Rain_XML['lowbat']);		
				//SetValue($this->GetIDForIdent("Rain_LowBat"), (bool)$Rain_Lowbat);

			}
			
			
			If ($this->ReadPropertyBoolean("UV") == 1)
			{
				$UV_XML = $xml->UV;
				$sourceID = $this->ReadPropertyInteger("SourceID");
				
				$UV_Index = ($UV_XML['index']);
				SetValue($this->GetIDForIdent("UV_Index"), (integer)$UV_Index);
				
				//$UV_Lowbat = (!$UV_XML['lowbat']);		
				//SetValue($this->GetIDForIdent("UV_LowBat"), (bool)$UV_Lowbat);

			}
			
			If ($this->ReadPropertyBoolean("Solar_Radiation") == 1)
			{
				$Solar_Radiation_XML = $xml->SOL;
				$sourceID = $this->ReadPropertyInteger("SourceID");
				
				$Solar_Radiation = ($Solar_Radiation_XML['index']);
				SetValue($this->GetIDForIdent("Solar_Radiation"), (integer)$Solar_Radiation);
				
				//$Solar_Radiation_Lowbat = (!$Solar_Radiation_XML['lowbat']);		
				//SetValue($this->GetIDForIdent("Solar_Radiation_LowBat"), (bool)$Solar_Radiation_Lowbat);
			}
			
			If ($this->ReadPropertyBoolean("Soil_Moisture_1") == 1)
			{
				$Soil_Moisture1_XML = $xml->SOIL;
				$sourceID = $this->ReadPropertyInteger("SourceID");
				
				$Soil_Moisture1 = ($Soil_Moisture1_XML['humidity']);
				SetValue($this->GetIDForIdent("Soil_Moisture1"), (integer)$Soil_Moisture1);
				
				$Soil_Temperature1 = ($Soil_Moisture1_XML['temp']);
				SetValue($this->GetIDForIdent("Soil_Temperature1"), (float)$Soil_Temperature1);
				
				//$Soil_Temperature1_Lowbat = (!$Soil_Temperature1_XML['lowbat']);		
				//SetValue($this->GetIDForIdent("Soil_Temperature1_LowBat"), (bool)$Soil_Temperature1_Lowbat);
				

			}
		
			
			If ($this->ReadPropertyBoolean("Leaf_Wetness_1") == 1)
			{
				$Leaf_Wetness1_XML = $xml->LEAF;
				$sourceID = $this->ReadPropertyInteger("SourceID");
				
				$Leaf_Wetness1 = ($Leaf_Wetness1_XML['humidity']);
				SetValue($this->GetIDForIdent("Leaf_Wetness1"), (float)$Leaf_Wetness1);
				
				//$Leaf_Wetness1 = (!$Leaf_Wetness1_XML['lowbat']);		
				//SetValue($this->GetIDForIdent("Leaf_Wetness1_LowBat"), (bool)$Leaf_Wetness1_Lowbat);
			
					
			}
			
		}
		
		// Section run on a second more frequent timer intended for weather warnings
		
		public function WeatherWarning()
		{
		
			$Server_Address = $this->ReadPropertyString("Server_Address");
			$User_Name = $this->ReadPropertyString("User_Name");
			$Password = $this->ReadPropertyString("Password");	
		
			if($this->ReadPropertyString("WeatherServer") == "B") 
			{
				$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi');
			}
			elseif($this->ReadPropertyString("WeatherServer") == "H")
			{
				$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/meteolog.cgi?mode=data&type=xml&quotes=1');
			}
			
			/*
			$TH_XML = $xml->TH;
			$sourceID = $this->ReadPropertyInteger("SourceID");
				
			$TH_Temp = ($TH_XML['temp']);
			SetValue($this->GetIDForIdent("Warning_Sensor1_Temperature"), (float)$TH_Temp);
			*/
			$Wind_XML = $xml->WIND;
			$sourceID = $this->ReadPropertyInteger("SourceID");
					
			$Wind_Gust = ($Wind_XML['gust']);
			SetValue($this->GetIDForIdent("Warning_Wind_Gust"), (float)$Wind_Gust);
						
			$Wind_Speed = ($Wind_XML['wind']);		
			SetValue($this->GetIDForIdent("Warning_Wind_Speed"), (float)$Wind_Speed);
			
		}
		
		
	}
	
?>
