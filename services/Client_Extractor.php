<?php
require_once("Mindbody_Extractor.php");
class Client_Extractor extends Mindbody_Extractor {
	public function __construct($sourcename, $password, $siteIDs) {
		$this->service = new Client_x0020_Service();
		parent::__construct($sourcename, $password, $siteIDs);
	}
	
	public function get_all($clobbertime = 1) {
		$callbacks = array(
			array($this, "GetClients"),
			array($this, "GetClientIndexes"),
			array($this, "GetClientReferralTypes"),
			array($this, "GetActiveClientMemberships"),
			array($this, "GetClientContracts"),
			array($this, "GetClientServices")
		);
		
		return parent::queuecall($callbacks, $clobbertime);
	}
	
	public function GetClients() {
		$call = new GetClients();
		$call->Request = new GetClientsRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetClients($call);
		
		return $result->GetClientsResult->Clients;
	}
	
	public function GetClientIndexes() {
		$call = new GetClientIndexes();
		$call->Request = new GetClientIndexesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetClientIndexes($call);
		
		return $result->GetClientIndexesResult->ClientIndexes;
	}
	
	public function GetClientReferralTypes() {
		$call = new GetClientReferralTypes();
		$call->Request = new GetClientReferralTypesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetClientReferralTypes($call);
		
		return $result->GetClientReferralTypesResult->ReferralTypes;
	}
	
	public function GetActiveClientMemberships() {
		$call = new GetActiveClientMemberships();
		$call->Request = new GetActiveClientMembershipsRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$clients = $this->GetClientIDs();
		$clientMemberships = array();
		
		foreach($clients as $clientID) {
			$call->Request->ClientID = $clientID;
			$result = $this->service->GetActiveClientMemberships($call);
			$clientMemberships[$client->ID] = $result->ClientMemberships;
		}
		
		return $clientMemberships;
	}
	
	public function GetClientContracts() {
		$call = new GetClientContracts();
		$call->Request = new GetClientContractsRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$clients = $this->GetClientIDs();
		$clientContracts = array();
		
		foreach($clients as $clientID) {
			$call->Request->ClientID = $clientID;
			$result = $this->service->ClientContracts($call);
			$clientContracts[$client->ID] = $result->Contracts;
		}
		
		return $clientContracts;
	}
	
	public function GetClientServices() {
		$call = new GetClientServices();
		$call->Request = new GetClientServicesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$clients = $this->GetClientIDs();
		$clientServices = array();
		
		foreach($clients as $clientID) {
			$call->Request->ClientID = $clientID;
			$result = $this->service->ClientServices($call);
			$clientServices[$client->ID] = $result->ClientServices;
		}
		
		return $clientServices;
	}
	
	protected GetClientIDs($override = false) {
		static $ids = array();
		if(!empty($ids) && !$override) return $ids; else $ids = array();
		
		$call = new GetClients();
		$call->Request = new GetClientsRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$call->Request->XMLDetail = "bare";
		
		$result = $this->service->GetClients($call);
		
		foreach($result->GetClientsResult->Clients as $client) $ids[] = $client->ID;
		
		return $ids;
	}
}
?>
