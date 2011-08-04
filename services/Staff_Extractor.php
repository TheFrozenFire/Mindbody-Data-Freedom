<?php
require_once("Mindbody_Extractor.php");
class Staff_Extractor extends Mindbody_Extractor {
	public function __construct($sourcename, $password, $siteIDs) {
		$this->service = new Staff_x0020_Service();
		parent::__construct($sourcename, $password, $siteIDs);
	}
	
	public function get_all($clobbertime = 1) {
		$callbacks = array(
			array($this, "GetStaff"),
			array($this, "GetStaffPermissions")
		);
		
		return parent::queuecall($callbacks, $clobbertime);
	}
	
	public function GetStaff() {
		$call = new GetStaff();
		$call->Request = new GetStaffRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetStaff($call);
		
		return $result->GetStaffResult->StaffMembers;
	}
	
	public function GetStaffPermissions() {
		$call = new GetStaffPermissions();
		$call->Request = new GetStaffPermissionsRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$staffList = $this->GetStaff();
		
		$result = array();
		
		foreach($staffList as $staffItem) {
			$call->Request->ID = $staffItem->ID;
			$staffPermissions = $this->service->GetStaffPermissions($call);
			$result[$staffItem->ID] = $staffPermissions->Permissions;
		}
		
		return $result;
	}
}
?>
