<?php
/*
 * Created on 2010-10-18
 * 
 * 得到SPL内容
 * 
 * @author     Thomas_FU
 * @copyright  (C) 2004-2006 Mezimedia.com
 * @license    http://www.mezimedia.com  PHP License 5.0
 * @version    CVS: $Id: BaiduAds.php,v 1.1 2013/04/15 10:56:30 rock Exp $
 * @link       http://www.smarter.com/
 * @deprecated File deprecated in Release 2.0.0
 */
namespace Custom\Sponsor\Ads;

use Custom\Util\Utilities;
use Custom\Sponsor\Ads\CommonAds;
use Custom\Util\TrackingFE;


class BaiduAds extends CommonAds {
	protected $baiduDomain = "http://www.baidu.com/s";
	protected $tn = 'smarter021_pg';	//默认百度计费账号
	protected $ch = '1';	//默认的渠道名
	protected $channelTag = '';
	protected $adsTypeMap = array(
		"advertizing-link"      => \Tracking_Constant::SPONSOR_BAIDU_PROMOTION,
		"accurate-matching"     => \Tracking_Constant::SPONSOR_BAIDU_ACCURATE,
		"intelligent-matching"  => \Tracking_Constant::SPONSOR_BAIDU_INTELLIGENT
	);
	protected $totalCount = 0; //百度返回的广告数量
    protected $DisplaySingleTemplate = array();   //显示广告模板样式
	
	public function initParams($params) {
		if (parent::initParams($params) == false) {
			return false;
		}
		$this->channelTag = self::getSemTag($this->keyword);
		if (strpos($this->channelTag, "smarter") !== false) {
			list($this->tn, $this->ch) = explode("|", $this->channelTag);
		} else {
			logError("Baidu Channel Tag return error.{$this->channelTag}");
		}
		if (defined("__LOG_LEVEL") && __LOG_LEVEL <= 1
			&& (Utilities::onlineIP(false) == '127.0.0.1'
					|| strpos(Utilities::onlineIP(false), '192.168.') === 0)) {
			$this->tn = "baidu_xmltest_3";	//测试账号
		}
        if ($params["DisplaySingleTemplate"]) {
            $this->DisplaySingleTemplate = $params["DisplaySingleTemplate"];
        }
		return true;
	}
	/**
	 * 得到百度广告内容
	 * @return array $adsArr
	 */
	public function  getAdsContent() {
		$startTime = Utilities::getMicrotime();
		//1.得到请求百度广告的URL
		$reqUrl = $this->makeRequestUrl();
		//2.得到百度广告内容
		$adsArr = $this->parseAds($reqUrl);
		//3.格式化广告显示的内容
		$adsArr = $this->formatToView($adsArr);
		//4.tracking Sponsor Transfer数据记录
		$costTime = Utilities::getMicrotime() - $startTime;
		TrackingFE::registerSponsorTransfer(
			$this->keyword,
			$costTime,
			$this->totalCount,
			'',//showCount
			$this->chid,
			'',//masterKeyword
			$this->channelTag,
			$this->requestAdsCount,
			\Tracking_Constant::SPONSOR_BAIDU
		);
		//5.打印信息
		if (getRequestValue('SMARTER_TEST') == "YES") {
			logWarn("Test outer: ".$reqUrl);
			echo $reqUrl . "\n<BR><BR><PRE>\n";
			print_r($adsArr);
			echo "\n\n</PRE>\n";
		}
		return $adsArr;
	}
	
