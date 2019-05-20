<?php
/**
 * @copyright Copyright (c) 2019, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name: UnitNbrToFhcWikiPage
 * @purpose: Implement the Fsapi functionality
 * @author:  Greg Rundlett <greg@equality-tech.com>
 * @version: 1.0.0
 * @date: 2019/04/19
 */
// this is the page we want to parse for data
define ("FHC_MASTER_LIST", 137044);

$language = '';

// take in our parameters from the request
if( isset( $_GET['language'] ) )  {
	$language = $_GET["language"]; // 'en'
}

// default to 'en' for language
$language = (empty($language))? "en" : $language;
$unitNum = $_GET["unitNbr"]; // 3331776

// Full URI for API endpoint
$url = "https://{$_SERVER['HTTP_HOST']}/wiki/$language/api.php" .
	"?format=json&action=query&pageids=" . FHC_MASTER_LIST . "&prop=revisions&rvprop=content";

// We requested json because it's easier to work with
$json = file_get_contents( $url );
// json_decode turns it into a PHP data structure
$obj = json_decode($json, true);

// intialize our return value
$ret = 'not found';
// and grab the string contents of the PHP object
$str = $obj["query"]["pages"]["137044"]["revisions"][0]["*"];

// Turn contents into an array based on newlines
$ary = explode("\n", $str);

// The values each have to be split as well
foreach ( $ary as $k => $v ) {
	$v = explode( '|', $v );
	// find the key we're looking for
	if ( in_array ( "#$unitNum", $v ) ) {
		$ret = $v[1];
	}
}

// Turn our response into XML to meet existing system requirements
$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><api><urls><rev>$ret</rev></urls></api>");

// Return the XML document
header("content-type: text/xml;charset=UTF-8");
echo $xml->asXML();
