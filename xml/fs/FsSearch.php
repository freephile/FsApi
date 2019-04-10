<?php
/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    FsSearch
 * @purpose: Abstract page data from Community Search service
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */

require_once __DIR__ . '/../FsXml.php';

class FsSearch extends FsXml
{

	function __construct()
	{
		parent::__construct();
	}

	function setActionName($name)
	{
		$this->xml_data['action']['name'] = $name;
	}

	function initializeXmlData()
	{
		return array(
			'action' =>
				array(
					'search' => array('name' => '', 'keys' => array()),
					'result' => array('name' => '', 'keys' => array()),
				),
		);
	}

	function parse($xml)
	{
		$o = new SimpleXMLElement($xml);
		$this->setSearchName((string)$o->action->attributes()->{'name'});
		foreach ($o->action->search->keys->key as $searchTerm) {
			$name = (string)$searchTerm->attributes()->{'name'};
			$value = (string)$searchTerm->attributes()->{'value'};
			$this->addSearchParameter($name, $value);
		}

		foreach ($o->action->result->keys->key as $result) {
			$name = (string)$result->attributes()->{'name'};
			$value = (string)$result->attributes()->{'value'};
			$this->addResult($name, $value);
		}
	}

	function setSearchName($name)
	{
		$this->xml_data['action']['search']['name'] = $name;
		$this->xml_data['action']['result']['name'] = $name;
	}

	function addSearchParameter($name, $value)
	{
		$array = array('key' => array('name' => $name, 'value' => $value));
		array_push($this->xml_data['action']['search']['keys'], $array);
	}

	function addResult($name, $value)
	{
		$array = array('key' => array('name' => $name, 'value' => $value));
		array_push($this->xml_data['action']['result']['keys'], $array);
	}

	function getTitle()
	{
		foreach ($this->xml_data['action']['result']['keys'] as $value) {
			if ($value['key']['name'] == 'title') {
				return $value['key']['value'];
			}
		}
		return null;
	}

	function getUrl()
	{
		foreach ($this->xml_data['action']['result']['keys'] as $value) {
			if ($value['key']['name'] == 'url') {
				return $value['key']['value'];
			}
		}
		return null;
	}

	function getCitation()
	{
		foreach ($this->xml_data['action']['result']['keys'] as $value) {
			if ($value['key']['name'] == 'citation') {
				return $value['key']['value'];
			}
		}
		return null;
	}

}
