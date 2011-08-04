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

	abstract public function get_all();
}
?>
