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
define ("FHC_MASTER_LIST", 137044);
$language = $_GET["language"]; // 'en'
$language = (empty($language))? "en" : $language;
$unitNum = $_GET["unitNbr"]; // 3331776

$url = "https://beta.familysearch.org/wiki/$language/api.php" .
	"?format=json&action=query&pageids=" . FHC_MASTER_LIST . "&prop=revisions&rvprop=content";

$json = file_get_contents( $url );

$obj = json_decode($json, true);

$ret = 'not found';
$str = $obj["query"]["pages"]["137044"]["revisions"][0]["*"];

$ary = explode("\n", $str);

foreach ( $ary as $k => $v ) {
	$v = explode( '|', $v );

	if ( in_array ( "#$unitNum", $v ) ) {
		$ret = $v[1];
	}
}

$xml = new SimpleXMLElement("<api><url><rev>$ret</rev></url></api>");
echo $xml->asXML();
