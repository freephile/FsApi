<?php
/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    FsApiImpl
 * @purpose: Implement the FsApi interface
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */

require_once('extensions/FsApi/FsApi.php');
require_once('extensions/FsApi/MwApiImpl.php');

class FsApiImpl implements FsApi
{

	private $fsTransport;
	private $mwApiImpl;

	function __construct($fsTransport, $fsApiUrl, $mwApiUrl, $indexUrl)
	{
		$this->fsTransport = $fsTransport;
		$this->mwApiImpl = new MwApiImpl($fsTransport, $fsApiUrl, $mwApiUrl, $indexUrl, FHC_MASTER_LIST, FHKB_MASTER_LIST);
	}

	function searchByCollectionId($collectionId)
	{
		return $this->mwApiImpl->searchByCollectionId($collectionId);
	}

	function searchByHelpTextId($pageId, $fieldId)
	{
		return $this->mwApiImpl->searchByHelpTextId($pageId, $fieldId);
	}

	/**
	 * Given a CDOL unit nbr and a language, lookup the wiki page
	 * pointing to the family history center served by that unit nbr
	 *
	 * @param - $unitNbr  - A string representation of a CDOL unit number
	 * @param = $language - A two letter string representing a language code
	 * @returns - A url representing a wiki page
	 *
	 */
	function unitNbrToFhcWikiPage($unitNbr, $language = 'en')
	{
		return $this->mwApiImpl->unitNbrToFhcWikiPage($unitNbr, $language);
	}

	function kbNbrToFhcWikiPage($unitNbr, $language = 'en')
	{
		return $this->mwApiImpl->kbNbrToFhcWikiPage($unitNbr, $language);
	}

	// A function to dump exceptions
	function getExceptionXml($e)
	{
		$code = $e->getCode();
		$message = $e->getMessage();
		return <<<EOS
<?xml version="1.0" encoding="UTF-8"?>
<exceptions>
 <exception code="$code" message="$message"/>
</exceptions>
EOS;
	}


	function runQuery($request)
	{
		$action = strtolower($request['action']);
		$outputXml = '';
		try {
			switch ($action) {
				case 'searchbycollectionid':
					$outputXml = $this->searchByCollectionId($_REQUEST['collectionId']);
					break;
				case 'searchbyhelptextid':
					$outputXml = $this->searchByHelpTextId($_REQUEST['pageId'], $_REQUEST['fieldId']);
					break;
				case 'unitnbrtofhcwikipage':
					$outputXml = $this->unitNbrToFhcWikiPage($_REQUEST['unitNbr'], $_REQUEST['language']);
					break;
				case 'kbnbrtofhcwikipage':
					$outputXml = $this->kbNbrToFhcWikiPage($_REQUEST['unitNbr'], $_REQUEST['language']);
					break;
				default:
					$outputXml = $this->getExceptionXml(new Exception("Unknown action: '$action'", 100));
					break;
			}
		} catch (Exception $e) {
			$outputXml = $this->getExceptionXml(new Exception($e, 800));
		}

		return $outputXml;
	}
}
