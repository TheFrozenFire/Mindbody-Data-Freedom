<?php
require_once("Mindbody_Extractor.php");
class Sale_Extractor extends Mindbody_Extractor {
	public function __construct($sourcename, $password, $siteIDs) {
		$this->service = new Sale_x0020_Service();
		parent::__construct($sourcename, $password, $siteIDs);
	}
	
	public function get_all($clobbertime = 1) {
		$callbacks = array(
			array($this, "GetAcceptedCardType"),
			array($this, "GetSales")
		);
		
		return parent::queuecall($callbacks, $clobbertime);
	}
	
	public function GetAcceptedCardType() {
		$call = new GetAcceptedCardType();
		$call->Request = new GetAcceptedCardTypeRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetAcceptedCardType($call);
		
		return $result->GetAcceptedCardTypeResult->CardTypes;
	}
	
	public function GetSales() {
		$call = new GetSales();
		$call->Request = new GetSalesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetSales($call);
		
		return $result->GetSalesResult->Sales;
	}
	
	public function GetServices() {
		$call = new GetServices();
		$call->Request = new GetServicesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetServices($call);
		
		return $result->GetServicesResult->Services;
	}
	
	public function GetPackages() {
		$call = new GetPackages();
		$call->Request = new GetPackagesRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetPackages($call);
		
		return $result->GetPackagesResult->Packages;
	}
	
	public function GetProducts() {
		$call = new GetProducts();
		$call->Request = new GetProductsRequest();
		$call->Request->SourceCredentials = $this->sourcecredentials;
		
		$result = $this->service->GetProducts($call);
		
		return $result->GetProductsResult->Products;
	}
}
?>
