<?
header("Content-type: text/xml");

$bus_stop_code = $_GET['bus_stop_code'];

if (!$bus_stop_code) die("Missing parameter: bus_stop_code");

$junk = getData($baseurl."display.php?clientType=b&&numberOfPassage=2&busStopCode=".$bus_stop_code);
$xml = simplexml_load_string($junk);
$xpath = $xml->xpath("//*[@id=\"displayDepartures\"]");

$returnxml = new SimpleXmlElement("<bustracker></bustracker>");
foreach (($xpath[0]->pre) as $pre) {
	// behold my mad reg-ex skillz
	preg_match("/([N|X]*\d*[A-Z]?) *([A-Z ]*)\**(\d*(:\d\d)*)/",$pre,$matches);
	$departure_node = $returnxml->addChild("departure");
	$departure_node->addChild("service",trim($matches[1]));
	$destination = preg_replace("/ *DUE/", "", trim($matches[2]));
	$departure_node->addChild("destination",$destination);
	if (strpos($matches[3],":")) {
		// if hours is less < current hour then add tomorrow's day before we strtotime it
		if (substr($matches[3],0,2) < date("h") ) {
			$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
			$timediff = strtotime(date("m/d/y",$tomorrow)." ".$matches[3]) - time();
		}
		else {
			$timediff = strtotime($matches[3]) - time();
		}
		$minstogo = round($timediff/60,0);
		$timechild = $departure_node->addChild("mins",$minstogo);
		$timechild->addAttribute("estimated","true");
	}
	else {
		$mins = trim($matches[3]);
		if ($mins == "")
		{
			$departure_node->addChild("mins","0");
		}
		else
		{
			$departure_node->addChild("mins",$mins);
		}
	}
}
echo $returnxml->asXML();
?>
