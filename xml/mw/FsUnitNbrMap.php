<?php
/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    FsUnitNbrMap
 * @purpose: Define the output xml syntax
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */

require_once 'extensions/FsApi/xml/FsXml.php';

class FsUnitNbrMap extends FsXml
{

	function __construct()
	{
		parent::__construct();
	}

	function initializeXmlData()
	{
		return array(
			'urls' => array()
		);
	}

	function addUrls($urls)
	{
		$this->xml_data['urls'] = $urls;
	}

}
