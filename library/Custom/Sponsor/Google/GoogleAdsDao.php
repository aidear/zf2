<?php
/**
 * class.GoogleAds.php
 *-------------------------
 *
 * This file include Book product classes' definetions.
 * It will be include in PHP files, and create a respected instance.
 *
 * PHP versions 5
 *
 * LICENSE: This source file is from Smarter Ver2.0, which is a comprehensive shopping engine
 * that helps consumers to make smarter buying decisions online. We empower consumers to compare
 * the attributes of over one million products in the Book and consumer electronics categories
 * and to read user product reviews in order to make informed purchase decisions. Consumers can then
 * research the latest promotional and pricing information on products listed at a wide selection of
 * online merchants, and read user reviews on those merchants.
 * The copyrights is reserved by http://www.mezimedia.com.
 * Copyright (c) 2005, Mezimedia. All rights reserved.
 *
 * @author     Kevin <Kevin@mezimedia.com>
 * @copyright  (C) 2004-2005 Mezimedia.com
 * @license    http://www.mezimedia.com  PHP License 5.0
 * @version    CVS: $Id: GoogleAdsDao.php,v 1.7 2013/07/01 08:41:47 rizhang Exp $
 * @link       http://www.smarter.com/
 * @deprecated File deprecated in Release 2.0.0
 */
namespace Custom\Sponsor\Google;

use Custom\Util\CURL;
use DomDocument;
use Custom\Util\TrackingFE;
use Custom\Util\Utilities;

class GoogleAdsDao {
	    
	public $googleDomain = "http://www.google.com";
    public $googleParams = array();
    public $breakMarking = "<br>";
    public static $useBase16 = true;
   
    public function  __construct(){
			$this->googleParams = array(
			 	"client" => "dahongbao-cn",
				"q" => "",
				"ip" => "",
				"ad" => "w10",
				"output" => "xml_no_dtd",
				"num" => 0,
				"channel" => "", //modify by fan 061221
				
				"adpage" => "",
				"adsafe" => "",
				"adtest" => "",
				"gl" => "cn",
				"hl" => "zh-CN",
				"ie" => "utf-8",
				"oe" => "utf-8",
					
				"useragent" => ""
			);
    }
    
    public function  __destruct(){		
    }
    
    
    /**
     * 获取Google广告
     *
     * @param array $params
     * @return array,false,null 正常返回array，没有广告返回null，超时放回false
     */
    public function getGoogleAds($params) {
		if ($params["ad"]) {
			$count = substr($params["ad"], 1);
//			$params["ad"] = substr($params["ad"], 0, 1) . ($count + 1);
		}
    	//assign parameters
    	reset($this->googleParams);
    	while(list($k,$v)=each($params)){
    		if(isset($this->googleParams[$k])){
    			$this->googleParams[$k] = $v;
    		}
    	}
    	$this->googleParams['q'] = urlencode(trim($params['q']));
    	$this->googleParams['useragent'] = urlencode(trim($params['useragent']));
    	//关键字必须不为空
    	if($this->googleParams['q'] == "") {
    		return false;
    	}
    	
    	//make up request url and get the xml doc
    	$reqUrl = $this->makeRequestUrl();
		//echo "$reqUrl<BR>";
	    if($reqUrl != ""){
	    	if(isset($params['timeout']) && ($params['timeout'] > 0 && $params['timeout'] < 30)) {
	    		$timeout = intval($params['timeout']);
	    	} else {
	    		$timeout = 10;
	    	}
	    	$ADSArr = $this->parseGoogleAds($reqUrl, $timeout);
	    	if($ADSArr === false) {
	    		return false;
	    	}
	    	/*logs for tracking goes here */
	    	$b64eKey = base64_encode($params['q']);
	    	//以下5行代码没有任何作用，是否是BUG？
	    	foreach($ADSArr as $v) {
	    		$tmpUrl = __REDIR_URL."sponsorRedir.php?statDestUrl=".
	    			base64_encode($v['url'])."&statBidPosition=".$v['n']."&searchTerm=".$b64eKey;
	    		$v['url'] = $tmpUrl ;
	    	}
	    	
	    	$strLog = $params['q']."|" . sizeof($ADSArr) . "|" . getenv("REMOTE_ADDR");
				$para = array('sessionID'=>0, 'keyword'=>$strLog,
					'sponsorType' => 1, 'channelID'=> $nChannelID);
			//TEST
			if(isset($_COOKIE['DAHONGBAO_TEST']) && $_COOKIE['DAHONGBAO_TEST'] == "YES") {
				echo "<PRE style='background:#fff;'>\n\n";
				echo $reqUrl . "\n\n";
				print_r($ADSArr);
				echo "\n</PRE>\n";
			}
		}
		if (isset($count)) {
			$ADSArrTmp = $ADSArr;
			$ADSArr = NULL;
			$currentNum = 0;
			for($loop=0, $cnt = count($ADSArrTmp); $loop<$cnt; $loop++) {
				if (strpos($ADSArrTmp[$loop]["visible_url"], "smarter.com") !== false) {
					continue;
				}
				$ADSArr[$currentNum] = $ADSArrTmp[$loop];
				$currentNum++;
				if ($currentNum >= $count) {
					break;
				}
			}
		}
		return $ADSArr;       
    }
    
