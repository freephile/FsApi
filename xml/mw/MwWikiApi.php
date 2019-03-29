<?php
/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    MwWikiApi
 * @purpose: Abstract the Page Revisions format of the MW Api
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */

require_once 'extensions/FsApi/xml/FsXml.php';

class MwWikiApi extends FsXml
{

	function __construct()
	{
		parent::__construct();
	}

	function initializeXmlData()
	{
		return array(
			array(
				'query' => array(
					'pages' => array(
						'page' => array(
							'revisions' => array()
						),
					),
				),
			),
		);
	}

	function parse($xml)
	{
		$o = new SimpleXMLElement($xml);
		if (!isset($o->query->pages->page->revisions)) {
			return null;
		}
		$rev = $o->query->pages->page->revisions[0]->rev;
		$text = str_replace('#', '', $rev);
		$this->rows = explode("\n", $text);
	}

	function getRows()
	{
		return $this->rows;
	}

	function matchUnitNbr($unitNbr)
	{
		$urls = array();
		foreach ($this->rows as $row) {
			$columns = explode('|', $row);
			if ($columns[0] == $unitNbr) {
				array_push($urls, trim($columns[1]));
			}
		}
		return $urls;
	}
}
