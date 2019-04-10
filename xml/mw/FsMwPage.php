<?php
/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    FsMwPage
 * @purpose: Abstract the Page format of the MW Api
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */

require_once __DIR__ . '/../FsXml.php';

class FsMwPage extends FsXml
{

	function __construct()
	{
		parent::__construct();
	}

	function initializeXmlData()
	{
		return array(
			'query' =>
				array(
					'normalized' => array('n' => array('from' => '', 'to' => '')),
					'pages' =>
						array(
							'page' =>
								array(
									'pageid' => '',
									'title' => '',
									'ns' => '',
									'revisions' => array(),
								),
						),
				),
		);
	}

	function parse($xml)
	{
		$o = new SimpleXMLElement($xml);
		$attrs = $o->query->pages->page->attributes();
		$this->setPageId((string)$attrs->{'pageid'});
		$this->setTitle((string)$attrs->{'title'});
		$this->setNameSpace((string)$attrs->{'ns'});
		$this->setMwText($o->query->pages->page->revisions[0]->rev);
	}

	function getCitation()
	{
		$text = $this->getMwText();
		if (!$text) {
			return '';
		}

		$startTerm = '<!--bibdescbegin-->';
		$endTerm = '<!--bibdescend-->';
		$begin = strpos($text, $startTerm) + strlen($startTerm);
		$end = strpos($text, $endTerm);
		$len = $end - $begin;
		if ($begin && $end) {
			$citation = substr($text, $begin, $len);
			return trim($citation);
		} else {
			return '';
		}
	}

	function getHelpText($fieldId)
	{
		$text = $this->getMwText();
		if (!$text) {
			return '';
		}

		// get rid of the ending template tags
		$text = str_replace('}}', '', $text);

		// Now split the chunks by opening template tags
		$chunks = explode('{{', $text);

		$fields = array();

		// Now walk the list of items looking for the correct field id
		foreach ($chunks as $chunk) {
			if (stripos($chunk, $fieldId)) {
				$items = explode('|', $chunk);
				foreach ($items as $item) {
					if (stripos($item, '=')) {
						$nvp = explode('=', $item);
						if ($nvp[0] != 'fieldId') {
							$key = trim($nvp[0]);
							$value = trim($nvp[1]);
							$fields[$key] = $value;
						}
					}
				}
			}
		}

		return $fields;
	}


	function setTitle($title)
	{
		$this->xml_data['query']['pages']['page']['title'] = $title;
	}

	function setNameSpace($ns)
	{
		$this->xml_data['query']['pages']['page']['ns'] = $ns;
	}

	function setPageId($id)
	{
		$this->xml_data['query']['pages']['page']['pageid'] = $id;
	}

	function setMwText($text)
	{
		array_push($this->xml_data['query']['pages']['page']['revisions'], (string)$text);
	}

	function getTitle()
	{
		return $this->xml_data['query']['pages']['page']['title'];
	}

	function getNameSpace()
	{
		return $this->xml_data['query']['pages']['page']['ns'];
	}

	function getPageId()
	{
		return $this->xml_data['query']['pages']['page']['pageid'];
	}

	function getMwText()
	{
		return $this->xml_data['query']['pages']['page']['revisions'][0];
	}

}
