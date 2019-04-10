<?php
/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    FsMwSearch
 * @purpose: Abstract the Search format of the MW Api
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */

require_once __DIR__ . "/../FsXml.php";

class FsMwSearch extends FsXml
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
					'searchinfo' => array('totalhits' => '1'),
					'search' =>
						array(
							'p' =>
								array(
									'ns' => '',
									'title' => '',
									'snippet' => '',
									'size' => '',
									'wordcount' => '',
									'timestamp' => ''
								),
						),
				),
		);
	}

	function parse($xml)
	{
		$o = new SimpleXMLElement($xml);
		if (!isset($o->query->search)) {
			return null;
		}
		$attrs = $o->query->search->p->attributes();
		$this->setTitle((string)$attrs->{'title'});
		$this->setNameSpace((string)$attrs->{'ns'});
		$this->setSnippet((string)$attrs->{'snippet'});
		$this->setSize((string)$attrs->{'size'});
		$this->setWordCount((string)$attrs->{'wordcount'});
		$this->setTimeStamp((string)$attrs->{'timestamp'});
	}

	function setTitle($title)
	{
		$this->xml_data['query']['search']['p']['title'] = $title;
	}

	function setNameSpace($ns)
	{
		$this->xml_data['query']['search']['p']['ns'] = $ns;
	}

	function setSnippet($snippet)
	{
		$this->xml_data['query']['search']['p']['snippet'] = $snippet;
	}

	function setSize($size)
	{
		$this->xml_data['query']['search']['p']['size'] = $size;
	}

	function setWordCount($count)
	{
		$this->xml_data['query']['search']['p']['wordcount'] = $count;
	}

	function setTimeStamp($stamp)
	{
		$this->xml_data['query']['search']['p']['timestamp'] = $stamp;
	}


	function getTitle()
	{
		return $this->xml_data['query']['search']['p']['title'];
	}

	function getNameSpace()
	{
		return $this->xml_data['query']['search']['p']['ns'];
	}

	function getSnippet()
	{
		return $this->xml_data['query']['search']['p']['snippet'];
	}

	function getSize()
	{
		return $this->xml_data['query']['search']['p']['size'];
	}

	function getWordCount()
	{
		return $this->xml_data['query']['search']['p']['wordcount'];
	}

	function getTimeStamp()
	{
		return $this->xml_data['query']['search']['p']['timestamp'];
	}
}
