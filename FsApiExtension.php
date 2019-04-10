<?php
/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    FsApiExtension
 * @purpose: Interface to mediawiki include process
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */

// require_once('includes/global_api.php');
require_once(__DIR__ . "/FsApiImpl.php");
require_once(__DIR__ . '/MwApiImpl.php');
require_once(__DIR__ . '/FsCurl.php');

$wgExtensionCredits['other'][] =
	array(
		'name' => 'FSApi',
		'author' => 'Andy Olsen',
		'version' => '0.0.1',
		'url' => 'https://familysearch.org/wiki/en/extensions/FsApi/FsApiExtension.php',
		'description' => 'An api centered toward FamilySearch needs.'
	);


// Create a network transport
$fsTransport = new FsCurl();

// Pulled in from includes/global_api.php
$fsApiImpl = new FsApiImpl($fsTransport, FS_WIKI_FS_API_URL, FS_WIKI_MW_API_URL, FS_WIKI_INDEX_URL);

// extract the api action
$outputXml = $fsApiImpl->runQuery($_REQUEST);

header('Content-Type: text/xml');
header('Content-Length: ' . strlen($outputXml));
print $outputXml;
