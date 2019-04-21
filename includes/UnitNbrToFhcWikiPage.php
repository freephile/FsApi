<?php
/**
 * @copyright Copyright (c) 2019, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name: UnitNbrToFhcWikiPage
 * @purpose: Implement the Fsapi functionality
 * @author:  Greg Rundlett <greg@equality-tech.com>
 * @version: 1.0.0
 * @date: 2019/04/19
 */


class UnitNbrToFhcWikiPage extends ApiBase {
	// is this even needed?
	public function __construct() {
		$parent::__construct();
	}
	const FHC_MASTER_LIST = 137044;
	const FHKB_MASTER_LIST = 131501;

	function execute () {
		$params = $this->extractRequestParams();
		$unitNum = $params['unitNum'];
		$language = $params['language'];
		$action = $params['action'];

		self::validateUnitNum($unitNum);
		self::validateLanguage($language);
		// $title = $this->getTitleFromTitleOrPageId(FHC_MASTER_LIST);
		/*
		$content = ApiParse::getParsedContent
			(	'', // WikiPage 	$page,
			 	'', // $popts,
			 	'', // $suppressCache,
			 	FHC_MASTER_LIST, // $pageId,
			 	'', // $rev,
			 	'', // $getContent
			);
		$ary = explode ("\n", $content);
		*/
		$url = "https://beta.familysearch.org/wiki/$language/api.php" .
			"?format=xml&action=query&pageids=" . FHC_MASTER_LIST . "&prop=revisions&rvprop=content";
		$xml = file_get_contents( $url );
		$ary = explode( "\n", $xml);
		$ret = 'not found';
		foreach ( $ary as $k => $v ) {
			$v = explode( '|', $v );
			if ( in_array ( $unitNum, $v ) ) {
				$ret = $v[1];
			}
		}
		return $ret;
/*
		$master_page_id = $this->fhcMasterPageId;
		$titleUrl = $this->indexUrl . "?format=xml&action=query&pageids=${master_page_id}&prop=revisions&rvprop=content";
		$wikiXml = $this->fsTransport->get($titleUrl);
		die ("got $wikiXml for wikiXml");
		$p = new MwWikiApi();
		$p->parse($wikiXml);
		$urls = $p->matchUnitNbr($unitNbr);
		$fs = new FsUnitNbrMap();
		$fs->addUrls($urls);
		return $fs->toString();
*/

	}
	protected function getAllowedParams() {
		return [
			'action' => 'unitNbrToFhcWikiPage',
			'unitNum' => [
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_TYPE => 'integer',
			],
			'language' => [
				ApiBase::PARAM_DFLT => 'en',
				ApiBase::PARAM_TYPE => ['de', 'en', 'es', 'fr', 'it', 'ja', 'ko', 'pt', 'ru', 'sv', 'zh'],
			]
		];
	}

	public static function validateUnitNum( &$unitNum ) {
		$unitNum = intval($unitNum);
		if ($unitNum === 0) {
			die ('invalid Unit Number');
		}
		return true;
	}

	public static function validateLanguage( &$language ) {
		if (! in_array( $language, ['de', 'en', 'es', 'fr', 'it', 'ja', 'ko', 'pt', 'ru', 'sv', 'zh'] ) ) {
			die ('invalid Language');
		}
		return true;
	}
}