    public function formatToView($adsData, $keyword, $chid, $channelTag='', $baseBidPos=0, $absRedirUrl=false, $masterKeyword="") {
    	if(count($adsData) == 0) {
    		return NULL;
    	}
    	for($loop=0,$cnt=count($adsData); $loop<$cnt; $loop++) {
			
	    	$baseBidPos++;
	    	$bidPos = $adsData[$loop]['n'];
    		$adsData[$loop]['LINE1'] = addslashes($adsData[$loop]['LINE1']);
    		$adsData[$loop]['LINE2'] = addslashes($adsData[$loop]['LINE2']);
    		$adsData[$loop]['SiteUrl'] = addslashes($adsData[$loop]['visible_url']);
    		if(!empty($adsData[$loop]['LINE3'])) {
    			$adsData[$loop]['LINE3'] = addslashes($adsData[$loop]['LINE3']);
    			$adsData[$loop]['SiteUrl'] = Utilities::cutString(
    									$adsData[$loop]['SiteUrl'], 17);
    		}
    		$adsData[$loop]['OriginalUrl'] = $adsData[$loop]['url'];
    		//SponsorRedir URL
    		//modify by Fan(20100305): $baseBidPos => $bidPos
    		$adsData[$loop]['url'] = TrackingFE::registerSponsorLink($keyword, 
				                                                     $bidPos,
				                                                     $adsData[$loop]['url'],
				                                                     $adsData[$loop]['visible_url'],
				                                                     array("chid"          =>$chid, 
				                                                           "masterKeyword" =>$masterKeyword,
				                                                           "channelTag"    =>$channelTag));
    		if($absRedirUrl) {
    			$adsData[$loop]['url'] = __WEB_DOMAIN_NAME . substr($adsData[$loop]['url'], 1);
    		}
    	}
    	return $adsData;
    }
    
    
    /*===== Util functions=====*/
    function makeRequestUrl(){
    	$url = $this->googleDomain."/search";
    	//base16
    	if(self::$useBase16) {
	    	$this->googleParams['q'] = bin2hex($this->googleParams['q']);
//	    	$this->googleParams['useragent'] = bin2hex($this->googleParams['useragent']);
//			foreach($this->googleParams as $key => $val) {
//				$this->googleParams[$key] = bin2hex($this->googleParams[$key]);
//			}
    	}
    	
    $paramstr = "";
    
    	while(list($k,$v)=each($this->googleParams)){
    		if(trim($v)==""){
    			continue;
    		}else{
    			$paramstr .= "&".$k."=".$v;
    		}
    	}
    	if($paramstr==""){
    		return "";
    	}else{
    		$url .= "?".substr($paramstr,1);
    		if(self::$useBase16) {
	    		$url .= "&base16=2";
    		}
    		return $url;
    	}
    }

