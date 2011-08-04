<?php
require_once("Mindbody_Extractor.php");
class Site_Extractor extends Mindbody_Extractor {
	public function __construct($sourcename, $password, $siteIDs) {
		$this->service = new Site_x0020_Service();
		parent::__construct($sourcename, $password, $siteIDs);
	}

	public function get_all($clobbertime = 1) {
		$callbacks = array(
			array($this, "GetSites"),
			array($this, "GetLocations"),
			array($this, "GetPrograms"),
			array($this, "GetSessionTypes"),
			array($this, "GetRelationships")
		);
		
		return parent::queuecall($callbacks, $clobbertime);
	}
	
	public function GetSites() {
		$call = new GetSites();
		$call->Request = new GetSitesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetSites($call);
		
		return $result->GetSitesResult->Sites;
	}
	
	public function GetLocations() {
		$call = new GetLocations();
		$call->Request = new GetLocationsRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetLocations($call);
		
		return $result->GetLocationsResult->Locations;
	}
	
	public function GetPrograms() {
		$call = new GetPrograms();
		$call->Request = new GetProgramsRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$call->Request->ScheduleType = "All";
		
		$result = $this->service->GetPrograms($call);
		
		return $result->GetProgramsResult->Programs;
	}
	
	public function GetSessionTypes() {
		$call = new GetSessionTypes();
		$call->Request = new GetSessionTypesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetSessionTypes($call);
		
		return $result->GetSessionTypesResult->SessionTypes;
	}
	
	/*
	TODO: Figure out why wildcard queries for this don't work.
	public function GetResources() {
		$call = new GetResources();
		$call->Request = new GetResourcesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetResources($call);
		
		return $result->GetResourcesResult->Resources;
	}
	*/
	
	public function GetRelationships() {
		$call = new GetRelationships();
		$call->Request = new GetRelationshipsRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetRelationships($call);
		
		return $result->GetRelationshipsResult->Relationships;
	}
}
?>