	/**
	 * 生成HTML
	 * @param  array $adsGroupArr 广告数组结果
	 * @return  string 广告结果
	 */
	public function renderTemplate(&$adsGroupArr) {
		if (empty($adsGroupArr)) {
			return false;
		}
		$strHtml = '';
		$convertedKeyword = htmlspecialchars($this->keyword);
		for ($loop = 0; $loop <= count($adsGroupArr); $loop++) {
			if ($adsGroupArr[$loop]) {
				$position = $loop + 1;
				$strHtml .= " var _ads_baidu_{$position} = '<div class=\"favBg\">".
					"<div class=\"favTl\"><div class=\"favTr\"></div></div>". 
					"<div class=\"favM\"><div class=\"favTop\">";
				if ($this->IsDisplayKeywordArr[$loop] === true) {
					$strHtml .= "<div class=\"search_keyword\">欢迎到这里选购 ".
						"<strong>{$convertedKeyword}</strong> 相关产品</div>";
				} else if ($this->IsDisplayKeywordArr[$loop]) {
					//显示特殊值
				    $decoratedKeyword = str_replace('{KEYWORD}', $convertedKeyword, $this->IsDisplayKeywordArr[$loop]);
					$strHtml .= "<div class=\"search_keyword\">".$decoratedKeyword."</div>";
				}
				$strHtml .= "<div class=\"google_four\">";
                if ($this->DisplaySingleTemplate[$loop]) {
                    foreach ($adsGroupArr[$loop] as $ads) {
                        $strHtml .= "<ul onMouseOver=\"return glb_baidu_ss(\'{$ads['url']}\', this)\" onMouseOut=\"glb_baidu_cs(this)\">".
                                "<li><a href=\"{$ads['url']}\" target=\"_blank\">".
                                "<span class=\"b_title\">{$ads['title']}</span>".
                                "<span class=\"b_text\">{$ads['abstract']}</span>".
                                "<span class=\"b_url\">{$ads['site']}</span></a></li>".
                                "</ul>";
                    }
                } else {
    				foreach ($adsGroupArr[$loop] as $ads) {
    					$strHtml .= "<ul onMouseOver=\"return glb_baidu_ss(\'{$ads['url']}\', this)\" onMouseOut=\"glb_baidu_cs(this)\">".
    						"<li class=\"li_title\"><a href=\"{$ads['url']}\" target=\"_blank\">".
    						"{$ads['title']}</a></li>".
    						"<li class=\"li_text\"><a href=\"{$ads['url']}\" target=\"_blank\">{$ads['abstract']} </a></li>".
    						"<li class=\"li_url\"><a href=\"{$ads['url']}\" target=\"_blank\">".
    						"{$ads['site']}</a></li>".
    						"</ul>";
    				}
                }
				$strHtml .= "</div></div></div>".
					"<div class=\"favBl\"><div class=\"favBr\"></div></div>".
					"</div>';";
				$strHtml .= "\r\n";
			}
		}
		return $strHtml;
	}
	
	/**
	 * 生成请求Baidu URL,JS再次调用
	 */
	public function getRequestUrl() {
		if (defined("__MEZI_ADS")) {
			$adsURL = __MEZI_ADS;
		} 
		else {
			$adsURL = "http://www.smarter.com.cn/async_mezi_ads.php";
		}
		$this->oldParamArr['adsName'] = "baidu";
		$adsURL .= "?params=";
		$adsURL .= Utilities::encode(serialize($this->oldParamArr));
		return $adsURL;
	}

	/**
	 * 得到请求的URL
	 */
	public function makeRequestUrl() {
		$gbkWord = urlencode(iconv("UTF-8", "GBK", $this->keyword));
		$clientIP = Utilities::onlineIP(false);//得到客户端请求的IP
		$url = $this->baiduDomain."?wd=".$gbkWord."&tn=".$this->tn."&ch=".$this->ch."&ip=".$clientIP;
		return $url;
	}
	
