<?php

/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    FsXml
 * @purpose: A base class to define common xml functionality
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */
class FsXml
{
	// The xml structure represented as a php object
	protected $xml_data;
	protected $xml_parsed;

	function __construct()
	{
		$this->xml_data = $this->initializeXmlData();
	}

	function initializeXmlData()
	{
		return array(
			'action' => array(
				'name' => 'OverrideMe',
				'search' => array('name' => 'searchFor', 'keys' => array()),
				'result' => array('name' => 'searchFor', 'keys' => array()),
			),
		);
	}

	function parse($xml)
	{
		$this->xml_parsed = new SimpleXMLElement($xml);
	}

	function array_to_xml($input_array, &$xml_obj)
	{
		foreach ($input_array as $key => $value) {
			if (is_array($value)) {
				if (!is_numeric($key)) {
					$subnode = $xml_obj->addChild("$key");
					$this->array_to_xml($value, $subnode);
				} else {
					$this->array_to_xml($value, $xml_obj);
				}
			} else {
				if (is_numeric($key)) {
					$xml_obj->addChild('rev', "$value");
				} else {
					$xml_obj->addAttribute("$key", "$value");
				}
			}
		}
	}

	function formatXmlString($xml)
	{

		// add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
		$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

		// now indent the tags
		$token = strtok($xml, "\n");
		$result = ''; // holds formatted version as it is built
		$pad = 0; // initial indent
		$matches = array(); // returns from preg_matches()

		// scan each line and adjust indent based on opening/closing tags
		while ($token !== false) :

			// test for the various tag states
			// 1. open and closing tags on same line - no change
			if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
				$indent = 0;
			// 2. closing tag - outdent now
			elseif (preg_match('/^<\/\w/', $token, $matches)) :
				$pad--;
			// 3. opening tag - don't pad this one, only subsequent tags
			elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
				$indent = 1;
			// 4. no indentation needed
			else :
				$indent = 0;
			endif;

			// pad the line with the required number of leading spaces
			$line = str_pad($token, strlen($token) + $pad, ' ', STR_PAD_LEFT);
			$result .= $line . "\n"; // add to the cumulative result, with linefeed
			$token = strtok("\n"); // get the next token
			$pad += $indent; // update the pad size for subsequent lines
		endwhile;

		return $result;
	}

	function toString()
	{
		$o = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><api></api>');
		$this->array_to_xml($this->xml_data, $o);
		return trim($this->formatXmlString($o->asXML()));
	}
}
