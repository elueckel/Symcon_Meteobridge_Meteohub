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
			$this->RegisterPropertyBoolean("Wind", 0);
			$this->RegisterPropertyInteger("Timer", 10);
			$this->RegisterPropertyBoolean("Debug", 0);
			
									
			//Component sets timer, but default is OFF
			$this->RegisterTimer("UpdateTimer",0,"MHS_SyncStation(\$_IPS['TARGET']);");			
		}
	
		public function ApplyChanges()
		{
			
			//Never delete this line!
			parent::ApplyChanges();
			
									
		        //Timer Update - if greater than 0 = On
				
				$TimerMS = $this->ReadPropertyInteger("Timer") * 1000;
				
        		$this->SetTimerInterval("UpdateTimer",$TimerMS);
    			
				$vpos = 0;
				
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
				$this->MaintainVariable('Solar_Radiation', $this->Translate('Solar Radiation'), vtFloat, "", $vpos++, $this->ReadPropertyBoolean("Solar_Radiation") == 1);
				//$this->MaintainVariable('Solar_Radiation_LowBat', $this->Translate('Solar Radiation Low Battery'), vtBoolean, "~Battery", $vpos++, $this->ReadPropertyBoolean("Solar_Radiation") == 1);
				
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
		elseif($this->ReadPropertyString("WeatherServer") == "M")
		{
			//$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/meteolog.cgi?mode=data&type=xml&quotes=1');
			$xml = simplexml_load_file('http://'.$Server_Address.'/meteolog.cgi?mode=data&type=xml&quotes=1');
		}
		
			
		If ($this->ReadPropertyBoolean("Station_ISS") == 1)
		{
			//$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi');
			print_r($xml);
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
			//$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi');
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
			//$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi');
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
			
			$Wind_Chill = ($Wind_XML['chill']);		
			SetValue($this->GetIDForIdent("Wind_Chill"), (float)$Wind_Chill);
			
			$Wind_Lowbat = (!$Wind_XML['lowbat']);		
			SetValue($this->GetIDForIdent("Wind_LowBat"), (bool)$Wind_Lowbat);

		}
		
		
		If ($this->ReadPropertyBoolean("Rain") == 1)
		{
			//$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi');
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
			//$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi');
			$UV_XML = $xml->UV;
			$sourceID = $this->ReadPropertyInteger("SourceID");
			
			$UV_Index = ($UV_XML['index']);
			SetValue($this->GetIDForIdent("UV_Index"), (integer)$UV_Index);
			
			//$UV_Lowbat = (!$UV_XML['lowbat']);		
			//SetValue($this->GetIDForIdent("UV_LowBat"), (bool)$UV_Lowbat);

		}
		
		
		If ($this->ReadPropertyBoolean("Solar_Radiation") == 1)
		{
			//$xml = simplexml_load_file('http://'.$User_Name.':'.$Password.'@'.$Server_Address.'/cgi-bin/livedataxml.cgi');
			$Solar_Radiation_XML = $xml->SOL;
			$sourceID = $this->ReadPropertyInteger("SourceID");
			
			$Solar_Radiation = ($Solar_Radiation_XML['rad']);
			SetValue($this->GetIDForIdent("Solar_Radiation"), (float)$Solar_Radiation);
			
			//$Solar_Radiation_Lowbat = (!$Solar_Radiation_XML['lowbat']);		
			//SetValue($this->GetIDForIdent("Solar_Radiation_LowBat"), (bool)$Solar_Radiation_Lowbat);
			

		}
				
		}
		
		
	}
	
?>
