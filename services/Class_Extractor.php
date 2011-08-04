<?php
require_once("Mindbody_Extractor.php");
class Class_Extractor extends Mindbody_Extractor {
	public function __construct($sourcename, $password, $siteIDs) {
		$this->service = new Class_x0020_Service();
		parent::__construct($sourcename, $password, $siteIDs);
	}

	public function get_all($clobbertime = 1) {
		$callbacks = array(
			array($this, "GetClasses")
		);
		
		return parent::queuecall($callbacks, $clobbertime);
	}
	
	public function GetClasses($startDate = null, $endDate = null) {
		if(is_null($startDate)) $startDate = 0;
		if(is_null($endDate)) $endDate = time() + 31556926; // A year from now
	
		$call = new GetClasses();
		$call->Request = new GetClassesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$call->Request->StartDateTime = date("c", $startDate);
		$call->Request->EndDateTime = date("c", $endDate);
		
		$result = $this->service->GetClasses($call);
		
		return $result->GetClassesResult->Classes;
	}
}
?>
