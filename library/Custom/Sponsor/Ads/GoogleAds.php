<?php
/*
 * package_name : GoogleAds.php
 * ------------------
 * 共同函数
 *
 * PHP versions 5
 * 
 * @Author   : richie(rizhang@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: GoogleAds.php,v 1.6 2013/06/18 02:34:05 rizhang Exp $
 */

namespace Custom\Sponsor\Ads;

use Custom\Util\Utilities;
use Custom\Sponsor\Ads\CommonAds;
use Custom\Sponsor\Google\GoogleAdsDao;
use Custom\Sponsor\Google\GoogleDNSDao;
use Custom\Sponsor\Google\GoogleTagDao;
use Custom\Util\TrackingFE;

class GoogleAds extends CommonAds {
	protected $googleParams = array(
						'adtest' => 'off',
						'adsafe' => 'off', //JC 09-05
						'q' => '',
						'channel' => '',
						'ad' => '',
						'ip' => '',
						'useragent' => '',
						'timeout' => '',
					);	//请求google 参数
	protected $channelTag = "";
	protected $DisplaySingleTemplate = array();   //显示广告模板样式
	protected $ShowTitleNumber = false; //显示广告的number
	protected $ShowMoreAds = false; //显示更多您感兴趣的结果
							
	/**
	 * 请求google广告参数设置
	 * $params array $params
	 * @return void
	 */
	public function initParams($params) {
		if (parent::initParams($params) == false) {
			return false;
		}
                
        if($this->sleep > 0){
            sleep($this->sleep);
        }
        //YODAO 无效的流量不请求GOOGLE广告
        $sourceFlag = trim(strtoupper(TrackingFE::getSourceGroup()));
        if ($sourceFlag == "YODAO" && TrackingFE::getTrafficType() == 20) {
            return '';
        }
		//google ads 全局参数设置
		//设置是否要编码
        /*
		if (defined("__LOG_LEVEL") && __LOG_LEVEL == 1) {
			GoogleAdsDao::$useBase16 = false;
		}
		*/
		//强制指定是否采用base16请求广告
		if ($this->googleParams['useBase16'] === "yes") {
			GoogleAdsDao::$useBase16 = true;
		}
		else if ($this->googleParams['useBase16'] === "no") {
			GoogleAdsDao::$useBase16 = false;
		}
		//设置是域名还是IP
		GoogleDNSDao::setDnslookup($this->googleParams['dnslookup']);
		
		//设置google ads 基本参数
		/*
		if(defined("__LOG_LEVEL") && __LOG_LEVEL == 1 && DIRECTORY_SEPARATOR != "/") {
			$this->googleParams['adtest'] = "on";
		} else if (!empty($params['googleAdtest'])) {
			$this->googleParams['adtest'] = $params['googleAdtest'];
		}
		*/

		$this->googleParams['q'] = $this->keyword;
		$tagInfo = $this->getSemTag($this->keyword);
		$this->channelTag = $tagInfo['channelTagForTrack'];
		$this->googleParams['client'] = $tagInfo['clientID'];
		$this->googleParams['channel'] = $tagInfo['channelTag'];
		$this->googleParams['ad'] = "w".$this->requestAdsCount;
		if (empty($this->googleParams['ip'])) {
			$this->googleParams['ip'] = Utilities::getClientIPForGG();
		}
		if (empty($this->googleParams['useragent'])) {
			$this->googleParams['useragent'] = Utilities::onlineUserAgent();
		}
		if ($params["DisplaySingleTemplate"]) {
		    $this->DisplaySingleTemplate = $params["DisplaySingleTemplate"];
		}
		if ($params["ShowTitleNumber"]) {
		    $this->ShowTitleNumber = $params["ShowTitleNumber"];
		}
		if ($params["ShowMoreAds"]) {
		    $this->ShowMoreAds = $params["ShowMoreAds"];
		}
		$this->googleParams['timeout'] = $this->timeout;
		return true;
	}
	
	public function getParams() {
		return $this->googleParams;
	}
	
