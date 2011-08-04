<?php
require_once("api/services/Appointment_x0020_Service.php");
require_once("api/services/Class_x0020_Service.php");
require_once("api/services/Client_x0020_Service.php");
require_once("api/services/Sale_x0020_Service.php");
require_once("api/services/Site_x0020_Service.php");
require_once("api/services/Staff_x0020_Service.php");

abstract class Mindbody_Extractor {
	protected $service;
	protected $sourcecredentials;

	public function __construct($sourcename, $password, $siteIDs) {
		$this->sourcecredentials = new SourceCredentials();
		$this->sourcecredentials->SourceName = $sourcename;
		$this->sourcecredentials->Password = $password;
		$this->sourcecredentials->SiteIDs = $siteIDs;
	}
	
	public static get_all($sourcename, $password, $siteIDs) {
		require_once("services/Appointment_Extractor.php");
		require_once("services/Class_Extractor.php");
		require_once("services/Client_Extractor.php");
		require_once("services/Sale_Extractor.php");
		require_once("services/Site_Extractor.php");
		require_once("services/Staff_Extractor.php");
	
		$result = array();
		foreach(array(
			"Appointment_Extractor",
			"Class_Extractor",
			"Client_Extractor",
			"Sale_Extractor",
			"Site_Extractor",
			"Staff_Extractor"
		) as $servicename) {
			if(class_exists($servicename)) $service = new $servicename($sourcename, $password, $siteIDs);
			$result[$servicename] = $service->get_all();
		}
		
		return $result;
	}

	public static function queuecall($callbacks, $clobbertime = 1) {
		static $lastcall = 0;
		if(time() < $lastcall + $clobbertime) sleep(($lastcall + $clobbertime) - time());
		
		$result = array();
		
		foreach($callbacks as $callback) {
			$result[substr($callback[1], 3)] = call_user_func($callback);
		}
		
		$lastcall = time();
		return $result;
	}
	
	/*
	Template for calls
	public function Get() {
		$call = new Get();
		$call->Request = new GetRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->Get($call);
		
		return $result->GetResult->;
	}
	*/
}
?>