	public function parseAds($reqUrl) {	
		$curl = CURL::getInstance();
		$curl->setTimeout($this->timeout);
		$str = $curl->get_contents($reqUrl);
		if (empty($str)) {
			logWarn("Baidu ads XML Result Is Empty (URL={$reqUrl}\nContent:\n{$str}\n).");
			return array();
		}
		$strCharPos = stripos($str, "?>");
		$utf8Str = '<?xml version="1.0" encoding="UTF-8"'. substr($str, $strCharPos);
		$utf8Str = iconv("GBK", __CHARSET."//IGNORE", $utf8Str);
		$dom = new \DomDocument();
		$dom->loadXML($utf8Str, LIBXML_NOCDATA);
		if (is_object($dom->documentElement) == false) {
			//注:只能记正式环境中的XML文件内容
			logWarn("Baidu ads XML error(URL={$reqUrl}\nContent:\n{$utf8Str}\n).");
			return array();
		}
		$arr = array();
		$adsCount = 0;
		$adResults = $dom->documentElement->getElementsByTagName("adresults")->item(0);
		$resultSets = $adResults->getElementsByTagName('resultset');
		foreach ($resultSets as $resultSet) {
			$adsResults = $resultSet->getElementsByTagName('result');
			//广告类型
			$adsType = trim($resultSet->getAttributeNode('type')->value);
			//百度返回广告数量
			$this->totalCount += intval($resultSet->getAttributeNode('numResults')->value);	
			foreach ($adsResults as $adsResult) {
				if ($adsCount >= $this->requestAdsCount) {
					break;
				}
				$tmpArr['rank'] = $adsResult->getAttributeNode('rank')->value;
				$tmpArr['sponsorType'] = $this->adsTypeMap[$adsType];
				$lines = $adsResult->childNodes;
				foreach ($lines as $line) {
					if ($line->nodeName == "#text") {
						continue;
					}
					if ($line->nodeName == "title") {
						$tmpArr['title'] = $this->formatAds($line->nodeValue);
					}
					if ($line->nodeName == "url") {
						$tmpArr['url'] = $this->formatAds($line->nodeValue);
					}
					if ($line->nodeName == "abstract") {
						$tmpArr['abstract'] = $this->formatAds($line->nodeValue);
					}
					if ($line->nodeName == "site") {
						$tmpArr['site'] = $this->formatAds($line->nodeValue);
					}
				}
				$arr[] = $tmpArr;
				$adsCount++;
			}
		}
		return $arr;
	}
	
	//格式化广告显示的内容
	public function formatToView($adsData, $masterKeyword="") {
		if (count($adsData) == 0) {
			return NULL;
		}
		//统计百度三种类型的广告数量
		$sponsorTypeCountArr = array();
		for ($loop=0, $cnt=count($adsData); $loop<$cnt; $loop++) {
			$bidPos = $adsData[$loop]['rank'];
			$adsData[$loop]['abstract'] = Utilities::cutString($adsData[$loop]['abstract'], 105);
			$sponsorTypeCountArr[$adsData[$loop]['sponsorType']]++;
			//SponsorRedir URL
			$adsData[$loop]['url'] = TrackingFE::registerSponsorLink($this->keyword, 
					$bidPos,
					$adsData[$loop]['url'],
					$adsData[$loop]['site'],
					array("chid" => $this->chid,
						"sponsorType" => $adsData[$loop]['sponsorType'], 
						"masterKeyword" => $masterKeyword,
						"channelTag" => $this->channelTag)
					);
		}
		//记录sponsor Impression
		foreach ($sponsorTypeCountArr as $sponsorType => $showCount) {
			TrackingFE::sponsorImpression(
				$this->keyword, 
				$showCount, 
				$this->chid, 
				'', 
				$this->channelTag, 
				$sponsorType
			);
		}
		return $adsData;
	}
	
	/**
	 * 格式化百度广告元素
	 * @param string $adsFieldText
	 * @return string
	 */
	public function formatAds($adsFieldText) {
		if (empty($adsFieldText)) {
			return '';
		}
		$searchWords = array('<font color=#CC0000>', '</font>');
		$replaceWords = array('<strong>', '</strong>');
		$adsFieldText = str_ireplace($searchWords, $replaceWords, $adsFieldText);
		return trim(addslashes($adsFieldText));
	}

	
	/**
	 * 返回Channel Tag 根据tn, ch
	 * @return string
	 */
	public function getSemTag($keyword) {
		$source = TrackingFE::getSource();
		$referer = TrackingFE::getReferer();
		$tag = BaiduTagDao::findtag($keyword, $source, $referer);
		return $tag;
	}
}

