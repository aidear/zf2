<?php
/*
 * Created on Mar 3, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace Custom\Sponsor\Sogou;

use Custom\Util\CURL;
use Custom\Sponsor\Ads\CommonAds;
use SimpleXMLElement;

class SogouTagDao {
    const SOGOU_TAG_URL = '/findsogoutag/?siteid=%d&kw=%s&source=%s&country=%s&ref=%s';
    const TIMEOUT = 2;
    const TAG = 'smarter8';
    const SITEID = 38;
    const COUNTRY = 'cn';
    const EXPIRETIME = 0;
    const MATCHTYPE = 'SEMDefault';
    const MATCH = 1;
    
    private static $tagParams = array();
    public static function findtag($keyword, $source, $referer)
    {
        $url = sprintf(__SOGOU_TAG_SERVICE . self::SOGOU_TAG_URL , self::SITEID , urlencode($keyword) , $source 
            , self::COUNTRY , urlencode($referer));
        $curl = CURL::getInstance();
        $curl->setTimeout(self::TIMEOUT);
        $str = $curl->get_contents($url);
        if(isset($_COOKIE['DAHONGBAO_TEST']) && $_COOKIE['DAHONGBAO_TEST'] == "YES") {
            //logWarn("S-STS request url: {$url}, \n fetch content: {$str}\n\n");
            echo "S-STS request url: {$url}, <br /> fetch content: {$str}<br />";
        }

        if(! $str){
            //logError("YOUDAO_TAG_SERVICE: fetch empty. maybe occurred timeout.");
            return self::TAG;
        }

        $xml = new SimpleXMLElement($str);

        if(isset($xml->tag->{'billing-id'})){
            self::$tagParams[$keyword]['matchType'] = isset($xml->tag->{'match-type'}) ? 
                (string)$xml->tag->{'match-type'} : self::MATCHTYPE;
            self::$tagParams[$keyword]["expireTime"] = isset($xml->tag->{'expire-time'}) ? 
                (string)$xml->tag->{'expire-time'} : self::EXPIRETIME;
            self::$tagParams[$keyword]['isMatched'] = self::MATCH;
            self::$tagParams[$keyword]['country'] = self::COUNTRY;
            return (string)$xml->tag->{'billing-id'};
        }else{
            //logError("YOUDAO_TAG_SERVICE: can't fount channel tag. content:\n'" . $str);
        }
    }

    public static function getTagParams($keyword)
    {
        return self::$tagParams[$keyword];
    }
}
?>