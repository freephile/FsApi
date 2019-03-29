<?php
/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    FsApi
 * @purpose: Define high level api operations
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */

require_once('extensions/FsApi/FsApiExtension.i18n.php');

interface FsApi
{

	/*
	 * search for citation information via a collection id.
	 *
	 * @param: $collectionId - String representation of collection id.
	 * @return: xml string representing a search result
	 */
	function searchByCollectionId($collectionId);

	function searchByHelpTextId($pageId, $fieldId);

	/**
	 * Given a CDOL unit nbr and a language, lookup the wiki page
	 * pointing to the family history center served by that unit nbr
	 *
	 * @param - $unitNbr  - A string representation of a CDOL unit number
	 * @param = $language - A two letter string representing a language code
	 * @returns - A url representing a wiki page
	 *
	 */
	function unitNbrToFhcWikiPage($unitNbr, $language = 'en');

}