    function parseGoogleAds($xmlUrl, $timeout=30){
		$curl = CURL::getInstance();
		$curl->setTimeout($timeout);
		if(GoogleDNSDao::getDnslookup() == "IP"
			&& ($googleip=GoogleDNSDao::getGoogleIP()) != "") {
			$xmlUrl = "http://{$googleip}".substr($xmlUrl, strlen($this->googleDomain));
			$curl->setOption(CURLOPT_HTTPHEADER, array("Host: www.google.com"));
			/*
			logInfo($xmlUrl);
			*/
		}

		//如果是内部 ORIGINAL 请求，输出请求的结果，供后续记录
		/*
		if(getRequestValue('SMARTER_TEST') == "ORIGINAL"
			 &&	(Utilities::onlineIP() == '127.0.0.1' || strpos(Utilities::onlineIP(), '192.168.') === 0)
			 ) {
			//可能需要Log CURL的所有内容，故将$curl->get_contents函数内容移出来
			$curl->setHeaderEnable(false);
			$info = $curl->get($xmlUrl);
			if(200 <= $info['http_code'] && $info['http_code'] < 300) {
				$str = $info['data'];
			}
			else {
				$str = false;
			}
			
			$urlinfo = parse_url($info['url']);
			$info['ip'] = gethostbyname($urlinfo['host']);
			$info['dnslookup'] = GoogleDNSDao::getDnslookup();
			echo "<!--[GOOGLELOG] ";
			echo htmlentities(serialize($info));
			echo " [GOOGLELOG]-->";
			
		}
		else {
			$str = $curl->get_contents($xmlUrl);
		}
		*/
		$str = $curl->get_contents($xmlUrl);
		
		if($str === false) {
			return false;
		}
    	if(empty($str)) {
    		return array();
		}
    	if(self::$useBase16) {
    		$str = pack("H*",$str);
    	}
    	//use dom to parse the xml doc
    	$dom = new DomDocument();
	    $dom->loadXML($str);
    	//add by Fan(2007-08-13): Google XML maybe cann't parsed.
    	if(is_object($dom->documentElement) == false) {
    		//注:只能记正式环境中的XML文件内容
    		//logWarn("Google ads XML error(URL={$xmlUrl}\nContent:\n{$str}\n).");
    		return array();
    	}
    	//end add.
     	$ADS = $dom->documentElement->getElementsByTagName('AD');
     	$arr = array();
     	$sortarr = array();
     	foreach ($ADS as $AD) {
     		if ($AD->nodeName == 'AD') {
     			$tmparr['n'] = $AD->getAttributeNode('n') ->value;
     			$sortarr[] = $tmparr['n'];
     			
     			$tmparr['url'] = $AD->getAttributeNode('url') ->value;
     			$tmparr['visible_url'] = $AD->getAttributeNode('visible_url') ->value;
     			//fetch lines
     			$lines = $AD->childNodes;
     			
     			foreach ($lines as $line) {
     				if ($line->nodeName == 'LINE1') {
     					$tmparr['LINE1'] = trim($line->nodeValue);
     				}
     				if ($line->nodeName == 'LINE2') {
     					$tmparr['LINE2'] = trim($line->nodeValue);
     				}
     				if ($line->nodeName == 'LINE3') {
     					$tmparr['LINE3'] = trim($line->nodeValue);
     				}   
     			}     			
     			$arr[] = $tmparr;
     		}	
     	}
     	
     	//asort by n     	
     	array_multisort($sortarr,SORT_ASC,$arr);
     	reset($arr);
     	return $arr;
    }
    
    function splitKeyword ($strKeyword) {
        $arrKeyword = explode("|", $strKeyword);
        $nCount = sizeof($arrKeyword);
        if ($arrKeyword[$nCount - 1] <> "") {
            $arrtmpKeyword = array_slice($arrKeyword, 0, $nCount-1);
        }else {
            $arrtmpKeyword = array_slice($arrKeyword, 0, $nCount-2);
        }
        return (implode("|", $arrtmpKeyword));
    }

    //get last word of keyword in strKeyword
    function getSingleKeyword($strKeyword) {
        $arrKeyword = explode("|", $strKeyword);
        $nCount = sizeof($arrKeyword);
        if($nCount > 1) {
            return $arrKeyword[$nCount-2];
        }else {
            return $strKeyword;
        }
        
    }
}
?>