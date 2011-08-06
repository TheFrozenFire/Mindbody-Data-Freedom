<?php
require_once("Mindbody_Extractor.php");

$shortopt = array(
	"h",
	"s:",
	"p:",
	"i:",
	"c:"
);

$longopt = array(
	"help",
	
	"sourcename:",
	"password:",
	"siteid:",
	
	"all",
	"appointment",
	"class",
	"client",
	"sale",
	"site",
	"staff",
	
	"clobbertime:"
);

$options = getopt(implode("", $shortopt), $longopt);

if(
	(array_key_exists("h", $options) || array_key_exists("help", $options)) // Asked for help
	|| !(array_key_exists("s", $options) || array_key_exists("sourcename", $options)) // Didn't specify sourcename
	|| !(array_key_exists("p", $options) || array_key_exists("password", $options)) // Didn't specify password
	|| !(array_key_exists("i", $options) || array_key_exists("siteid", $options)) // Didn't specify site ID
	|| (is_array($options["s"]) || is_array($options["sourcename"]) || is_array($options["p"]) || is_array($options["password"]) || is_array($options["c"]) || is_array($options["clobbertime"])) // Specified more than one sourcename, password or clobbertime style
	|| (array_key_exists("s", $options) && array_key_exists("sourcename", $options)) // Specified both sourcename styles
	|| (array_key_exists("p", $options) && array_key_exists("password", $options)) // Specified both password styles
	|| (array_key_exists("c", $options) && array_key_exists("clobbertime", $options)) // Specified both clobbertime styles
) {
?>
Usage: php extract.php [OPTION]...

	-h, --help			Show this message

Source Credentials:
	-s, --sourcename=SOURCENAME	Specify the sourcename to be used for
					source credentials.
	-p, --password=PASSWORD		Specify the password to be used for
					source credentials.
	-i, --siteid=SITEID		Specify the site ID to be used for
					source credentials. Specify multiple
					times for multiple sites.
		
Services:
	Specify either --all to extract data from all available services, or
	specify each service individually.
	
	--all				Extract data from all available services. (Resource intensive)
	--appointment			Extract data from the Appointment service.
	--class				Extract data from the Class service.
	--client			Extract data from the Client service.
	--sale				Extract data from the Sale service.
	--site				Extract data from the Site service.
	--staff				Extract data from the Staff service.
	
Formats:
	Specify each format which you would like to have output.
	
	--excel				Output an Excel book.
	--csv				Output a set of CSV files.
	--sqlite			Output an SQLite database. (pdo_sqlite extension required)
	--mysql				Output a MySQL database dump.

Other:
	-c, --clobbertime=SECONDS	Interval between API calls. Defaults to 1 second.
	-o, --output=DIR		Specify an output directory. Defaults to ./output.

See https://github.com/TheFrozenFire/Mindbody-Data-Freedom for support.
<?php
	exit;
}

$sourcename = array_key_exists("s", $options)?$options["s"]:$options["sourcename"];
$password = array_key_exists("p", $options)?$options["p"]:$options["password"];
// I really don't think the following can be expressed any more succinctly...
if(is_array($options["i"]) && is_array($options["siteid"])) $siteids = array_merge($options["i"], $options["siteid"]);
elseif(is_array($options["i"]) && is_numeric($options["siteid"])) {
	$siteids = $options["i"];
	$siteids[] = $options["siteid"];
} elseif(is_array($options["siteid"]) && is_numeric($options["i"])) {
	$siteids = $options["siteid"];
	$siteids[] = $options["i"];
} elseif(is_array($options["i"])) $siteids = $options["i"];
elseif(is_array($options["siteid"])) $siteids = $options["siteid"];
else $siteids = array_key_exists("i", $options)?array($options["i"]):array($options["siteid"]);

if(array_key_exists("c", $options) || array_key_exists("clobbertime", $options)) $clobbertime = array_key_exists("c", $options)?$options["c"]:$options["clobbertime"]; else $clobbertime = 1;

if(array_key_exists("all", $options)) {
	extract_services($sourcename, $password, $siteids, null, $clobbertime);
} else {
	$services = array();
	if(array_key_exists("appointment", $options)) $services[] = "Appointment_Extractor";
	if(array_key_exists("class", $options)) $services[] = "Class_Extractor";
	if(array_key_exists("client", $options)) $services[] = "Client_Extractor";
	if(array_key_exists("sale", $options)) $services[] = "Sale_Extractor";
	if(array_key_exists("site", $options)) $services[] = "Site_Extractor";
	if(array_key_exists("staff", $options)) $services[] = "Staff_Extractor";
	
	if(empty($services)) exit; else extract_services($sourcename, $password, $siteids, $services, $clobbertime);
}

function extract_services($sourcename, $password, $siteids, $services = null, $clobbertime = 1) {
	if(is_null($services)) {
		$extractors = Mindbody_Extractor::_get_all($sourcename, $password, $siteids, $clobbertime);
		foreach($extractors as $service=>$data) write_extracted_data($service, $data);
	} else foreach($services as $service) {
		if(is_readable("services/".$service.".php")) include_once("services/".$service.".php");
		$extractor = new $service($sourcename, $password, $siteids);
		
		$data = $extractor->get_all($clobbertime);
		write_extracted_data($service, $data);
	}
}

function write_extracted_data($service, $data) {
	static $tempdir;
	
	if(empty($tempdir)) {
		$dirnam = sys_get_temp_dir().DIRECTORY_SEPARATOR."Mindbody_Extractor_".md5(microtime());
		if(!mkdir($dirnam)) die("Could not write to your system's temp directory.\n");
		$tempdir = $dirnam;
	}
	
	if(!file_put_contents($tempdir.DIRECTORY_SEPARATOR."{$service}.txt", var_export($data, true))) die("Could not write to your system's temp directory.\n");
	
	echo "{$service} output:\t\t{$tempdir}".DIRECTORY_SEPARATOR."{$service}.txt\n";
}
?>
