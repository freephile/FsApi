<?php
/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    MwApiImpl
 * @purpose: Implement the MwApi Interface
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */


require_once(__DIR__ . '/MwApi.php');
require_once(__DIR__ . '/xml/mw/FsMwSearch.php');
require_once(__DIR__ . '/xml/mw/FsMwPage.php');
require_once(__DIR__ . '/xml/mw/MwWikiApi.php');
require_once(__DIR__ . '/xml/mw/FsUnitNbrMap.php');
require_once(__DIR__ . '/xml/fs/FsSearch.php');

class MwApiImpl implements MwApi
{

	private $fsTransport;
	private $apiUrl;
	private $indexUrl;

	function MwApiImpl($fsTransport, $fsApiUrl, $mwApiUrl, $indexUrl, $fhcMasterPageId, $helpMasterPageId)
	{
		$this->fsTransport = $fsTransport;
		$this->fsApiUrl = $fsApiUrl;
		$this->mwApiUrl = $mwApiUrl;
		$this->indexUrl = $indexUrl;
		$this->fhcMasterPageId = $fhcMasterPageId;
		$this->helpMasterPageId = $helpMasterPageId;
	}

	function searchByCollectionId($collectionId)
	{

		// Call the mediawiki api and perform a search on this collection id.
		$searchUrl = $this->mwApiUrl . '?action=query&list=search&srwhat=text&format=xml&srsearch=' . $collectionId;
		$searchResultXml = $this->fsTransport->get($searchUrl);
		if (strlen($searchResultXml) < 1) {
			throw new Exception('Wiki Search Failed', 200);
		}

		// Hydrate the xml to a php object and lookup up the title
		$s = new FsMwSearch();
		$s->parse($searchResultXml);
		$title = $s->getTitle();
		if (strlen($title) < 1) {
			throw new Exception('No Search Results Found', 300);
		}

		// Now dial back to the mediawiki api to lookup the title
		$encoded = str_replace(' ', '_', $title);
		$encoded = urlencode($encoded);
		$titleUrl = $this->indexUrl . '?action=query&format=xml&prop=revisions&rvprop=content&titles=' . $encoded;
		$titleResultXml = $this->fsTransport->get($titleUrl);
		if (strlen($titleResultXml) < 1) {
			throw new Exception('Could not extract wikitext from api', 400);
		}

		// Hydrate the xml to a php object
		$fsMwPage = new FsMwPage();
		$fsMwPage->parse($titleResultXml);
		$fsCitation = $fsMwPage->getCitation();
		if (strlen($fsCitation) < 1) {
			throw new Exception('Could not extract citation information from wikipage', 500);
		}

		// Now create an FsSearch object
		$fs = new FsSearch();
		$fs->setActionName('searchByCollectionId');
		$fs->setSearchName('default');
		$fs->addSearchParameter('collectionId', $collectionId);
		$fs->addResult('title', $title);
		$fs->addResult('url', 'https://www.familysearch.org/wiki/en/' . $encoded);
		$fs->addResult('citation', $fsCitation);

		# Now drop the xml
		return $fs->toString();
	}

	function searchByHelpTextId($pageId, $fieldId)
	{
		// Call the mediawiki api and perform a search on this collection id.
		$searchUrl = $this->mwApiUrl . '?action=query&list=search&srwhat=text&format=xml&srsearch=' . $pageId;
		$searchResultXml = $this->fsTransport->get($searchUrl);
		if (strlen($searchResultXml) < 1) {
			throw new Exception('Wiki Search Failed', 200);
		}

		// Hydrate the xml to a php object and lookup up the title
		$s = new FsMwSearch();
		$s->parse($searchResultXml);
		$title = $s->getTitle();
		if (strlen($title) < 1) {
			throw new Exception('No Search Results Found', 300);
		}

		// Now dial back to the mediawiki api to lookup the title
		$encoded = str_replace(' ', '_', $title);
		$encoded = urlencode($encoded);
		$titleUrl = $this->indexUrl . '?action=query&format=xml&prop=revisions&rvprop=content&titles=' . $encoded;
		$titleResultXml = $this->fsTransport->get($titleUrl);
		if (strlen($titleResultXml) < 1) {
			throw new Exception('Could not extract wikitext from api', 400);
		}

		// Hydrate the xml to a php object
		$fsMwPage = new FsMwPage();
		$fsMwPage->parse($titleResultXml);
		$items = $fsMwPage->getHelpText($fieldId);
		if (count($items) < 1) {
			throw new Exception('Could not extract Field information from wikipage', 600);
		}


		// Now create an FsSearch object
		$fs = new FsSearch();
		$fs->setActionName('searchByHelpTextId');
		$fs->setSearchName('default');
		$fs->addSearchParameter('pageId', $pageId);
		$fs->addSearchParameter('fieldId', $fieldId);
		foreach ($items as $key => $value) {
			$fs->addResult($key, $value);
		}

		# Now drop the xml
		return $fs->toString();
	}

	/**
	 * Given a CDOL unit nbr and a language, lookup the wiki page
	 * pointing to the family history center served by that unit nbr
	 *
	 * @param - $unitNbr  - A string representation of a CDOL unit number
	 * @param - $language - A two letter string representing a language code
	 * @returns - A url representing a wiki page
	 *
	 */
	function unitNbrToFhcWikiPage($unitNbr, $language = 'en')
	{
		$master_page_id = $this->fhcMasterPageId;
		$titleUrl = $this->indexUrl . "?format=xml&action=query&pageids=${master_page_id}&prop=revisions&rvprop=content";
		$wikiXml = $this->fsTransport->get($titleUrl);
		$p = new MwWikiApi();
		$p->parse($wikiXml);
		$urls = $p->matchUnitNbr($unitNbr);
		$fs = new FsUnitNbrMap();
		$fs->addUrls($urls);
		return $fs->toString();
	}

	/**
	 * Given a knownledge base id, lookup the wiki page pointing to the
	 * knowledge base article
	 *
	 * @param - $kbNbr    - A string representation of a CDOL unit number
	 * @param - $language - A two letter string representing a language code
	 * @returns - A url representing a wiki page
	 *
	 */
	function kbNbrToFhcWikiPage($kbNbr, $language = 'en')
	{
		$help_page_id = $this->helpMasterPageId;
		$titleUrl = $this->indexUrl . "?format=xml&action=query&pageids=${help_page_id}&prop=revisions&rvprop=content";
		$wikiXml = $this->fsTransport->get($titleUrl);
		$p = new MwWikiApi();
		$p->parse($wikiXml);
		$urls = $p->matchUnitNbr($kbNbr);
		$fs = new FsUnitNbrMap();
		$fs->addUrls($urls);
		return $fs->toString();
	}
}
