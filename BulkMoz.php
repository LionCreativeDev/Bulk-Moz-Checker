<?php

// Start the session
session_start();

function mozcredentials($arg)
{
	switch ($arg) {
		case "1":
			return array(
				"accessID" => "mozscape-19f7a721b6",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "2":
			return array(
				"accessID" => "mozscape-5a3a7d6fb6",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "3":
			return array(
				"accessID" => "mozscape-40ddd53844",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "4":
			return array(
				"accessID" => "mozscape-288776f601",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "5":
			return array(
				"accessID" => "mozscape-a69ef771d5",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "6":
			return array(
				"accessID" => "mozscape-25384985ad",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "7":
			return array(
				"accessID" => "mozscape-a3c14a527",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "8":
			return array(
				"accessID" => "mozscape-fe192c335b",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "9":
			return array(
				"accessID" => "mozscape-d612fe7d54",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "10":
			return array(
				"accessID" => "mozscape-b50f46a17f",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "11":
			return array(
				"accessID" => "mozscape-31d5581569",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "12":
			return array(
				"accessID" => "mozscape-956184cf04",
				"secretKey" => "your moz secret key here"
			);
			break;
			
		case "13":
			return array(
				"accessID" => "mozscape-f4263ccdcf",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "14":
			return array(
				"accessID" => "mozscape-ae48533118",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "15":
			return array(
				"accessID" => "mozscape-b006091f02",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "16":
			return array(
				"accessID" => "mozscape-c4ad58c523",
				"secretKey" => "your moz secret key here"
			);
			break;
		case "17":
			return array(
				"accessID" => "mozscape-b11dc789a9",
				"secretKey" => "your moz secret key here"
			);
			break;
	}
}

function GetMoz(&$cycle, &$DA, &$PA, &$MOZ_RANK, &$IP, $checkurl)
{
	$cycle = $_SESSION['apikey'];
	// Get your access id and secret key here: https://moz.com/products/api/keys

	//To get Random Api key
	//$x = mozcredentials(rand(1, 17)); //Range 1-17
	
	//To get 1 0f 17 api for each url
	$x = mozcredentials($_SESSION['apikey']);
	//echo $x["accessID"].'<br/>';
	//echo $x["secretKey"].'<br/>';

	$accessID = $x["accessID"];
	$secretKey = $x["secretKey"];
	// Set your expires times for several minutes into the future.
	// An expires time excessively far in the future will not be honored by the Mozscape API.
	$expires = time() + 300;
	// Put each parameter on a new line.
	$stringToSign = $accessID . "\n" . $expires;
	// Get the "raw" or binary output of the hmac hash.
	$binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);
	// Base64-encode it and then url-encode that.
	$urlSafeSignature = urlencode(base64_encode($binarySignature));
	// Specify the URL that you want link metrics for.
	$objectURL = $checkurl;
	// Add up all the bit flags you want returned.
	// Learn more here: https://moz.com/help/guides/moz-api/mozscape/api-reference/url-metrics
	
	//Canonical URL	4	uu
	//Page Authority	34359738368	upa
	//Domain Authority	68719476736	pda
	//cols = 103079215108
	
	//$cols = "103079215108";//Canonical URL(4) + Page Authority(34359738368) + Domain Authority(68719476736);
	
	//For Parameters
	//https://moz.com/help/links-api/making-calls/url-metrics
	$PageAuthority = 34359738368; //upa
	$DomainAuthority = 68719476736; //pda
	//$MozRank = 65536; //Root Domain : pmrp pmrr
	$MozRank = 16384; //Root Domain : umrp umrr
	$SubdomainSpamScore = 67108864; //fspsc
	$cols = ($PageAuthority + $DomainAuthority + $MozRank + $SubdomainSpamScore);
	
	// Put it all together and you get your request URL.
	// This example uses the Mozscape URL Metrics API.
	$requestUrl = "http://lsapi.seomoz.com/linkscape/url-metrics/" . urlencode($objectURL) . "?Cols=" . $cols . "&AccessID=" . $accessID . "&Expires=" . $expires . "&Signature=" . $urlSafeSignature;
	// Use Curl to send off your request.
	$options = array(
		CURLOPT_RETURNTRANSFER => true
	);
	$ch = curl_init($requestUrl);
	curl_setopt_array($ch, $options);
	$json = curl_exec($ch);
	
	if (!curl_errno($ch)) {
		if (isset($json)) {
			//var_dump($json);
			//exit();
			$jsonDecode = json_decode($json);
			//var_dump($jsonDecode);
			$DA = $jsonDecode->pda;
			$PA = $jsonDecode->upa;
			$MOZ_RANK = $jsonDecode->umrp;
			//$Spam_Score = $jsonDecode->fspsc;
			
			//sleep(11);	// We waited 11 seconds (delay required for the Moz API with free account)
			$domain = parse_url('http://' . str_replace(array('https://', 'http://','www.'), '', $checkurl), PHP_URL_HOST);
			$IP = gethostbyname($domain);
		}
	}
	else{
		//$DA = "- accessID ".$accessID." secretKey ".$secretKey;
		$DA = "-";
		$PA = "-";
		$MOZ_RANK = "-";
	}
	curl_close($ch);
}

$cycle = '';
$DA = ''; //pda
$PA = ''; //upa
$MOZ_RANK = ''; //pmrp
//$Spam_Score = ''; //fspsc
$IP = '';

if (isset($_GET["url"])) {
	
	if (!isset($_SESSION['apikey']) || $_SESSION['apikey'] == '')
	{
		$_SESSION['apikey'] = 1;
	}
	elseif (isset($_SESSION['apikey']) && $_SESSION['apikey'] == '18')
	{
		$_SESSION['apikey'] = 1;
	}
	
	// $x = mozcredentials($_SESSION['apikey']);
	// //echo $x["accessID"].'<br/>';
	// //echo $x["secretKey"].'<br/>';

	// $accessID = $x["accessID"];
	// $secretKey = $x["secretKey"];
	// echo '{"cycle":"'.$_SESSION['apikey'].'","url":"'.$_GET["url"].'","DA":"'.$accessID.'","PA":"'.$secretKey.'","MOZRANK":"-"}';
	
	// if(isset($_SESSION['apikey']))
	// {
		// $temp = $_SESSION['apikey'];
		// $temp = $temp+1;
		// $_SESSION['apikey'] = $temp;
	// }
	// exit;
	
	$checkurl = trim($_GET["url"]);
	GetMoz($cycle, $DA, $PA, $MOZ_RANK, $IP, $checkurl);
	//echo $DA.' | '.$PA.' | '.$MOZ_RANK.' | '.$Spam_Score;
	//echo '{ "DA":' . $DA . ', "PA":' . $PA . ', "MOZRANK":' . $MOZ_RANK . ', "SPAMSCORE": ' . $Spam_Score . '}';
	echo '{"cycle":"'.$cycle.'","url":"'.$checkurl.'","DA":"'.$DA.'","PA":"'.$PA.'","MOZRANK":"'.$MOZ_RANK.'","IP":"'.$IP.'"}';
	
	if(isset($_SESSION['apikey']))
	{
		$temp = $_SESSION['apikey'];
		$temp = $temp+1;
		$_SESSION['apikey'] = $temp;
	}
}
?>