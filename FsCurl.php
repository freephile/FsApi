<?php

/**
 * @copyright Copyright (c) 2012, Intellectual Reserve, Inc.
 * http://www.familysearch.org
 * Basic Api functionality
 *
 *
 * @name:    FsCurl
 * @purpose: Wrap the curl functions to provide network transport
 * @author:  Andy Olsen <andy@rfocus.com>
 * @version: 0.0.1
 * @date:    31 Dec 2012
 */
class FsCurl
{

	private $url;
	private $curl;
	private $data;

	function getFsApiUrl()
	{
		return FS_WIKI_FS_API_URL;
	}

	function getMwApiUrl()
	{
		return FS_WIKI_MW_API_URL;
	}

	function getIndexUrl()
	{
		return FS_WIKI_INDEX_URL;
	}

	function get($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);
		curl_close($ch);

		if ($curl_errno > 0) {
			error_log("cURL Error ($curl_errno): $curl_error\n");
		}
		return $data;
	}

	function post($url, $values)
	{

	}

}