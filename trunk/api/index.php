<?
function getData($url) {
	$filename = "cache/".md5($url);
	if ( (file_exists($filename)) && (filemtime($filename) > (time()-60) ) )
	{
		$response = file_get_contents($filename);
	}
	else
	{
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $curl, CURLOPT_USERAGENT, "'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'" );
		$response = curl_exec( $curl );
		file_put_contents($filename, $response);
	}
	return $response;
}

$baseurl = "http://mybustracker.co.uk/";
$method = $_GET['method'];

switch ($method) {
case "bustracker.departures.getNext":
	include("bustracker.departures.getNext");
	break;
default:
	include("default");
}
?>