	/**
	 * 请求google ads 内容
	 *
	 * @return array
	 */
	public function getAdsContent() {
		$params = $this->getParams();
		$ads = new GoogleAdsDao();
		$adsNum = substr($params['ad'], 1);
		$startTime = Utilities::getMicrotime();
		$ret = $ads->getGoogleAds($params);
		$rank = $ret ? count($ret) : 0;
		$costTime = Utilities::getMicrotime() - $startTime;
		//request google ads is timeout
		if($ret === false) {
			$ret = array();
		}
		$adsData = $ads->formatToView($ret, $this->keyword, $this->chid, $this->channelTag);
		$showCount = count($adsData);
		//保存ads count
		$this->adsCount = count($adsData);
		TrackingFE::registerSponsorTransfer(
			$this->keyword,
			$costTime,
			$rank,
			$showCount,
			$this->chid,
			'',
			$this->channelTag,
			$adsNum
		);
		return $adsData;
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
				$strHtml .= " var _ads_{$position} = '<div class=\"favBg\">".
					"<div class=\"favTl\"><div class=\"favTr\"></div></div>". 
					"<div class=\"favM\"><div class=\"favTop\">";
				if ($this->IsDisplayKeywordArr[$loop] === true) {
					$strHtml .= "<div class=\"search_keyword\">欢迎到这里选购 ".
						"<strong>{$convertedKeyword}</strong> 相关产品</div>";
				} else if ($this->IsDisplayKeywordArr[$loop]) {
				    $decoratedKeyword = str_replace('{KEYWORD}', $convertedKeyword, $this->IsDisplayKeywordArr[$loop]);
					$strHtml .= "<div class=\"search_keyword\">".$decoratedKeyword."</div>";
				}
				$strHtml .= "<div class=\"google_four\">";
				if ($this->DisplaySingleTemplate[$loop]) {
				    foreach ($adsGroupArr[$loop] as $ads) {
				        $strHtml .= "<a href=\"{$ads['url']}\" target=\"_blank\">".
				                "<ul onMouseOver=\"return glb_ss(\'{$ads['visible_url']}\')\" onMouseOut=\"glb_cs()\">".
				                "<li class=\"li_title\">{$ads['LINE1']}</li>".
				                "<li class=\"li_text\">{$ads['LINE2']}</li>".
				                "<li class=\"li_url\">{$ads['SiteUrl']}</li></ul></a>";
				    }
				} elseif ($this->ShowTitleNumber) {
				    foreach ($adsGroupArr[$loop] as $k => $ads) {
				        $className = "";
				        if ($k == count($adsGroupArr[$loop]) -1) {
				            $className = 'class="last"';
				        }
				        $strHtml .= "<ul onMouseOver=\"return glb_ss(\'{$ads['visible_url']}\')\" onMouseOut=\"glb_cs()\" ".$className.">".
    						"<li class=\"orderid\">{$ads['n']}</li>";
				        $adsCnt = $this->getAdsCnt();
				        $lastLoop = count($adsGroupArr) - 1;
				        $moreUrl = "/search.php?q=".urlencode($this->keyword)."&charset=utf8&more=yes";
				        if ($this->ShowMoreAds && $adsCnt <= 4 && $k == count($adsGroupArr[$lastLoop]) -1 && $loop == $lastLoop) {
				            //返回条数小<=4条，则只在最后一条广告处显示连接
				            $strHtml .="<li class=\"seemore\"><a href=\"".$moreUrl."\"><b></b>更多您感兴趣的结果</a></li>";
				        } elseif($this->ShowMoreAds && $adsCnt > 4 && (($k == 3 && $loop == 0) || ($k == count($adsGroupArr[$lastLoop]) -1) && $loop == $lastLoop && $lastLoop > 0)) {
				            //第4条广告和最后一条广告处显示连接
				            $strHtml .="<li class=\"seemore\"><a href=\"".$moreUrl."\"><b></b>更多您感兴趣的结果</a></li>";
				        }
				        $strHtml .="<li class=\"li_title\"><a href=\"{$ads['url']}\" target=\"_blank\">".
    						"{$ads['LINE1']}</a></li>".
    						"<li class=\"li_text\">{$ads['LINE2']} </li>".
    						"<li class=\"li_url\"><a href=\"{$ads['url']}\" target=\"_blank\">".
    						"{$ads['SiteUrl']}</a></li>".
    						"</ul>";
				    }
				} else {
    				foreach ($adsGroupArr[$loop] as $ads) {
    					$strHtml .= "<ul onMouseOver=\"return glb_ss(\'{$ads['visible_url']}\')\" onMouseOut=\"glb_cs()\">".
    						"<li class=\"li_title\"><a href=\"{$ads['url']}\" target=\"_blank\">".
    						"{$ads['LINE1']}</a></li>".
    						"<li class=\"li_text\">{$ads['LINE2']} </li>".
    						"<li class=\"li_url\"><a href=\"{$ads['url']}\" target=\"_blank\">".
    						"{$ads['SiteUrl']}</a></li>".
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
	 * 消重两次请求的广告
	 * @param array $ret2 需要消重的广告
	 * @param array $ret 已有广告
	 * @return 消重后的广告数组
	 */
	protected function filterExistsAd($ret2, &$ret) {
		$existsAdUrl = array();
		foreach($ret as $index => $r) {
			$existsAdUrl[$r['visible_url']] = true;
		}
		$tmp_ret2 = array();
		foreach($ret2 as $index => $r2) {
			if(! isset($existsAdUrl[$r2['visible_url']])) {
				$tmp_ret2[] = $r2;
			}
		}
		return $tmp_ret2;
	}
	
	/**
	 * 生成请求Google URL,JS再次调用
	 */
	public function getRequestUrl() {
		/*
		if (defined("__MEZI_ADS")) {
			$adsURL = __MEZI_ADS;
		} 
		else {
			$adsURL = "http://www.smarter.com.cn/async_mezi_ads.php";
		}
		*/
	    $adsURL = __MEZI_ADS;
		$adsURL .= "?params=";
		$dnsLookup = $this->googleParams['dnslookup'];
		if ($dnsLookup) {
			$this->oldParamArr['dnslookup'] = $dnsLookup;
		}
		$this->oldParamArr['pageTypeID'] = CommonAds::getPageTypeID();
		$adsURL .= Utilities::encode(serialize($this->oldParamArr));
		return $adsURL;
	}
	
	/**
	 * 返回Channel Tag 根据Keyword
	 * @param string $keyword
	 * @return string
	 */
	public function getSemTag($keyword) {
		$source = TrackingFE::getSource();
		$referer = TrackingFE::getReferer();
		$tag = GoogleTagDao::findtag($keyword, $source, $referer);
		return $tag;
	}
}

