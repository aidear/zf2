<?php
/*
 * Created on Mar 3, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace Custom\Sponsor\Google;

use Custom\Util\CURL;
use Custom\Sponsor\Ads\CommonAds;

class GoogleTagDao {
	private static $defaultClientID = "dahongbao-cn";
	private static $defaultTag = "Search";
	private static $defaultSiteID = "38";
	private static $defaultCountry = "";
	private static $defaultMatch = "1";
	private static $defaultMatchType = "Default";
	private static $kwTagParams = array();
	private static $defaultExpireTime = 0;
	
	/**
	 * 注意: 返回值从self::$kwTagParams[$keyword]['channelTag'] 改为 self::$kwTagParams[$keyword]
	 * 日期：2011-02-25
	 */
	public static function findtag($keyword, $source, $referer) {
		if(!defined("TAG_SERVICE_BASE_URL")) {
			//logError("undefine TAG_SERVICE_BASE_URL");
			return self::$defaultTag;
		}
		/*
		$requestKeyword = urlencode(iconv("GBK", "UTF-8//IGNORE", $keyword));
		$source = urlencode(iconv("GBK", "UTF-8//IGNORE", $source));
		*/
		$requestKeyword = urlencode($keyword);
		$source = urlencode($source);
		$url = TAG_SERVICE_BASE_URL 
			.'/findgtag/?siteid='.self::$defaultSiteID
			.'&kw='.$requestKeyword
			.'&source='.$source
			.'&country='.self::$defaultCountry
			.'&ref='.urlencode($referer);
		$curl = new CURL();
		$curl->setTimeOut(1);
		$content = $curl->get_contents($url);
		$gstsObject = simplexml_load_string($content);
		$tagArray = get_object_vars($gstsObject->tag);

		if(isset($_COOKIE['DAHONGBAO_TEST']) && $_COOKIE['DAHONGBAO_TEST'] == "YES") {
			echo "<PRE style='background:#fff;'>\n\n";
			echo  $url. "\n\n";
			echo htmlspecialchars($content);
			echo "\n</PRE>\n";
		}
		//设置channel tag params 默认值
		self::$kwTagParams[$keyword]["clientID"] = $tagArray["client-id"] ? 
			trim($tagArray["client-id"]) : $defaultClientID;
		self::$kwTagParams[$keyword]["channelTag"] = $tagArray["channel-tag"] ? 
			trim($tagArray["channel-tag"]) : self::$defaultTag;
		self::$kwTagParams[$keyword]["isMatched"] =  isset($tagArray["is-matched"]) ? 
			trim($tagArray["is-matched"]) : self::$defaultMatch;
		self::$kwTagParams[$keyword]["matchType"] =  $tagArray["match-type"] ? 
			trim($tagArray["match-type"]) : self::$defaultMatchType;
		self::$kwTagParams[$keyword]["expireTime"] = $tagArray["expire-time"] ? 
			trim($tagArray["expire-time"]) : self::$defaultExpireTime;
		self::$kwTagParams[$keyword]["country"] = $tagArray["country"] ? 
			trim($tagArray["country"]) : self::$defaultCountry;
		self::$kwTagParams[$keyword]['channelTagForTrack'] = 
			self::$kwTagParams[$keyword]["clientID"]."|".self::$kwTagParams[$keyword]["channelTag"];
		
		if(empty($gstsObject)) {
			//logError("GOOGLE_TAG_SERVICE: fetch empty. maybe occurred timeout."); 
		}
		else if (empty($tagArray["channel-tag"])) {
			//logError("GOOGLE_TAG_SERVICE: can't fount channel tag. content:\n'".$content);
		}
		return self::$kwTagParams[$keyword];
	}
	
	public static function getTagParams($keyword) {
		return self::$kwTagParams[$keyword];
	}
}
?>